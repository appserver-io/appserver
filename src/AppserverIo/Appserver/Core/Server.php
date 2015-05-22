<?php

/**
 * \AppserverIo\Appserver\Core\Server
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Scanner\ScannerFactory;
use AppserverIo\Appserver\Core\Scanner\HeartbeatScanner;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Utilities\ContainerStateKeys;
use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Appserver\Core\Interfaces\ProvisionerInterface;
use AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface;
use AppserverIo\Configuration\Interfaces\ConfigurationInterface;

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
class Server
{

    /**
     * Initialize the array for the running threads.
     *
     * @var array
     */
    protected $containers = array();

    /**
     * The registred extractors.
     *
     * @var array
     */
    protected $extractors = array();

    /**
     * The system configuration.
     *
     * @var \AppserverIo\Configuration\Interfaces\NodeInterface
     */
    protected $systemConfiguration;

    /**
     * The servers initial context instance.
     *
     * @var \AppserverIo\Appserver\Application\Interfaces\ContextInterface
     */
    protected $initialContext;

    /**
     * Initializes the the server with the parsed configuration file.
     *
     * @param \AppserverIo\Configuration\Interfaces\ConfigurationInterface $configuration The parsed configuration file
     */
    public function __construct(ConfigurationInterface $configuration)
    {

        // initialize the configuration and the base directory
        $systemConfiguration = new AppserverNode();
        $systemConfiguration->initFromConfiguration($configuration);
        $this->setSystemConfiguration($systemConfiguration);

        // initialize the server instance
        $this->init();
    }

    /**
     * Initialize the server instance.
     *
     * @return void
     */
    protected function init()
    {
        // init the umask to use creating files/directories
        $this->initUmask();
        // init initial context
        $this->initInitialContext();
        // init the file system
        $this->initFileSystem();
        // init main system logger
        $this->initLoggers();
        // init the SSL certificate
        $this->initSslCertificate();
        // init the extractor
        $this->initExtractors();
        // init the containers
        $this->initContainers();
    }

    /**
     * Init the umask to use creating files/directories.
     *
     * @throws \Exception
     *
     * @return void
     */
    protected function initUmask()
    {
        // don't do anything under Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return;
        }
        // set the configured umask to use
        umask($newUmask = $this->getSystemConfiguration()->getParam('umask'));
        if (umask() != $newUmask) { // check if set, throw an exception if not
            throw new \Exception("Can't set configured umask '$newUmask' found '" . umask() . "' instead");
        }
    }

    /**
     * Initialize the initial context instance.
     *
     * @return void
     */
    protected function initInitialContext()
    {
        $initialContextNode = $this->getSystemConfiguration()->getInitialContext();
        $reflectionClass = new \ReflectionClass($initialContextNode->getType());
        $initialContext = $reflectionClass->newInstanceArgs(
            array(
                $this->getSystemConfiguration()
            )
        );
        // set the initial context and flush it initially
        $this->setInitialContext($initialContext);
    }

    /**
     * Prepares filesystem to be sure that everything is on place as expected
     *
     * @return void
     * @throws \Exception Is thrown if a server directory can't be created
     */
    protected function initFileSystem()
    {

        // init API service to use
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');

        // load the directories
        $directories = $service->getDirectories();

        // check if the necessary directories already exists, if not, create them
        foreach (DirectoryKeys::getServerDirectoryKeysToBeCreated() as $directoryKey) {
            // prepare the path to the directory to be created
            $toBeCreated = $service->realpath($directories[$directoryKey]);

            // prepare the directory name and check if the directory already exists
            if (is_dir($toBeCreated) === false) {
                // if not, try to create it
                if (mkdir($toBeCreated, 0755, true) === false)  {
                    throw new \Exception(
                        sprintf('Can\'t create necessary directory %s while starting application server', $toBeCreated)
                    );
                }
            }
        }

        // check if specific directories has to be cleaned up on startup
        foreach (DirectoryKeys::getServerDirectoryKeysToBeCleanedUp() as $directoryKey) {
            // prepare the path to the directory to be cleaned up
            $toBeCleanedUp = $service->realpath($directories[$directoryKey]);

            // if the directory exists, clean it up
            if (is_dir($toBeCleanedUp)) {
                $service->cleanUpDir(new \SplFileInfo($toBeCleanedUp));
            }
        }
    }

    /**
     * Creates a new SSL certificate on first system start.
     *
     * @return void
     */
    protected function initSslCertificate()
    {
        // load the service instance and create the SSL file if not available
        /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
        $service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
        $service->createSslCertificate(new \SplFileInfo($service->getConfDir('/server.pem')));
    }

    /**
     * Initialize all loggers.
     *
     * @return void
     */
    protected function initLoggers()
    {

        // initialize the loggers
        $loggers = array();
        foreach ($this->getSystemConfiguration()->getLoggers() as $loggerNode) {
            $loggers[$loggerNode->getName()] = LoggerFactory::factory($loggerNode);
        }

        // set the initialized loggers finally
        $this->getInitialContext()->setLoggers($loggers);
        // @deprecated todo: refactor hard coded SystemLogger getter
        $this->getInitialContext()->setSystemLogger($loggers['System']);
    }

    /**
     * Initializes the extractor and extracts the web application
     * archives to their target folders.
     *
     * @return void
     */
    protected function initExtractors()
    {

        // add the configured extractors to the internal array
        foreach ($this->getSystemConfiguration()->getExtractors() as $extractorNode) {

            // initialize parameters for the constructor
            $params = array($this->getInitialContext(), $extractorNode);

            // create a new instance and add it to the internal array
            $this->addExtractor($this->newInstance($extractorNode->getType(), $params));
        }
    }

    /**
     * Initialize the container threads.
     *
     * @return void
     */
    protected function initContainers()
    {

        // and initialize a container thread for each container
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {

            // initialize the container configuration with the base directory and pass it to the thread
            $params = array($this->getInitialContext(), $containerNode);

            // create and append the thread instance to the internal array
            $this->addContainer($this->newInstance($containerNode->getType(), $params));
        }
    }

    /**
     * Adds the passed container to the server.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The container to add
     *
     * @return void
     */
    public function addContainer(ContainerInterface $container)
    {
        $this->containers[] = $container;
    }

    /**
     * Returns the running container threads.
     *
     * @return array Array with the running container threads
     */
    public function getContainers()
    {
        return $this->containers;
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

    /**
     * Returns the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger()
    {
        return $this->getInitialContext()->getSystemLogger();
    }

    /**
     * Adds the passed extractor to the server.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface $extractor The extractor instance to add
     *
     * @return void
     */
    public function addExtractor(ExtractorInterface $extractor)
    {
        $this->extractors[$extractor->getExtractorNode()->getName()] = $extractor;
    }

    /**
     * Returns all registered extractors.
     *
     * @return array The array with the extractors
     */
    public function getExtractors()
    {
        return $this->extractors;
    }

    /**
     * Start the container threads.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\Server::watch();
     */
    public function start()
    {

        // log that the server will be started now
        $this->getSystemLogger()->info(
            sprintf(
                'Now starting Server in basedirectory %s ',
                $this->getSystemConfiguration()->getBaseDirectory()
            )
        );

        // start the container threads
        $this->startContainers();

        // Switch to the configured user (if any)
        $this->switchProcessUser();

        // extract the application archives
        $this->extract();

        // deploy the applications
        $this->deploy();
    }

    /**
     * Profiles the server instance for memory usage and system load
     *
     * @return void
     */
    public function profile()
    {
        while (true) { // profile the server context
            if ($profileLogger = $this->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
                $profileLogger->debug('Successfully processed incoming connection');
            }
            sleep(2);
        }
    }

    /**
     * Scan's the deployment directory for changes and restarts
     * the server instance if necessary.
     *
     * This is an alternative method to call start() because the
     * monitor is running exclusively like the start() method.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\Server::start();
     */
    public function watch()
    {

        // load the initial context instance
        $initialContext = $this->getInitialContext();

        // initialize the default monitor for the deployment directory
        $scanners = array();

        // add the configured extractors to the internal array
        foreach ($this->getSystemConfiguration()->getScanners() as $scannerNode) {
            foreach ($scannerNode->getDirectories() as $directoryNode) {
                $scanners[] = ScannerFactory::factory($initialContext, $directoryNode, $scannerNode);
            }
        }

        // start all scanners
        foreach ($scanners as $scanner) {
            $scanner->start();
        }
    }

    /**
     * Starts the registered container threads.
     *
     * @return void
     */
    protected function startContainers()
    {

        // start the container threads
        foreach ($this->getContainers() as $container) {

            // start the container
            $container->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);

            // wait for the container to be started
            $waitForContainer = true;
            while ($waitForContainer) {
                sleep(1);
                if ($container->containerState->greaterOrEqualThan(ContainerStateKeys::get(ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL))) {
                    $waitForContainer = false;
                }
            }
        }
    }

    /**
     * This method will set the system user we might have configured as appserver params.
     * Will set the user and his group.
     *
     * @return void
     */
    protected function switchProcessUser()
    {
        // if we're on a OS (not Windows) that supports POSIX we have
        // to change the configured user/group for security reasons.
        if (!extension_loaded('posix')) {
            // log that we were not able to change the user
            $this->getSystemLogger()->info(
                "Could not change user due to missing POSIX extension"
            );
            return;
        }

        // init API service to use
        $containerService = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');

        // check for the existence of a user
        $user = $this->getSystemConfiguration()->getParam('user');
        $userChangeable = false;
        if (!empty($user)) {
            // get the user id, set it accordingly and check if it is usable for a user switch
            $userId = posix_getpwnam($user)['uid'];
            if (is_int($userId)) {
                // tell them that we are able to change the user
                $userChangeable = true;
            }
        }

        // check for the existence of a group
        $group = $this->getSystemConfiguration()->getParam('group');
        $groupChangeable = false;
        if (!empty($group)) {
            // get the user id, set it accordingly and check if it is usable for a group switch
            $groupId = posix_getgrnam($group)['gid'];
            if (is_int($groupId)) {
                // tell them we are able to change the group
                $groupChangeable = true;
            }
        }

        // do the actual file permission switching
        $containerService->setUserRights(new \SplFileInfo($containerService->getLogDir()));

        // As we should only change user and group AFTER we made all chown and chgrp
        // changes we will do it here after collecting if we are able to.

        // ATTENTION: We first need to change the group, because we need to be root
        //            to do that. After that we can change the user also!!!!!!!!!!!
        if ($groupChangeable) {
            // change the group ID
            posix_setgid($groupId);
        }
        if ($userChangeable) {
            // change the user ID
            posix_setuid($userId);
        }

        // log a message with the time needed for restart
        $this->getSystemLogger()->info(
            sprintf(
                "Changing process group and user to %s:%s",
                $user,
                $group
            )
        );
    }

    /**
     * Deploys the applications.
     *
     * @return void
     */
    protected function deploy()
    {

        // deploy the applications for all containers
        /** @var \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container */
        foreach ($this->getContainers() as $container) {

            // load the containers deployment
            $deployment = $container->getDeployment();
            $deployment->injectContainer($container);

            // deploy and initialize the container's applications
            $deployment->deploy();
        }
    }

    /**
     * Extracts the application archives to the configured document root.
     *
     * @return void
     */
    protected function extract()
    {

        // let the extractor extract the web applications
        /** @var \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface $extractor */
        foreach ($this->getExtractors() as $name => $extractor) {

            // deploy the found archives
            $extractor->deployWebapps();

            // log that the extractor has successfully been initialized and executed
            $this->getSystemLogger()->debug(sprintf('Extractor %s successfully initialized and executed', $name));
        }
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \AppserverIo\Appserver\Core\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
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
     * Will safely put the appserver to rest by cleaning up after the last run
     *
     * @return void
     */
    public function cleanup()
    {
        // We need to delete the heartbeat file as the watcher might restart the appserver otherwise
        unlink(
            $this->getSystemConfiguration()
                ->getBaseDirectory() . DIRECTORY_SEPARATOR .
            DirectoryKeys::VAR_RUN . DIRECTORY_SEPARATOR .
            HeartbeatScanner::HEARTBEAT_FILE_NAME
        );
    }

    /**
     * Starts giving the heartbeat to tell everyone we are alive.
     * This will keep your server in an endless loop, so be wary!
     *
     * @return void
     */
    protected function initHeartbeat()
    {
        while (true) {

            // Tell them we are alive
            touch(
                $this->getSystemConfiguration()
                    ->getBaseDirectory() . DIRECTORY_SEPARATOR .
                DirectoryKeys::VAR_RUN . DIRECTORY_SEPARATOR .
                HeartbeatScanner::HEARTBEAT_FILE_NAME
            );

            // Sleep a little
            sleep(1);
        }
    }
}
