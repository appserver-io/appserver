<?php

/**
 * \AppserverIo\Appserver\Core\ApplicationServer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Concurrency\ExecutorService\Core as ExecutorService;

/**
 * This is the main server class that starts the application server
 * and creates a separate thread for each container found in the
 * configuration file.

 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ApplicationServer extends \Thread
{

    /**
     * The available runlevels.
     *
     * @var integer
     */
    const SHUTDOWN       = 0;
    const ADMINISTRATION = 1;
    const DAEMON         = 2;
    const NETWORK        = 3;
    const SECURE         = 4;
    const FULL           = 5;
    const REBOOT         = 6;

    /**
     * String mappings for the runlevels.
     *
     * @var array
     */
    public static $runlevels = array(
        'shutdown'       => ApplicationServer::SHUTDOWN,
        'administration' => ApplicationServer::ADMINISTRATION,
        'daemon'         => ApplicationServer::DAEMON,
        'network'        => ApplicationServer::NETWORK,
        'secure'         => ApplicationServer::SECURE,
        'full'           => ApplicationServer::FULL,
        'reboot'         => ApplicationServer::REBOOT
    );

    /**
     * Initialize and start the application server.
     */
    public function __construct()
    {

        // create a mutex to lock an comman
        $this->mutex = \Mutex::create();

        // set to TRUE, because we swith to runlevel 1 immediately
        $this->switching = true;

        // initialize the members
        $this->childs = ExecutorService::getEntity('childs');
        $this->services = ExecutorService::getEntity('services');
        $this->logger = ExecutorService::getEntity('logger');
        $this->runlevel = ApplicationServer::ADMINISTRATION;

        // by default, we want to log to the STDOUT
        $this->logger->attachLogStream('default', 'php://stdout');
    }

    /**
     * Simple logger method that writes the passed log messages.
     * to a stream.
     *
     * @param string $message The message to log
     *
     * @return void
     */
    protected function log($message)
    {
        $this->logger->log($message);
    }

    /**
     * The runlevel to switch to.
     *
     * @param integer $runlevel The new runlevel to switch to
     *
     * @return void
     */
    public function init($runlevel = ApplicationServer::FULL)
    {

        // lock the server to execute command
        \Mutex::lock($this->mutex);

        // wait till a previos command has been finished
        while ($this->switching === true) {
            sleep(1);
        }

        // lock process
        $this->switching = true;
        $this->runlevel = $runlevel;

        // unlock the server to execute next command
        \Mutex::unlock($this->mutex);
    }

    /**
     * Query whether the application server should keep running or not.
     *
     * @return boolean TRUE if the server should keep running, else FALSE
     */
    public function keepRunning()
    {
        return $this->synchronized(
            function ($self) {
                return $self->runlevel > ApplicationServer::SHUTDOWN;
            },
            $this
        );
    }

    /**
     * Shutdown handler that checks for fatal/user errors.
     *
     * @return void
     */
    public function shutdown()
    {
        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize type + message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                $this->log($message);
            }
        }
    }

    /**
     * The thread's run() method that runs asynchronously.
     *
     * @link http://www.php.net/manual/en/thread.run.php
     */
    public function run()
    {

        // register a shutdown handler for controlled shutdown
        register_shutdown_function(array(&$this, 'shutdown'));

        // we need the autloader again
        require SERVER_AUTOLOADER;

        // flag to keep the server running or to stop it
        $keepRunning = true;

        // initialize the runlevels
        $newRunlevel = 0;
        $actualRunlevel = 0;

        do {
            try {
                // check if the actual runlevel === the requested one
                if ($actualRunlevel == $this->runlevel) {
                    // print a message and wait
                    $this->log("Now start waiting in runlevel $actualRunlevel!!!");

                    // singal that we've finished switching the runlevels and wait
                    $this->switching = false;
                    sleep(1);
                }

                // if the actual runlevel is lower, raise the new runlevel by one
                if ($actualRunlevel < $this->runlevel) {
                    $newRunlevel = $actualRunlevel + 1;
                }

                // if the actual runlevel is higher, lower the new runlevel by one
                if ($actualRunlevel > $this->runlevel) {
                    $newRunlevel = $actualRunlevel - 1;
                }

                // if the actual runlevel differs from the requested one, switch it
                if ($actualRunlevel <> $this->runlevel) {
                    $keepRunning = $this->switchRunlevel($actualRunlevel, $newRunlevel);
                }

                // update the actual runlevel
                $actualRunlevel = $newRunlevel;

            } catch (\Exception $e) {
                $this->log($e->getMessage());
            }

        } while ($keepRunning);
    }

    /**
     * Stops all services of the passed runlevel.
     *
     * @param integer $runlevel The runlevel to stop all services for
     *
     * @return void
     */
    protected function stopAllServicesForRunlevel($runlevel)
    {
        // iterate over all services and stop them
        foreach (array_keys($this->childs->get($runlevel)) as $name) {
            // stop, kill and unset the service instance
            $this->childs->get($runlevel, $name)->stop();
            $this->childs->get($runlevel, $name)->kill();
            $this->childs->del($runlevel, $name);

            /*
             * Onother possibility is to send a closure which is executed entirely in
             * its entity executorservice context

            // invoke closure to run within entity context
            $this->childs->__invoke(function($self) use($runlevel, $name) {
                // $self is our entity instance
                $self->get($runlevel, $name)->stop();
                $self->get($runlevel, $name)->kill();
                $self->del($runlevel, $name);
            });
            */

            // print a message that the service has been stopped
            $this->log("Successfully stopped service $name");
        }
    }

    /**
     * This is main method to switch between the runlevels.
     *
     * @param integer $actualRunlevel The runlevel the application server actual has
     * @param integer $newRunlevel    The new runlevel we want to switch to
     *
     * @return boolean TRUE if the server should keep running, else FALSE
     *
     * @throws \Exception Is thrown if an unknown runlevel has been requested
     */
    protected function switchRunlevel($actualRunlevel, $newRunlevel)
    {

        // print a message with the new runlevel we switch to
        $this->log("Now change runlevel to $newRunlevel");

        // query the new runlevel
        switch ($newRunlevel) {

            case ApplicationServer::SHUTDOWN:

                // kill all processes and unset them from the childs
                foreach (ApplicationServer::$runlevels as $runlevel) {
                    $this->stopAllServicesForRunlevel($runlevel);
                }

                return false;
                break;

            case ApplicationServer::ADMINISTRATION:

                /* Query whether if the requested runlevel is lower or equal than the final one.
                 * This means, a user switched from runlevel 0 to runlevel 1 and we've to start
                 * all services for this runlevel!
                 */
                if ($actualRunlevel < $newRunlevel && $this->runlevel >= $newRunlevel) {
                    // create an instance for each of the management consoles
                    $console = $this->services->get('AppserverIo\Appserver\Core\Console\Telnet', $this);
                    $this->childs->set($newRunlevel, $console, $console->getName());
                    /*
                    $sshConsole = $this->services->get('AppserverIo\Appserver\Core\Console\Ssh', $this);
                    $this->childs->set($newRunlevel, $sshConsole, $sshConsole->getName());
                    */
                }

                /* Query whether if the requested runlevel is higher than the final one.
                 * This means, a user switched from runlevel 3 to runlevel 1 for example
                 * and we've to stop all services of this runlevel!
                 */
                if ($actualRunlevel > $newRunlevel && $this->runlevel < $newRunlevel) {
                    $this->stopAllServicesForRunlevel($newRunlevel);
                }

                return true;
                break;

            case ApplicationServer::DAEMON:

                return true;
                break;

            case ApplicationServer::NETWORK:

                /* Query whether if the requested runlevel is lower or equal than the final one.
                 * This means, a user switched from runlevel 2 to runlevel 3 for example and
                 * we've to start all services for this runlevel!
                 */
                if ($actualRunlevel < $newRunlevel && $this->runlevel >= $newRunlevel) {
                    // create an instance of the HTTP server service
                    $httpServer = $this->services->get('\AppserverIo\Lab\Bootstrap\HttpServer');
                    $this->childs->set($newRunlevel, $httpServer, $httpServer->getName());
                }

                /* Query whether if the requested runlevel is higher than the final one.
                 * This means, a user switched from runlevel 5 to runlevel 3 for example
                 * and we've to stop all services of this runlevel!
                 */
                if ($actualRunlevel > $newRunlevel && $this->runlevel < $newRunlevel) {
                    $this->stopAllServicesForRunlevel($newRunlevel);
                }

                return true;
                break;

            case ApplicationServer::SECURE:

                /* Query whether if the requested runlevel is lower or equal than the final one.
                 * This means, a user switched from runlevel 0 to runlevel 1 and we've to start
                 * all services for this runlevel!
                 */
                if ($actualRunlevel < $newRunlevel && $this->runlevel >= $newRunlevel) {

                    // print a message with the old UID/EUID
                    $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());

                    // switcht the effective UID to _www
                    if (!posix_seteuid(posix_getpwnam('_www')['uid'])) {
                        $this->log("Can't switch UID to '_www'");
                    }

                    // print a message with the new UID/EUID
                    $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());
                }

                /* Query whether if the requested runlevel is higher than the final one.
                 * This means, a user switched from runlevel 3 to runlevel 1 for example
                 * and we've to stop all services of this runlevel!
                 */
                if ($actualRunlevel > $newRunlevel && $this->runlevel < $newRunlevel) {

                    // print a message with the old UID/EUID
                    $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());

                    // switcht the effective UID back to root
                    if (!posix_setuid(posix_getpwnam('root')['uid'])) {
                        $this->log("Can't switch UID back to 'root'");
                    }

                    // print a message with the new UID/EUID
                    $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());
                }

                return true;
                break;

            case ApplicationServer::FULL:

                return true;
                break;

            case ApplicationServer::REBOOT:

                return true;
                break;

            default:
                throw new \Exception("Invalid runlevel $newRunlevel requested");
                break;
        }
    }
}
