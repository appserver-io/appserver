<?php

/**
 * \AppserverIo\Appserver\Core\AbstractContainerThread
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
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Naming\NamingDirectory;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Appserver\Core\Utilities\ContainerStateKeys;
use AppserverIo\Appserver\Core\Api\Node\ParamNode;
use AppserverIo\Server\Dictionaries\ServerStateKeys;

/**
 * Abstract container implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Naming\NamingDirectoryInterface         $namingDirectory Naming directory used for binding various information to
 * @property \AppserverIo\Appserver\Core\Api\AppService               $service         The app service used to bind applications to the configuration
 * @property \AppserverIo\Appserver\Core\Api\Node\ContainerNode       $containerNode   The container node information
 * @property \AppserverIo\Storage\GenericStackable                    $applications    The initialized applications
 * @property \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys $containerState  The actual container state
 */
abstract class AbstractContainerThread extends AbstractContextThread implements ContainerInterface
{

    /**
     * The time we wait after each loop.
     *
     * @var integer
     */
    const TIME_TO_LIVE = 1;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext         $initialContext  The initial context
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface   $namingDirectory The naming directory
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNode $containerNode   The container node
     */
    public function __construct($initialContext, $namingDirectory, $containerNode)
    {
        $this->initialContext = $initialContext;
        $this->namingDirectory = $namingDirectory;
        $this->containerNode = $containerNode;
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::WAITING_FOR_INITIALIZATION);
    }

    /**
     * Returns the unique container name from the configuration.
     *
     * @return string The unique container name
     * @see \AppserverIo\Appserver\Core\Interfaces\ContainerInterface::getName()
     */
    public function getName()
    {
        return $this->getContainerNode()->getName();
    }

    /**
     * Run the containers logic
     *
     * @return void
     */
    public function main()
    {

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // initialize the container state
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::WAITING_FOR_INITIALIZATION);

        // create a new API app service instance
        $this->service = $this->newService('AppserverIo\Appserver\Core\Api\AppService');

        // initialize the naming directory with the environment data
        $this->namingDirectory->bind('php:env/appBase', $this->getAppBase());
        $this->namingDirectory->bind('php:env/tmpDirectory', $this->getTmpDir());
        $this->namingDirectory->bind('php:env/baseDirectory', $this->getBaseDirectory());
        $this->namingDirectory->bind('php:env/umask', $this->getInitialContext()->getSystemConfiguration()->getUmask());
        $this->namingDirectory->bind('php:env/user', $this->getInitialContext()->getSystemConfiguration()->getUser());
        $this->namingDirectory->bind('php:env/group', $this->getInitialContext()->getSystemConfiguration()->getGroup());

        // initialize the container state
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::INITIALIZATION_SUCCESSFUL);

        // initialize instance that contains the applications
        $this->applications = new GenericStackable();

        // initialize the profile logger and the thread context
        if ($profileLogger = $this->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext($this->getContainerNode()->getName());
        }

        // initialize the array for the server configurations
        $serverConfigurations = array();

        // load the server configurations and query whether a server signatures has been set
        /** @var \AppserverIo\Appserver\Core\Api\Node\ServerNodeInterface $serverNode */
        foreach ($this->getContainerNode()->getServers() as $serverNode) {
            // query whether a server signature (software) has been configured
            if ($serverNode->getParam('software') == null) {
                $serverNode->setParam('software', ParamNode::TYPE_STRING, $this->getService()->getServerSignature());
            }

            // add the server node configuration
            $serverConfigurations[] = new ServerNodeConfiguration($serverNode);
        }

        // init upstreams
        $upstreams = array();
        if ($this->getContainerNode()->getUpstreams()) {
            $upstreams = $this->getContainerNode()->getUpstreams();
            foreach ($this->getContainerNode()->getUpstreams() as $upstreamNode) {
                // get upstream type
                $upstreamType = $upstreamNode->getType();
                // init upstream instance
                $upstream = new $upstreamType();
                // init upstream servers
                $servers = array();
                // get upstream servers from upstream
                foreach ($upstreamNode->getUpstreamServers() as $upstreamServerNode) {
                    $upstreamServerType = $upstreamServerNode->getType();
                    $upstreamServerParams = $upstreamServerNode->getParamsAsArray();
                    $servers[$upstreamServerNode->getName()] = new $upstreamServerType($upstreamServerParams);
                }
                // inject server instances to upstream
                $upstream->injectServers($servers);
                // set upstream by name
                $upstreams[$upstreamNode->getName()] = $upstream;
            }
        }

        // init server array
        $servers = array();
        // start servers by given configurations
        /** @var \AppserverIo\Server\Interfaces\ServerConfigurationInterface $serverConfig */
        foreach ($serverConfigurations as $serverConfig) {
            // get type definitions
            $serverType = $serverConfig->getType();
            $serverContextType = $serverConfig->getServerContextType();

            // create a new instance server context
            /** @var \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext */
            $serverContext = new $serverContextType();

            // inject container to be available in specific mods etc. and initialize the module
            $serverContext->injectContainer($this);

            // init server context by config
            $serverContext->init($serverConfig);

            // inject upstreams
            $serverContext->injectUpstreams($upstreams);

            // inject loggers
            $serverContext->injectLoggers($this->getInitialContext()->getLoggers());

            // Create the server (which should start it automatically)
            $server = new $serverType($serverContext);
            // Collect the servers we started
            $servers[] = $server;
        }

        // wait for all servers to be started
        $waitForServers = true;
        while ($waitForServers === true) {
            // iterate over all servers to check the state
            foreach ($servers as $server) {
                if ($server->serverState === ServerStateKeys::SERVER_SOCKET_STARTED) {
                    $waitForServers = false;
                } else {
                    $waitForServers = true;
                }
            }
            usleep(10000);
        }

        // the container has successfully been initialized
        $this->synchronized(function ($self) {
            $self->containerState = ContainerStateKeys::get(ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL);
        }, $this);

        // initialize the flag to keep the application running
        $keepRunning = true;

        // wait till container will be shutdown
        while ($keepRunning) {
            // query whether we've a profile logger, log resource usage
            if ($profileLogger) {
                $profileLogger->debug(sprintf('Container %s still waiting for shutdown', $this->getContainerNode()->getName()));
            }

            // wait a second to lower system load
            $keepRunning = $this->synchronized(function ($self) {
                $self->wait(1000000 * AbstractContainerThread::TIME_TO_LIVE);
                return $self->containerState->equals(ContainerStateKeys::get(ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL));
            }, $this);
        }

        // we need to stop all servers before we can shutdown the container
        /** @var \AppserverIo\Server\Interfaces\ServerInterface $server */
        foreach ($servers as $server) {
            $server->stop();
        }

        // mark the container as successfully shutdown
        $this->synchronized(function ($self) {
            $self->containerState = ContainerStateKeys::get(ContainerStateKeys::SHUTDOWN);
        }, $this);

        // send log message that the container has been shutdown
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf('Successfully shutdown container %s', $this->getContainerNode()->getName())
        );
    }

    /**
     * Returns the containers naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The containers naming directory
     */
    public function getNamingDirectory()
    {
        return $this->namingDirectory;
    }

    /**
     * Returns the deployed applications.
     *
     * @return \AppserverIo\Storage\GenericStackable The with applications
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Returns the application instance with the passed name.
     *
     * @param string $name The name of the application to return
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication($name)
    {
        if (isset($this->applications[$name])) {
            return $this->applications[$name];
        }
    }

    /**
     * Returns the containers configuration node.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNode The configuration node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the service instance we need to handle system configuration tasks.
     *
     * @return \AppserverIo\Appserver\Core\Api\AppService The service instance we need
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ContextInterface The initial context instance
     * @see \AppserverIo\Appserver\Core\Interfaces\ContainerInterface::getInitialContext()
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * (non-PHPdoc)
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
     * (non-PHPdoc)
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
     * Returns the deployment interface for the container for
     * this container thread.
     *
     * @return \AppserverIo\Psr\Deployment\DeploymentInterface The deployment instance for this container thread
     */
    public function getDeployment()
    {
        return $this->newInstance($this->getContainerNode()->getDeployment()->getType());
    }

    /**
     * (non-PHPdoc)
     *
     * @param string|null $directoryToAppend Append this directory to the base directory before returning it
     *
     * @return string The base directory
     * @see \AppserverIo\Appserver\Core\Api\ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->getService()->getBaseDirectory($directoryToAppend);
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The application base directory for this container
     * @see \AppserverIo\Appserver\Core\Api\ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->getBaseDirectory($this->getContainerNode()->getHost()->getAppBase());
    }

    /**
     * Returns the servers tmp directory, append with the passed directory.
     *
     * @param string|null $directoryToAppend The directory to append
     *
     * @return string
     */
    public function getTmpDir($directoryToAppend = null)
    {
        return $this->getService()->getTmpDir($directoryToAppend);
    }

    /**
     * Connects the passed application to the system configuration.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to be prepared
     *
     * @return void
     */
    public function addApplicationToSystemConfiguration(ApplicationInterface $application)
    {

        // try to load the API app service instance
        $appNode = $this->getService()->loadByWebappPath($application->getWebappPath());

        // check if the application has already been attached to the container
        if ($appNode == null) {
            $this->getService()->newFromApplication($application);
        }

        // connect the application to the container
        $application->connect();
    }

    /**
     * Append the deployed application to the deployment instance
     * and registers it in the system configuration.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to append
     *
     * @return void
     */
    public function addApplication(ApplicationInterface $application)
    {

        // register the application in this instance
        $this->applications[$application->getName()] = $application;

        // adds the application to the system configuration
        $this->addApplicationToSystemConfiguration($application);
    }

    /**
     * Stops the container instance.
     *
     * @return void
     */
    public function stop()
    {

        // start container shutdown
        $this->synchronized(function ($self) {
            $self->containerState = ContainerStateKeys::get(ContainerStateKeys::HALT);
        }, $this);

        do {
            // wait for 0.5 seconds
            usleep(500000);

            // log a message that we'll wait till application has been shutdown
            $this->getInitialContext()->getSystemLogger()->info(
                sprintf('Wait for container %s to be shutdown', $this->getContainerNode()->getName())
            );

            // query whether application state key is SHUTDOWN or not
            $waitForShutdown = $this->synchronized(function ($self) {
                return $self->containerState->notEquals(ContainerStateKeys::get(ContainerStateKeys::SHUTDOWN));
            }, $this);

        } while ($waitForShutdown);
    }

    /**
     * Returns boolean wheather the servers has been started yet or not
     * 
     * @return boolean
     */
    public function hasServersStarted() {
        return $this->containerState->equals(ContainerStateKeys::get(ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL));
    }

    /**
     * Does shutdown logic for request handler if something went wrong and
     * produces a fatal error for example.
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
                $this->getInitialContext()->getSystemLogger()->critical($message);
            }
        }
    }
}
