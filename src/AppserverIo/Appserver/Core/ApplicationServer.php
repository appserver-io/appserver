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
     * @param string $configurationFilename The default configuration file
     */
    public function __construct($configurationFilename = 'etc/appserver/appserver.xml')
    {

        // initialize the default configuration filename
        $this->configurationFilename = $configurationFilename;

        // initialize the default runlevel
        $this->runlevel = ApplicationServerInterface::ADMINISTRATION;

        // set to TRUE, because we switch to runlevel 1 immediately
        $this->locked = false;

        // initialize command/params
        $this->command = null;
        $this->params = null;
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
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Core\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    public function setLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    public function getLoggers()
    {
        return $this->loggers;
    }

    /**
     * Returns the system logger instance.
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
        return $this->configurationFilename;
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

            $self->command = 'init';

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

            $self->command = 'mode';

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
    protected function bootstrap()
    {

        // load the system configuration
        $this->loadConfiguration($this->getConfigurationFilename());

        // load loggers and initial context
        $systemConfiguration = $this->getSystemConfiguration();
        $this->loadLoggers($systemConfiguration);
        $this->loadInitialContext($systemConfiguration);

        // switch the default umask
        $this->switchUmask($this->getSystemConfiguration()->getUmask());

        // create a SSL certificate if not already available
        $this->createSslCertificate();
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

        // array containing all service instances
        $services = array();

        // add the storeage containers for the runlevels
        foreach (ApplicationServer::$runlevels as $runlevel) {
            $services[$runlevel] = array();
        }

        // flag to keep the server running or to stop it
        $keepRunning = true;

        // initialize the actual runlevel with -1
        $actualRunlevel = -1;

        // start with the default runlevel
        $this->init($this->runlevel);

        do {

            try {

                switch ($this->command) {

                    case 'init':

                        // copy command params -> the requested runlevel in that case
                        $this->runlevel = $this->params;

                        if ($this->runlevel == ApplicationServerInterface::REBOOT) {

                            // backup the runlevel
                            $backupRunlevel = $actualRunlevel;

                            // shutdown the application server
                            for ($i = $actualRunlevel; $i >= ApplicationServerInterface::SHUTDOWN; $i--) {
                                $this->switchRunlevelDown($services, $i);
                            }

                            // switch back to the runlevel we backed up before
                            for ($z = ApplicationServerInterface::SHUTDOWN; $z <= $backupRunlevel; $z++) {
                                $this->switchRunlevelUp($services, $z);
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
                                $this->switchRunlevelUp($services, $i);
                            }

                            // set the new runlevel
                            $actualRunlevel = $this->runlevel;

                        } elseif ($actualRunlevel > $this->runlevel) {

                            // switch down to the requested runlevel
                            for ($i = $actualRunlevel; $i >= $this->runlevel; $i--) {
                                $this->switchRunlevelDown($services, $i);
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

                    case 'mode':

                        // singal that we've finished setting umask and wait
                        $this->locked = false;
                        $this->command = null;

                        // switch the application server mode
                        $this->switchMode($this->params);

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
    protected function stopAllServicesForRunlevel(&$services, $runlevel)
    {
        // iterate over all services and stop them
        foreach (array_keys($services[$runlevel]) as $name) {
            // stop, kill and unset the service instance
            $services[$runlevel][$name]->stop();
            $services[$runlevel][$name]->kill();
            unset($services[$runlevel][$name]);

            // print a message that the service has been stopped
            $this->log("Successfully stopped service $name");
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
    protected function switchRunlevelUp(&$services, $actualRunlevel)
    {

        // query the new runlevel
        switch ($actualRunlevel) {

            case ApplicationServerInterface::SHUTDOWN:

                // bootstrap the application server
                $this->bootstrap();

                break;

            case ApplicationServerInterface::ADMINISTRATION:

                // create and register the Telnet console instance
                $console = TelnetFactory::factory($this);
                $services[$actualRunlevel][$console->getName()] = $console;

                break;

            case ApplicationServerInterface::DAEMON:

                break;

            case ApplicationServerInterface::NETWORK:

                break;

            case ApplicationServerInterface::SECURE:

                // switch the user and group
                $this->switchUser($this->getSystemConfiguration()->getUser(), $this->getSystemConfiguration()->getGroup());

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
     * This is main method to switch between the runlevels.
     *
     * @param integer $actualRunlevel The runlevel the application server actual has
     *
     * @return void
     *
     * @throws \Exception Is thrown if an unknown runlevel has been requested
     */
    protected function switchRunlevelDown(&$services, $actualRunlevel)
    {

        $this->stopAllServicesForRunlevel($services, $actualRunlevel + 1);

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

                $this->switchUser('root', 'root');

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
    protected function switchUser($user, $group = null)
    {

        // print a message with the old UID/EUID
        $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());

        // extract the variables
        extract(posix_getpwnam($user));

        // switcht the effective UID to the passed user
        if (posix_seteuid($uid) === false) {
            $this->log(sprintf('Can\'t switch UID to \'%s\'', $uid));
        }

        // print a message with the new UID/EUID
        $this->log("Running as " . posix_getuid() . "/" . posix_geteuid());
    }

    /**
     * Switches the running setup mode to the passed value.
     *
     * @param string $newMode The mode to switch to
     *
     * @return void
     * @throws \Exception Is thrown for an invalid setup mode passed
     */
    protected function switchMode($newMode)
    {

        $this->log(sprintf('Now switch mode to %s!!!', $newMode));

        // init setup context
        Setup::prepareContext(APPSERVER_BP);

        // init user and group vars
        $user = null;
        $group = null;

        $configurationUserReplacePattern = '/(<appserver[^>]+>[^<]+<params>.*<param name="user[^>]+>)([^<]+)/s';

        $configurationFilename = $this->getConfigurationFilename();

        // check setup modes
        switch ($newMode) {

            // prepares everything for developer mode
            case 'dev':
                // set current user
                $user = get_current_user();
                // check if script is called via sudo
                if (array_key_exists('SUDO_USER', $_SERVER)) {
                    // set current sudo user
                    $user = $_SERVER['SUDO_USER'];
                }
                // get defined group from configuration
                $group = Setup::getValue(SetupKeys::GROUP);
                // replace user in configuration file
                file_put_contents($configurationFilename, preg_replace(
                    $configurationUserReplacePattern,
                    '${1}' . $user,
                    file_get_contents($configurationFilename)
                ));
                // add everyone write access to configuration files for dev mode
                FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc', 0777, 0777);

                break;

                // prepares everything for production mode
            case 'prod':
                // get defined user and group from configuration
                $user = Setup::getValue(SetupKeys::USER);
                $group = Setup::getValue(SetupKeys::GROUP);
                // replace user to be same as user in configuration file
                file_put_contents($configurationFilename, preg_replace(
                    $configurationUserReplacePattern,
                    '${1}' . $user,
                    file_get_contents($configurationFilename)
                ));
                // set correct file permissions for configurations
                FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc');

                break;

                // prepares everything for first installation which is default mode
            case 'install':
                // first check if it is a fresh installation
                if (!IS_INSTALLED) {
                    // set example app dodeploy flag to be deployed for a fresh installation
                    touch(APPSERVER_BP . DIRECTORY_SEPARATOR . 'deploy' . DIRECTORY_SEPARATOR . 'example.phar.dodeploy');
                }

                // create is installed flag for prevent further setup install mode calls
                touch(IS_INSTALLED_FILE);

                // get defined user and group from configuration
                $user = Setup::getValue(SetupKeys::USER);
                $group = Setup::getValue(SetupKeys::GROUP);

                // set correct file permissions for configurations
                FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'etc');

                break;
            default:
                throw new \Exception('No valid setup mode given');
        }

        // check if user and group is set
        if (!is_null($user) && !is_null($group)) {
            // get needed files as accessable for all root files remove "." and ".." from the list
            $rootFiles = scandir(APPSERVER_BP);
            // iterate all files
            foreach ($rootFiles as $rootFile) {
                // we want just files on root dir
                if (is_file($rootFile) && !in_array($rootFile, array('.', '..'))) {
                    FileSystem::chmod($rootFile, 0644);
                    FileSystem::chown($rootFile, $user, $group);
                }
            }
            // ... and change own and mod of following directories
            FileSystem::chown(APPSERVER_BP, $user, $group);
            FileSystem::chown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'webapps', $user, $group);
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'resources', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'resources');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'deploy', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'deploy');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'src', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'src');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'var', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'var');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'tests', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'tests');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'vendor', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'vendor');
            FileSystem::recursiveChown(APPSERVER_BP . DIRECTORY_SEPARATOR . 'tmp', $user, $group);
            FileSystem::recursiveChmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'tmp');
            // make server.php executable
            FileSystem::chmod(APPSERVER_BP . DIRECTORY_SEPARATOR . 'server.php', 0755);

            $this->log("Setup for mode '$newMode' done successfully!");

        } else {
            throw new \Exception('No user or group given');
        }
    }

    /**
     * Loads the system configuration from passed configuration file.
     *
     * @param string $filename The path and name of the file to load the configuration for
     *
     * @throws \Exception Is thrown if the configuration can't be parsed
     */
    protected function loadConfiguration($filename)
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
    protected function loadInitialContext(SystemConfigurationInterface $systemConfiguration)
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
     * Switches the umask to the passed value.
     *
     * @param integer $newUmask The umask to set
     *
     * @return void
     */
    protected function switchUmask($newUmask)
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
    protected function createSslCertificate($certificateName = 'server.pem')
    {
        // load the service instance and create the SSL file if not available
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
        $service->createSslCertificate(new \SplFileInfo($service->getConfDir($certificateName)));
    }

    /**
     * Initialize all loggers.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration The system configuration
     *
     * @return void
     */
    protected function loadLoggers(SystemConfigurationInterface $systemConfiguration)
    {

        // initialize the loggers
        $loggers = array();
        foreach ($systemConfiguration->getLoggers() as $loggerNode) {
            $loggers[$loggerNode->getName()] = LoggerFactory::factory($loggerNode);
        }

        // set the initialized loggers finally
        $this->setLoggers($loggers);
    }
}
