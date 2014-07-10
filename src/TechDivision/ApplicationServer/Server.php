<?php
/**
 * TechDivision\ApplicationServer\Server
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Extractors\PharExtractor;
use TechDivision\ApplicationServer\Interfaces\ProvisionerInterface;
use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\Api\Node\AppserverNode;
use TechDivision\ApplicationServer\Scanner\HeartbeatScanner;
use TechDivision\ApplicationServer\Utilities\StateKeys;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use \Psr\Log\LoggerInterface;

/**
 * This is the main server class that starts the application server
 * and creates a separate thread for each container found in the
 * configuration file.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class Server
{

    /**
     * Initialize the array for the running threads.
     *
     * @var array
     */
    protected $threads = array();

    /**
     * The system configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\NodeInterface
     */
    protected $systemConfiguration;

    /**
     * The servers initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The servers webapp extractor
     *
     * @var \TechDivision\ApplicationServer\Interfaces\ExtractorInterface
     */
    protected $extractor;

    /**
     * The servers provisioners.
     *
     * @var array
     */
    protected $provisioners = array();

    /**
     * Initializes the the server with the parsed configuration file.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration The parsed configuration file
     */
    public function __construct(Configuration $configuration)
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
     */
    protected function initFileSystem()
    {

        // init API service to use
        $service = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');

        // check if the log directory already exists, if not, create it
        foreach ($service->getDirectories() as $directory) {

            // prepare the path to the directory to be created
            $toBeCreated = $service->realpath($directory);
            // prepare the directory name and check if the directory already exists
            if (is_dir($toBeCreated) === false) {
                // if not create it
                mkdir($toBeCreated, 0755, true);
            }
        }
    }

    /**
     * Initialize all loggers.
     *
     * @return void
     */
    protected function initLoggers()
    {
        $loggers = array();
        foreach ($this->getSystemConfiguration()->getLoggers() as $loggerNode) {
            // initialize the processors
            $processors = array();
            foreach ($loggerNode->getProcessors() as $processorNode) {
                $processors[] = $this->newInstance($processorNode->getType(), $processorNode->getParamsAsArray());
            }

            // initialize the handlers
            $handlers = array();
            foreach ($loggerNode->getHandlers() as $handlerNode) {
                $handler = $this->newInstance($handlerNode->getType(), $handlerNode->getParamsAsArray());
                $formatterNode = $handlerNode->getFormatter();
                $handler->setFormatter(
                    $this->newInstance($formatterNode->getType(), $formatterNode->getParamsAsArray())
                );
                $handlers[] = $handler;
            }

            // initialize the logger instance itself
            $loggers[$loggerNode->getName()] = $this->newInstance(
                $loggerNode->getType(),
                array(
                    $loggerNode->getChannelName(),
                    $handlers,
                    $processors
                )
            );
        }
        // set the initialized loggers finally
        $this->getInitialContext()->setLoggers($loggers);
        // @deprecated todo: refactor hard coded SystemLogger getter
        $this->getInitialContext()->setSystemLogger($loggers['System']);
    }

    /**
     * Initializes the extractor.
     *
     * @return void
     */
    protected function initExtractor()
    {
        // @TODO: Read extractor type from configuration
        $this->setExtractor(new PharExtractor($this->getInitialContext()));
        // extract all webapps
        $this->getExtractor()->deployWebapps();
    }

    /**
     * Initializes the provisioners.
     *
     * @return void
     */
    protected function initProvisioners()
    {
        // @TODO: Read provisioner type from configuration
        $this->addProvisioner(new DatasourceProvisioner($this->getInitialContext()));
        $this->addProvisioner(new StandardProvisioner($this->getInitialContext()));

        // invoke the provisioners
        foreach ($this->getProvisioners() as $provisioner) {
            $provisioner->provision();
        }
    }

    /**
     * Initialize the container threads.
     *
     * @return void
     */
    protected function initContainers()
    {

        // initialize the array for the threads
        $this->threads = array();

        // and initialize a container thread for each container
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {

            // initialize the container configuration with the base directory and pass it to the thread
            $params = array($this->getInitialContext(), $containerNode);

            // create and append the thread instance to the internal array
            $this->threads[] = $this->newInstance($containerNode->getType(), $params);
        }
    }

    /**
     * Returns the running container threads.
     *
     * @return array Array with the running container threads
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * Set's the system configuration.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $systemConfiguration The system configuration object
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function setSystemConfiguration(NodeInterface $systemConfiguration)
    {
        return $this->systemConfiguration = $systemConfiguration;
    }

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->systemConfiguration;
    }

    /**
     * Set's the initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext The initial context instance
     *
     * @return void
     */
    public function setInitialContext(InitialContext $initialContext)
    {
        return $this->initialContext = $initialContext;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
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
     * Sets the extractor
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ExtractorInterface $extractor The initial context instance
     *
     * @return void
     */
    public function setExtractor(ExtractorInterface $extractor)
    {
        return $this->extractor = $extractor;
    }

    /**
     * Returns the extractor
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ExtractorInterface The extractor instance
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * Sets the provisioner.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ProvisionerInterface $provisioner The initial context instance
     *
     * @return array
     */
    public function addProvisioner(ProvisionerInterface $provisioner)
    {
        return $this->provisioners[] = $provisioner;
    }

    /**
     * Returns the provisioners.
     *
     * @return array The provisioners
     */
    public function getProvisioners()
    {
        return $this->provisioners;
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
                ->getBaseDirectory()
                ->getNodeValue()
                ->__toString() . DIRECTORY_SEPARATOR .
            DirectoryKeys::RUN . DIRECTORY_SEPARATOR .
            HeartbeatScanner::HEARTBEAT_FILE_NAME
        );
    }

    /**
     * Start the container threads.
     *
     * @return void
     * @see \TechDivision\ApplicationServer\Server::watch();
     */
    public function start()
    {
        // init the extractor
        $this->initExtractor();

        // init the provisioner
        $this->initProvisioners();

        // init the containers
        $this->initContainers();

        // log that the server will be started now
        $this->getSystemLogger()->info(
            sprintf(
                'Server successfully started in basedirectory %s ',
                $this->getSystemConfiguration()
                    ->getBaseDirectory()
                    ->getNodeValue()
                    ->__toString()
            )
        );

        // start the container threads
        $this->startContainers();

        // Switch to the configured user (if any)
        $this->initProcessUser();
    }

    /**
     * Starts giving the heartbeat to tell everyone we are alive.
     * This will keep your server in an endless loop, so be wary!
     *
     * @return void
     *
     * @TODO integrate this into a maintenance layer
     */
    protected function initHeartbeat()
    {
        while (true) {

            // Tell them we are alive
            touch(
                $this->getSystemConfiguration()
                    ->getBaseDirectory()
                    ->getNodeValue()
                    ->__toString() . DIRECTORY_SEPARATOR .
                DirectoryKeys::RUN . DIRECTORY_SEPARATOR .
                HeartbeatScanner::HEARTBEAT_FILE_NAME
            );

            // Sleep a little
            sleep(1);
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
     * @see \TechDivision\ApplicationServer\Server::start();
     */
    public function watch()
    {

        // initialize the default monitor for the deployment directory
        $monitors = array();

        // Add a deployment scanner
        $monitors[] = $this->newInstance(
            'TechDivision\ApplicationServer\Scanner\DeploymentScanner',
            array($this->getInitialContext())
        );

        // Start all monitors
        foreach ($monitors as $monitor) {

            $monitor->start();
        }
    }

    /**
     * Starts the registered container threads.
     *
     * @return void
     */
    public function startContainers()
    {
        // set the flag that the application will be started
        $this->getInitialContext()->setAttribute(StateKeys::KEY, StateKeys::get(StateKeys::STARTING));

        // start the container threads
        foreach ($this->getThreads() as $thread) {

            // start the thread
            $thread->start();

            // synchronize container threads to avoid registering apps several times
            $thread->synchronized(
                function ($self) {
                    $self->wait();
                },
                $thread
            );
        }

        // set the flag that the application has been started
        $this->getInitialContext()->setAttribute(StateKeys::KEY, StateKeys::get(StateKeys::RUNNING));
    }

    /**
     * This method will set the system user we might have configured as appserver params.
     * Will set the user and his group.
     *
     * @return void
     */
    protected function initProcessUser()
    {
        // if we're on a OS (not Windows) that supports POSIX we have
        // to change the configured user/group for security reasons.
        if (!extension_loaded('posix')) {

            // Log that we were not able to change the user
            $this->getSystemLogger()->info(
                "Could not change user due to missing POSIX extension"
            );
            return;
        }

        // init API service to use
        $service = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');

        // Check for the existence of a user
        $user = $this->getSystemConfiguration()->getParam('user');
        $userChangeable = false;
        if (!empty($user)) {

            // Get the user id and set it accordingly
            $userId = posix_getpwnam($user)['uid'];

            // Did we get something useful?
            if (is_int($userId)) {

                // check if deploy dir exists
                if (is_dir(new \DirectoryIterator($logDir = $service->getLogDir()))) {
                    // init file iterator on deployment directory
                    $fileIterator = new \FilesystemIterator($logDir);
                    // Iterate through all phar files and extract them to tmp dir
                    foreach (new \RegexIterator($fileIterator, '/^.*\\.log$/') as $logFile) {
                        chown($logFile, $userId);
                    }
                }

                // Tell them that we are able to change the user
                $userChangeable = true;
            }
        }

        // Check for the existence of a group
        $group = $this->getSystemConfiguration()->getParam('group');
        $groupChangeable = false;
        if (!empty($group)) {

            // Get the user id and set it accordingly
            $groupId = posix_getgrnam($group)['gid'];

            // Did we get something useful?
            if (is_int($groupId)) {

                // check if deploy dir exists
                if (is_dir(new \DirectoryIterator($logDir = $service->getLogDir()))) {
                    // init file iterator on deployment directory
                    $fileIterator = new \FilesystemIterator($logDir);
                    // Iterate through all phar files and extract them to tmp dir
                    foreach (new \RegexIterator($fileIterator, '/^.*\\.log$/') as $logFile) {
                        chgrp($logFile, $groupId);
                    }
                }

                // Tell them we are able to change the group
                $groupChangeable = true;
            }
        }

        // As we should only change user and group AFTER we made all chown and chgrp changes we will do it here
        // after collecting if we are able to
        if ($userChangeable) {

            // change the user ID
            posix_setuid($userId);
        }
        if ($groupChangeable) {

            // change the group ID
            posix_setgid($groupId);
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
     * Stops the appserver by setting the appropriate flag in the
     * initial context.
     *
     * @return void
     */
    public function stopContainers()
    {

        // calculate the start time
        $start = microtime(true);

        // set the flag that the application has to be stopped
        $this->getInitialContext()->setAttribute(StateKeys::KEY, StateKeys::get(StateKeys::STOPPING));

        // log a message with the time needed for restart
        $this->getSystemLogger()->info(
            sprintf(
                "Successfully stopped appserver (in %d sec)",
                microtime(true) - $start
            )
        );
    }

    /**
     * Redeploys the apps and restarts the appserver.
     *
     * @return void
     */
    public function restartContainers()
    {

        // log a message that the appserver will be restarted now
        $this->getSystemLogger()->info('Now restarting appserver');

        // calculate the start time
        $start = microtime(true);

        // stop the container threads
        $this->stopContainers();

        // check if apps has to be redeployed
        $this->getExtractor()->deployWebapps();

        // reinitialize the container threads
        $this->initContainers();

        // start the container threads
        $this->startContainers();

        // log a message with the time needed for restart
        $this->getSystemLogger()->info(
            sprintf(
                "Successfully restarted appserver (in %d sec)",
                microtime(true) - $start
            )
        );
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
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
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }
}
