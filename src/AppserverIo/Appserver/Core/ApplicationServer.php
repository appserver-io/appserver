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
use React\Socket\ConnectionInterface;
use AppserverIo\Logger\Logger;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Naming\NamingDirectoryInterface;
use AppserverIo\Appserver\Core\Commands\ModeCommand;
use AppserverIo\Appserver\Core\Commands\InitCommand;
use AppserverIo\Appserver\Core\Api\Node\BootstrapNode;
use AppserverIo\Appserver\Core\Utilities\Runlevels;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Appserver\Core\Listeners\ApplicationServerAwareListenerInterface;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;

/**
 * This is the main server class that starts the application server
 * and creates a separate thread for each container found in the
 * configuration file.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ApplicationServer extends \Thread implements ApplicationServerInterface
{

    /**
     * The application server instance itself.
     *
     * @var \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface
     */
    protected static $instance;

    /**
     * Initialize and start the application server.
     *
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The default naming directory
     * @param \AppserverIo\Storage\GenericStackable            $runlevels       The storage for the services
     */
    protected function __construct(NamingDirectoryInterface $namingDirectory, GenericStackable $runlevels)
    {

        // set the services and the naming directory
        $this->runlevels = $runlevels;
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
     * Creates a new singleton application server instance.
     *
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The default naming directory
     * @param \AppserverIo\Storage\GenericStackable            $runlevels       The storage for the services
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface The singleton application instance
     */
    public static function singleton(NamingDirectoryInterface $namingDirectory, GenericStackable $runlevels)
    {

        // query whether we already have an instance or not
        if (ApplicationServer::$instance == null) {
            // initialize and start the application server
            ApplicationServer::$instance = new ApplicationServer($namingDirectory, $runlevels);
            ApplicationServer::$instance->start();
        }

        // return the instance
        return ApplicationServer::$instance;
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
        $runlevels = array_flip(Runlevels::singleton()->getRunlevels());
        if (isset($runlevels[$runlevel])) {
            return $runlevels[$runlevel];
        }

        // throw an exception if the runlevel is unknown
        throw new \Exception(sprintf('Request invalid runlevel to string conversion for %s', $runlevel));
    }

    /**
     * Translates and returns the runlevel of the passed a string representation.
     *
     * @param string $runlevel The string representation of the runlevel to return
     *
     * @return integer The runlevel of the passed string representation
     *
     * @throws \Exception Is thrown if the passed string representation is not a valid runlevel
     */
    public function runlevelFromString($runlevel)
    {

        // query whether the passed string representation is a valid runlevel
        if (Runlevels::singleton()->isRunlevel($runlevel)) {
            return Runlevels::singleton()->getRunlevel($runlevel);
        }

        // throw an exception if the runlevel is unknown
        throw new \Exception(sprintf('Request invalid runlevel to string conversion for %s', $runlevel));
    }

    /**
     * Returns the naming directory instance.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The default naming directory
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
     *
     * @return void
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
     * Returns the requested logger instance.
     *
     * @param string $name Name of the requested logger instance
     *
     * @return \Psr\Log\LoggerInterface|null The requested logger instance
     */
    public function getLogger($name)
    {
        if (isset($this->loggers[$name])) {
            return $this->loggers[$name];
        }
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    public function getSystemLogger()
    {

        try {
            return $this->getNamingDirectory()->search(NamingDirectoryKeys::SYSTEM_LOGGER);
        } catch (NamingException $ne) {
            return new Logger('System');
        }
    }

    /**
     * Returns the name and the path of the system configuration file.
     *
     * @return string Name and path of the sytsem configuration file
     */
    public function getConfigurationFilename()
    {
        return $this->getNamingDirectory()->search('php:env/configurationFilename');
    }

    /**
     * Returns the name and the path of the bootstrap configuration file.
     *
     * @return string Name and path of the bootstrap configuraiton file
     */
    public function getBootstrapConfigurationFilename()
    {
        return $this->getNamingDirectory()->search('php:env/bootstrapConfigurationFilename');
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
     * @param \React\Socket\ConnectionInterface $conn     The connection resource
     * @param integer                           $runlevel The new runlevel to switch to
     *
     * @return void
     */
    public function init(ConnectionInterface $conn = null, $runlevel = ApplicationServerInterface::FULL)
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
     * Switch to the passed mode, which can either be 'dev', 'prod' or 'install'.
     *
     * @param \React\Socket\ConnectionInterface $conn The connection resource
     * @param string                            $mode The setup mode to switch to
     *
     * @return void
     */
    public function mode(ConnectionInterface $conn, $mode)
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
     * @return void
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

        // load the bootstrap configuration
        /** @var \AppserverIo\Appserver\Core\Api\Node\BootstrapNodeInterface $bootstrapNode */
        $bootstrapNode = $this->doLoadBootstrap($this->getBootstrapConfigurationFilename());

        // iterate over the listeners and add them to the emitter
        /** @var \AppserverIo\Appserver\Core\Api\Node\ListenerNodeInterface $listener */
        foreach ($bootstrapNode->getListeners() as $listener) {
            // load the listener class name
            $listenerClassName = $listener->getType();

            // create a new instance of the listener class
            /** @var \League\Event\ListenerInterface $listenerInstance */
            $listenerInstance = new $listenerClassName();

            // query whether we've to inject the application server instance or not
            if ($listenerInstance instanceof ApplicationServerAwareListenerInterface) {
                $listenerInstance->injectApplicationServer($this);
            }

            // add the listeners
            $emitter->addListener($listener->getEvent(), $listenerInstance);
        }

        // synchronize the emitter
        $this->emitter = $emitter;

        // override the default runlevel with the value found in the bootstrap configuration
        $this->runlevel = $this->runlevelFromString($bootstrapNode->getDefaultRunlevel());

        // flag to keep the server running or to stop it
        $keepRunning = true;

        // initialize the actual runlevel with -1
        $actualRunlevel = -1;

        // start with the default runlevel
        $this->init(null, $this->runlevel);

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
                                $this->emitter->emit(sprintf('leave.runlevel.%s', $this->runlevelToString($i)), $i);
                                // stop the services of the PREVIOUS runlevel
                                $this->doStopServices($i + 1);
                            }

                            // switch back to the runlevel we backed up before
                            for ($z = ApplicationServerInterface::SHUTDOWN; $z <= $backupRunlevel; $z++) {
                                $this->emitter->emit(sprintf('enter.runlevel.%s', $this->runlevelToString($z)), $z);
                            }

                            // set the runlevel to the one before we restart
                            $actualRunlevel = $backupRunlevel;

                            // reset the runlevel and the params
                            $this->runlevel = $this->params = $actualRunlevel;

                        } elseif ($actualRunlevel == ApplicationServerInterface::SHUTDOWN) {
                            // we want to shutdown the application server
                            $keepRunning = false;

                        } elseif ($actualRunlevel < $this->runlevel) {
                            // switch to the requested runlevel
                            for ($i = $actualRunlevel + 1; $i <= $this->runlevel; $i++) {
                                $this->emitter->emit(sprintf('enter.runlevel.%s', $this->runlevelToString($i)), $i);
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } elseif ($actualRunlevel > $this->runlevel) {
                            // switch down to the requested runlevel
                            for ($i = $actualRunlevel; $i >= $this->runlevel; $i--) {
                                $this->emitter->emit(sprintf('leave.runlevel.%s', $this->runlevelToString($i)), $i);
                                // stop the services of the PREVIOUS runlevel
                                $this->doStopServices($i + 1);
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } else {
                            // signal that we've finished switching the runlevels and wait
                            $this->locked = false;
                            $this->command = null;

                            // print a message and wait
                            $this->getSystemLogger()->info(sprintf('Switched to runlevel %s!!!', $actualRunlevel));

                            // wait for a new command
                            $this->synchronized(function ($self) {
                                $self->wait();
                            }, $this);
                        }

                        break;

                    case ModeCommand::COMMAND:

                        // switch the application server mode
                        $this->doSwitchSetupMode($this->params, $this->getConfigurationFilename());

                        // singal that we've finished setting umask and wait
                        $this->locked = false;
                        $this->command = null;

                        // wait for a new command
                        $this->synchronized(function ($self) {
                            $self->wait();
                        }, $this);

                        break;

                    default:

                        // print a message and wait
                         $this->getSystemLogger()->info('Can\'t find any command!!!');

                        // singal that we've finished setting umask and wait
                        $this->locked = false;

                        // wait for a new command
                        $this->synchronized(function ($self) {
                            $self->wait();
                        }, $this);

                        break;
                }

            } catch (\Exception $e) {
                $this->getSystemLogger()->error($e->getMessage());
            }

        } while ($keepRunning);
    }

    /**
     * Returns the service for the passed runlevel and name.
     *
     * @param integer $runlevel The runlevel of the requested service
     * @param string  $name     The name of the requested service
     *
     * @return mixed The service instance
     */
    public function getService($runlevel, $name)
    {
        return $this->runlevels[$runlevel][$name];
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
        $this->runlevels[$runlevel][$name]->stop();

        // unbind the service from the naming directory
        $this->getNamingDirectory()->unbind(sprintf('php:services/%s/%s', $this->runlevelToString($runlevel), $name));

        // unset the service instance
        unset($this->runlevels[$runlevel][$name]);

        // print a message that the service has been stopped
        $this->getSystemLogger()->info(sprintf('Successfully stopped service %s', $name));
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
        $this->runlevels[$runlevel][$service->getName()] = $service;

        // bind the service callback to the naming directory
        $this->getNamingDirectory()->bindCallback(
            sprintf('php:services/%s/%s', $this->runlevelToString($runlevel), $service->getName()),
            array(&$this, 'getService'),
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
    public function stopServices($runlevel)
    {
        $this->doStopServices($runlevel);
    }

    /**
     * Loads the bootstrap configuration from the XML file.
     *
     * @param string $bootstrapConfigurationFilename The boostrap configuration file
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\BootstrapNode The boostrap configuration
     */
    protected function doLoadBootstrap($bootstrapConfigurationFilename)
    {

        // initialize the bootstrap configuration
        $bootstrapNode = new BootstrapNode();
        $bootstrapNode->initFromFile($bootstrapConfigurationFilename);

        // return the bootstrap configuration
        return $bootstrapNode;
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
        foreach (array_flip($this->runlevels[$runlevel]) as $name) {
            $this->unbindService($runlevel, $name);
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
        // load the current user from the naming directory
        $currentUser = $this->getNamingDirectory()->search('php:env/currentUser');

        // load the service instance and switch to the new setup mode
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
        $service->switchSetupMode($newMode, $configurationFilename, $currentUser);
    }
}
