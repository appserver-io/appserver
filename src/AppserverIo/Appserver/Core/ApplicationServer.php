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
use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\Console\Telnet;
use AppserverIo\Appserver\Core\Api\Node\ParamNode;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Api\ConfigurationService;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;
use AppserverIo\Appserver\Meta\Composer\Script\Setup;
use AppserverIo\Appserver\Core\Utilities\FileSystem;
use AppserverIo\Appserver\Meta\Composer\Script\SetupKeys;
use AppserverIo\Appserver\Core\Console\TelnetFactory;
use AppserverIo\Appserver\Core\Api\ContainerService;
use AppserverIo\Appserver\Core\Extractors\PharExtractorFactory;
use AppserverIo\Appserver\Core\Commands\ModeCommand;
use AppserverIo\Appserver\Core\Commands\InitCommand;
use AppserverIo\Psr\Naming\NamingDirectoryInterface;

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
     * Simple logger method that writes the passed log messages.
     * to a stream.
     *
     * @param string $message The message to log
     *
     * @return void
     */
    protected function log($message)
    {
        $this->getSystemLogger()->info($message);
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
                $this->log($message);
            }
        }
    }

    /**
     * Do all the bootstrapping stuff necessary to start services etc.
     *
     * @return void
     */
    protected function doBootstrap()
    {

        // load the system configuration
        $this->doLoadConfiguration($this->getConfigurationFilename());

        // load the system loggers and the initial context
        $this->doLoadLoggers($this->getSystemConfiguration());
        $this->doLoadInitialContext($this->getSystemConfiguration());

        // switch the default umask
        $this->doSwitchUmask($this->getSystemConfiguration()->getUmask());

        // check that the application server has been installed properly
        $this->doSwitchSetupMode(ContainerService::SETUP_MODE_INSTALL, $this->getConfigurationFilename());

        // prepare the file system
        $this->doPrepareFileSystem();

        // create a SSL certificate if not already available
        $this->doCreateSslCertificate();
    }

    public function firstListenerTest($event)
    {
        $this->log('EMITTED EVENT');

        try {
            $console = $this->getNamingDirectory()->search('php:services/administration/console');
            $this->log("Found console class " . get_class($console));
        } catch (\Exception $e) {
            $this->log($e->__toString());
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

        $emitter = new Emitter();
        $emitter->addListener('do.switch.runlevel.up', array($this, 'firstListenerTest'));

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
                                $this->doSwitchRunlevelDown($i);
                            }

                            // switch back to the runlevel we backed up before
                            for ($z = ApplicationServerInterface::SHUTDOWN; $z <= $backupRunlevel; $z++) {
                                $this->doSwitchRunlevelUp($z);
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
                                $this->doSwitchRunlevelUp($i);
                                $this->emitter->emit('do.switch.runlevel.up');
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } elseif ($actualRunlevel > $this->runlevel) {

                            // switch down to the requested runlevel
                            for ($i = $actualRunlevel; $i >= $this->runlevel; $i--) {
                                $this->doSwitchRunlevelDown($i);
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } else {

                            // print a message and wait
                            $this->log("Switched to runlevel $actualRunlevel!!!");

                            // singal that we've finished switching the runlevels and wait
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
                        $this->log('Can\'t find any command!!!');

                        // singal that we've finished setting umask and wait
                        $this->locked = false;

                        // wait for a new command
                        $this->synchronized(function ($self) {
                            $self->wait();
                        }, $this);

                        break;
                }

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
    protected function doStopServices($runlevel)
    {
        // iterate over all services and stop them
        foreach ($this->services[$runlevel] as $name => $service) {
            // stop the service instance
            $this->services[$runlevel][$name]->stop();

            // unset the service instance
            unset($this->services[$runlevel][$name]);

            // print a message that the service has been stopped
            $this->log("Successfully stopped service $name");
        }
    }

    public function getService($runlevel, $name)
    {
        return $this->services[$runlevel][$name];
    }

    /**
     * This is main method to switch between the runlevels.
     *
     * @param integer $actualRunlevel The runlevel the application server actual has
     *
     * @return void
     *
     * @throws \Exception Is thrown if an unknown runlevel has been requested
     */
    protected function doSwitchRunlevelUp($actualRunlevel)
    {

        // query the new runlevel
        switch ($actualRunlevel) {

            case ApplicationServerInterface::SHUTDOWN:

                // bootstrap the application server
                $this->doBootstrap();

                break;

            case ApplicationServerInterface::ADMINISTRATION:

                // create a new console instance and start it
                $console = TelnetFactory::factory($this);

                // register the container as service
                $this->services[$actualRunlevel][$console->getName()] = $console;

                $this->getNamingDirectory()->bindCallback(
                    sprintf('php:services/administration/%s', $console->getName()),
                    array($this, 'getService'),
                    array($actualRunlevel, $console->getName())
                );

                break;

            case ApplicationServerInterface::DAEMON:

                break;

            case ApplicationServerInterface::NETWORK:

                $this->doStartContainers($services);

                break;

            case ApplicationServerInterface::SECURE:

                // switch the user and group
                $this->doSwitchUser($this->getSystemConfiguration()->getUser(), $this->getSystemConfiguration()->getGroup());

                break;

            case ApplicationServerInterface::FULL:

                $this->doExtract();
                $this->doDeploy();

                break;

            case ApplicationServerInterface::REBOOT:

                break;

            default:
                throw new \Exception("Invalid runlevel $actualRunlevel requested");
                break;
        }
    }


    protected function doStartContainers()
    {

        // and initialize a container thread for each container
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode */
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {

            // create a new container instance and start it
            $container = GenericContainerFactory::factory($this, $containerNode);
            $container->start(PTHREADS_INHERIT_ALL);

            // register the container as service
            $this->services[ApplicationServerInterface::NETWORK][$containerNode->getName()] = $container;
        }
    }

    protected function doExtract()
    {

        // add the configured extractors to the internal array
        /** @var \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface $extractorNode */
        foreach ($this->getSystemConfiguration()->getExtractors() as $extractorNode) {

            // create a new extractor instance
            $extractor = PharExtractorFactory::factory($this, $extractorNode);

            // deploy the found archives
            $extractor->deployWebapps();

            // log that the extractor has successfully been initialized and executed
            $this->getSystemLogger()->debug(sprintf('Extractor %s successfully initialized and executed', $extractorNode->getName()));
        }
    }

    protected function doDeploy()
    {

        // deploy the applications for all containers
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode */
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {

            // load the container instance to deploy the applications for
            $container = $this->services[ApplicationServerInterface::NETWORK][$containerNode->getName()];

            // load the containers deployment
            $deployment = $container->getDeployment();
            $deployment->injectContainer($container);

            // deploy and initialize the container's applications
            $deployment->deploy();
        }
    }

    /**
     * This is main method to switch between the runlevels.
     *
     * @param integer $actualRunlevel The runlevel the application server actual has
     *
     * @return void
     *
     * @throws \Exception Is thrown if an unknown runlevel has been requested
     */
    protected function doSwitchRunlevelDown($actualRunlevel)
    {

        // stop all services for this runlevel
        $this->doStopServices($actualRunlevel + 1);

        // query the new runlevel
        switch ($actualRunlevel) {

            case ApplicationServerInterface::SHUTDOWN:

                break;

            case ApplicationServerInterface::ADMINISTRATION:

                break;

            case ApplicationServerInterface::DAEMON:

                break;

            case ApplicationServerInterface::NETWORK:

                break;

            case ApplicationServerInterface::SECURE:

                $this->doSwitchUser('root', 'root');

                break;

            case ApplicationServerInterface::FULL:

                break;

            case ApplicationServerInterface::REBOOT:

                break;

            default:
                throw new \Exception("Invalid runlevel $actualRunlevel requested");
                break;
        }
    }

    /**
     * Switch user and group to the passed values.
     *
     * @param string $user  The user to switch to
     * @param string $group The group to switch to
     *
     * @return void
     */
    protected function doSwitchUser($user, $group = null)
    {

        // print a message with the old UID/EUID
        $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());

        // extract the variables
        $uid = 0;
        extract(posix_getpwnam($user));

        // switcht the effective UID to the passed user
        if (posix_seteuid($uid) === false) {
            $this->log(sprintf('Can\'t switch UID to \'%s\'', $uid));
        }

        // print a message with the new UID/EUID
        $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());
    }

    /**
     * Loads the system configuration from passed configuration file.
     *
     * @param string $filename The path and name of the file to load the configuration for
     *
     * @throws \Exception Is thrown if the configuration can't be parsed
     */
    protected function doLoadConfiguration($filename)
    {

        // initialize configuration and schema file name
        $configurationFileName = DirectoryKeys::realpath($filename);

        // initialize the DOMDocument with the configuration file to be validated
        $configurationFile = new \DOMDocument();
        $configurationFile->load($configurationFileName);

        // substitute xincludes
        $configurationFile->xinclude(LIBXML_SCHEMA_CREATE);

        // create a DOMElement with the base.dir configuration
        $paramElement = $configurationFile->createElement('param', APPSERVER_BP);
        $paramElement->setAttribute('name', DirectoryKeys::BASE);
        $paramElement->setAttribute('type', ParamNode::TYPE_STRING);

        // create an XPath instance
        $xpath = new \DOMXpath($configurationFile);
        $xpath->registerNamespace('a', 'http://www.appserver.io/appserver');

        // for node data in a selected id
        $baseDirParam = $xpath->query(sprintf('/a:appserver/a:params/a:param[@name="%s"]', DirectoryKeys::BASE));
        if ($baseDirParam->length === 0) {

            // load the <params> node
            $paramNodes = $xpath->query('/a:appserver/a:params');

            // load the first item => the node itself
            if ($paramsNode = $paramNodes->item(0)) {
                // append the base.dir DOMElement
                $paramsNode->appendChild($paramElement);
            } else {
                // throw an exception, because we can't find a mandatory node
                throw new \Exception('Can\'t find /appserver/params node');
            }
        }

        // create a new DOMDocument with the merge content => necessary because else, schema validation fails!!
        $mergeDoc = new \DOMDocument();
        $mergeDoc->loadXML($configurationFile->saveXML());

        // get an instance of our configuration tester
        $configurationService = new ConfigurationService(new InitialContext(new AppserverNode()));

        // validate the configuration file with the schema
        if ($configurationService->validateXml($mergeDoc) === false) {
            $this->log(print_r($configurationService->getErrorMessages(), true));
            throw new \Exception('Can\'t parse configuration file');
        }

        // initialize the SimpleXMLElement with the content XML configuration file
        $configuration = new Configuration();
        $configuration->initFromString($mergeDoc->saveXML());

        // initialize the configuration and the base directory
        $systemConfiguration = new AppserverNode();
        $systemConfiguration->initFromConfiguration($configuration);
        $this->setSystemConfiguration($systemConfiguration);
    }

    /**
     * Loads the initial context instance defined in the passed
     * system configuration.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration with the loggers
     *
     * @return void
     */
    protected function doLoadInitialContext(SystemConfigurationInterface $systemConfiguration)
    {

        // load the initial context configuration
        $initialContextNode = $systemConfiguration->getInitialContext();
        $reflectionClass = new \ReflectionClass($initialContextNode->getType());
        $initialContext = $reflectionClass->newInstanceArgs(array($this->getSystemConfiguration()));

        // attach the registered loggers to the initial context
        $initialContext->setLoggers($this->getLoggers());
        $initialContext->setSystemLogger($this->getSystemLogger());

        // set the initial context and flush it initially
        $this->setInitialContext($initialContext);
    }

    /**
     * Initialize all loggers.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration
     *
     * @return void
     */
    protected function doLoadLoggers(SystemConfigurationInterface $systemConfiguration)
    {

        // initialize the loggers
        $loggers = array();
        foreach ($systemConfiguration->getLoggers() as $loggerNode) {
            $loggers[$loggerNode->getName()] = LoggerFactory::factory($loggerNode);
        }

        // set the initialized loggers finally
        $this->setLoggers($loggers);
    }

    /**
     * Switches the umask to the passed value.
     *
     * @param integer $newUmask The umask to set
     *
     * @return void
     */
    protected function doSwitchUmask($newUmask)
    {
        // load the service instance and switch the umask
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $service->initUmask($newUmask);
    }

    /**
     * Creates a new SSL certificate on first system start.
     *
     * @param string $certificateName The name of the certificate to be generated, e. g. server.pem
     *
     * @return void
     */
    protected function doCreateSslCertificate($certificateName = 'server.pem')
    {
        // load the service instance and create the SSL file if not available
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
        $service->createSslCertificate(new \SplFileInfo($service->getConfDir($certificateName)));
    }

    /**
     * Prepares filesystem to be sure that everything is on place as expected
     *
     * @return void
     * @throws \Exception Is thrown if a server directory can't be created
     */
    protected function doPrepareFileSystem()
    {
        // load the service instance and prepare the filesystem
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
        $service->prepareFileSystem();
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
