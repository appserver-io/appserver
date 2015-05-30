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

use League\Event\Emitter;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Naming\NamingDirectoryInterface;
use AppserverIo\Appserver\Core\Commands\ModeCommand;
use AppserverIo\Appserver\Core\Commands\InitCommand;
use AppserverIo\Appserver\Core\Listeners\ApplicationServerAwareListenerInterface;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;

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
class ApplicationServer extends \Thread implements ApplicationServerInterface
{

    /**
     * String mappings for the runlevels.
     *
     * @var array
     */
    public static $runlevels = array(
        'shutdown'       => ApplicationServerInterface::SHUTDOWN,
        'administration' => ApplicationServerInterface::ADMINISTRATION,
        'daemon'         => ApplicationServerInterface::DAEMON,
        'network'        => ApplicationServerInterface::NETWORK,
        'secure'         => ApplicationServerInterface::SECURE,
        'full'           => ApplicationServerInterface::FULL,
        'reboot'         => ApplicationServerInterface::REBOOT
    );

    /**
     * Temporary array with event => listener mapping (has to be replaced with a XML configuration).
     *
     * @var array
     */
    public static $listeners = array(
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\LoadConfigurationListener'),
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\LoadLoggersListener'),
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\LoadInitialContextListener'),
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\SwitchUmaskListener'),
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\SwitchSetupModeListener'),
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\PrepareFileSystemListener'),
        array('enter.runlevel.shutdown',       'AppserverIo\Appserver\Core\Listeners\CreateSslCertificateListener'),
        array('enter.runlevel.network',        'AppserverIo\Appserver\Core\Listeners\StartContainersListener'),
        array('enter.runlevel.secure',         'AppserverIo\Appserver\Core\Listeners\SwitchUserListener'),
        array('leave.runlevel.secure',         'AppserverIo\Appserver\Core\Listeners\SwitchRootListener'),
        array('enter.runlevel.full',           'AppserverIo\Appserver\Core\Listeners\ExtractArchivesListener'),
        array('enter.runlevel.full',           'AppserverIo\Appserver\Core\Listeners\DeployApplicationsListener'),
        array('enter.runlevel.administration', 'AppserverIo\Appserver\Core\Listeners\StartConsolesListener')
    );

    /**
     * Initialize and start the application server.
     *
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface $configurationFilename The default naming directory
     * @param \AppserverIo\Storage\GenericStackable            $services              The storage for the services
     */
    public function __construct(NamingDirectoryInterface $namingDirectory, GenericStackable $services)
    {

        // set the services and the naming directory
        $this->services = $services;
        $this->namingDirectory = $namingDirectory;

        // initialize the default runlevel
        $this->runlevel = ApplicationServerInterface::ADMINISTRATION;

        // set to TRUE, because we switch to runlevel 1 immediately
        $this->locked = false;

        // initialize command/params
        $this->command = null;
        $this->params = null;
    }

    /**
     * Translates and returns a string representation of the passed runlevel.
     *
     * @param integer $runlevel The runlevel to return the string representation for
     *
     * @return string The string representation for the passed runlevel
     *
     * @throws \Exception Is thrown if the passed runlevel is not available
     */
    public function runlevelToString($runlevel)
    {

        // flip the array with the string => integer runlevel definitions
        $runlevels = array_flip(ApplicationServer::$runlevels);
        if (isset($runlevels[$runlevel])) {
            return $runlevels[$runlevel];
        }

        // throw an exception if the runlevel is unknown
        throw new \Exception(sprintf('Request invalid runlevel to string conversion for %s', $runlevel));
    }

    /**
     * Returns the naming directory instance.
     *
     * @return NamingDirectoryInterface $namingDirectory The default naming directory
     */
    public function getNamingDirectory()
    {
        return $this->namingDirectory;
    }

    /**
     * Sets the system configuration.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration object
     *
     * @return null
     */
    public function setSystemConfiguration(SystemConfigurationInterface $systemConfiguration)
    {
        $this->systemConfiguration = $systemConfiguration;
    }

    /**
     * Returns the system configuration.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->systemConfiguration;
    }

    /**
     * Sets the initial context instance.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function setInitialContext(InitialContext $initialContext)
    {
        $this->initialContext = $initialContext;
    }

    /**
     * Return's the initial context instance.
     *
     * @return \AppserverIo\Appserver\Core\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Set's the logger instances.
     *
     * @param array $loggers The logger instances to set
     */
    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * Returns the logger instances.
     *
     * @return array The logger instances
     */
    public function getLoggers()
    {
        return $this->loggers;
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger()
    {
        return $this->loggers['System'];
    }

    /**
     * Returns the name and the path of the system configuration file.
     *
     * @return string
     */
    public function getConfigurationFilename()
    {
        return $this->getNamingDirectory()->search('php:env/configurationFilename');
    }

    /**
     * Returns a new instance of the passed API service.
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The service instance
     * @see \AppserverIo\Appserver\Core\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * The runlevel to switch to.
     *
     * @param integer $runlevel The new runlevel to switch to
     *
     * @return void
     */
    public function init($runlevel = ApplicationServerInterface::FULL)
    {

        // switch to the new runlevel
        $this->synchronized(function ($self, $newRunlevel) {
            // wait till the previous commands has been finished
            while ($self->locked === true) {
                sleep(1);
            }

            // set the command name
            $self->command = InitCommand::COMMAND;

            // lock process
            $self->locked = true;
            $self->params = $newRunlevel;

            // notify the AS to execute the command
            $self->notify();

        }, $this, $runlevel);
    }

    /**
     * Switch to the passed mode, which can either be 'dev' or 'prod'.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function mode($mode)
    {

        // switch to the new runlevel
        $this->synchronized(function ($self, $newMode) {
            // wait till the previous commands has been finished
            while ($self->locked === true) {
                sleep(1);
            }

            // set the command name
            $self->command = ModeCommand::COMMAND;

            // lock process
            $self->locked = true;
            $self->params = $newMode;

            // notify the AS to execute the command
            $self->notify();

        }, $this, $mode);
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
                return $self->runlevel > ApplicationServerInterface::SHUTDOWN;
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
                error_log($message);
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

        // create the service emitter
        $emitter = new Emitter();

        foreach (ApplicationServer::$listeners as $listener) {
            // extract even name and listener class name
            list ($event, $listenerClassName) = $listener;

            // create a new instance of the listener class
            $listenerInstance = new $listenerClassName();

            // query whether we've to inject the application server instance or not
            if ($listenerInstance instanceof ApplicationServerAwareListenerInterface) {
                $listenerInstance->injectApplicationServer($this);
            }

            // add the listeners
            $emitter->addListener($event, $listenerInstance);
        }

        // synchronize the emitter
        $this->emitter = $emitter;

        // flag to keep the server running or to stop it
        $keepRunning = true;

        // initialize the actual runlevel with -1
        $actualRunlevel = -1;

        // start with the default runlevel
        $this->init($this->runlevel);

        do {

            try {

                switch ($this->command) {

                    case InitCommand::COMMAND:

                        // copy command params -> the requested runlevel in that case
                        $this->runlevel = $this->params;

                        if ($this->runlevel == ApplicationServerInterface::REBOOT) {
                            // backup the runlevel
                            $backupRunlevel = $actualRunlevel;

                            // shutdown the application server
                            for ($i = $actualRunlevel; $i >= ApplicationServerInterface::SHUTDOWN; $i--) {
                                $this->doStopServices($i);
                                $this->emitter->emit(sprintf('leave.runlevel.%s', $this->runlevelToString($i)), $this->getNamingDirectory());
                            }

                            // switch back to the runlevel we backed up before
                            for ($z = ApplicationServerInterface::SHUTDOWN; $z <= $backupRunlevel; $z++) {
                                $this->emitter->emit(sprintf('enter.runlevel.%s', $this->runlevelToString($z)), $this->getNamingDirectory());
                            }

                            // set the runlevel to the one before we restart
                            $actualRunlevel = $backupRunlevel;

                            // reset the runlevel and the params
                            $this->runlevel = $this->params = $actualRunlevel;

                        } elseif ($actualRunlevel == ApplicationServerInterface::SHUTDOWN) {
                            // we want to shudown the application server
                            $keepRunning = false;

                        } elseif ($actualRunlevel < $this->runlevel) {
                            // switch to the requested runlevel
                            for ($i = $actualRunlevel + 1; $i <= $this->runlevel; $i++) {
                                $this->emitter->emit(sprintf('enter.runlevel.%s', $this->runlevelToString($i)), $this->getNamingDirectory());
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } elseif ($actualRunlevel > $this->runlevel) {
                            // switch down to the requested runlevel
                            for ($i = $actualRunlevel; $i >= $this->runlevel; $i--) {
                                $this->doStopServices($i);
                                $this->emitter->emit(sprintf('leave.runlevel.%s', $this->runlevelToString($i)), $this->getNamingDirectory());
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } else {

                            // print a message and wait
                            $this->getNamingDirectory()->search('php:global/log/System')->info(sprintf('Switched to runlevel %s!!!', $actualRunlevel));

                            // signal that we've finished switching the runlevels and wait
                            $this->locked = false;
                            $this->command = null;

                            // wait for a new command
                            $this->synchronized(function ($self) {
                                $self->wait();
                            }, $this);
                        }

                        break;

                    case ModeCommand::COMMAND:

                        // singal that we've finished setting umask and wait
                        $this->locked = false;
                        $this->command = null;

                        // switch the application server mode
                        $this->doSwitchSetupMode($this->params, $this->getConfigurationFilename());

                        // wait for a new command
                        $this->synchronized(function ($self) {
                            $self->wait();
                        }, $this);

                        break;

                    default:

                        // print a message and wait
                         $this->getNamingDirectory()->search('php:global/log/System')->info('Can\'t find any command!!!');

                        // singal that we've finished setting umask and wait
                        $this->locked = false;

                        // wait for a new command
                        $this->synchronized(function ($self) {
                            $self->wait();
                        }, $this);

                        break;
                }

            } catch (\Exception $e) {
                $this->getNamingDirectory()->search('php:global/log/System')->error($e->getMessage());
            }

        } while ($keepRunning);
    }

    /**
     * Returns the service for the passed runlevel and name.
     *
     * @param integer $runlevel The runlevel of the requested service
     * @param string  $name     The name of the requested service
     */
    public function getService($runlevel, $name)
    {
        return $this->services[$runlevel][$name];
    }

    /**
     * Unbind the service with the passed name and runlevel.
     *
     * @param integer $runlevel The runlevel of the service
     * @param string  $name     The name of the service
     *
     * @return void
     */
    public function unbindService($runlevel, $name)
    {

        // stop the service instance
        $this->services[$runlevel][$name]->stop();

        // unset the service instance
        unset($this->services[$runlevel][$name]);

        // print a message that the service has been stopped
        $this->getNamingDirectory()->search('php:global/log/System')->error(sprintf('Successfully stopped service %s', $name));
    }

    /**
     * Binds the passed service to the runlevel.
     *
     * @param integer $runlevel The runlevel to bound the service to
     * @param object  $service  The service to bound
     *
     * @return void
     */
    public function bindService($runlevel, $service)
    {

        // bind the service to the runlevel
        $this->services[$runlevel][$service->getName()] = $service;

        // bind the service callback to the naming directory
        $this->getNamingDirectory()->bindCallback(
            sprintf('php:services/%s/%s', $this->runlevelToString($runlevel), $service->getName()),
            array($this, 'getService'),
            array($runlevel, $service->getName())
        );
    }

    /**
     * Stops all services of the passed runlevel.
     *
     * @param integer $runlevel The runlevel to stop all services for
     *
     * @return void
     */
    protected function doStopServices($runlevel)
    {
        // iterate over all services and stop them
        foreach ($this->services[$runlevel] as $name => $service) {
            $this->unbindService($unlevel, $name);
        }
    }

    /**
     * Switches the running setup mode to the passed value.
     *
     * @param string $newMode               The mode to switch to
     * @param string $configurationFilename The path of the configuration filename
     *
     * @return void
     * @throws \Exception Is thrown for an invalid setup mode passed
     */
    protected function doSwitchSetupMode($newMode, $configurationFilename)
    {
        // load the service instance and switch to the new setup mode
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
        $service->switchSetupMode($newMode, $configurationFilename);
    }
}
