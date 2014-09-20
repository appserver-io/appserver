<?php

/**
 * TechDivision\ApplicationServer\AbstractContainerThread
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jz@techdivision.com>
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\ApplicationServer\Utilities\ContainerStateKeys;

/**
 * Class AbstractContainerThread
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @author    Johann Zelger <jz@techdivision.com>
 * @author    Bernhard Wick <b.wick@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
abstract class AbstractContainerThread extends AbstractContextThread implements ContainerInterface
{

    /**
     * The container node information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The initialized applications.
     *
     * @var \ArrayAccess $applications
     */
    protected $applications;

    /**
     * The actual container state.
     *
     * @var \TechDivision\ApplicationServer\Utilities\ContainerStateKeys
     */
    protected $containerState;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \TechDivision\ApplicationServer\InitialContext         $initialContext The initial context
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode  The container node
     */
    public function __construct($initialContext, $containerNode)
    {

        // initialize the container state
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::WAITING_FOR_INITIALIZATION);

        // initialize the initial context + the container node
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;

        // initialize instance that contains the applications
        $this->applications = new GenericStackable();

        // create a new API app service instance
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\AppService');

        // initialization has been successful
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::INITIALIZATION_SUCCESSFUL);
    }

    /**
     * Returns the receiver instance ready to be started.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function getReceiver()
    {
        // nothing
    }

    /**
     * Run the containers logic
     *
     * @return void
     */
    public function main()
    {

        // define webservers base dir
        define(
            'SERVER_BASEDIR',
            $this->getInitialContext()->getSystemConfiguration()->getBaseDirectory()->getNodeValue()->__toString()
            . DIRECTORY_SEPARATOR
        );

        // check if we've the old or the new directory structure
        if (file_exists(SERVER_BASEDIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
            $autoloaderFile = SERVER_BASEDIR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        } else { // this is the old directory structure
            $autoloaderFile = SERVER_BASEDIR . 'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
        }

        // define the autoloader file
        define('SERVER_AUTOLOADER', $autoloaderFile);

        // deploy and initialize the applications for this container
        $this->getDeployment()->deploy($this);

        // deployment has been successful
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::DEPLOYMENT_SUCCESSFUL);

        // setup configurations
        $serverConfigurations = array();
        foreach ($this->getContainerNode()->getServers() as $serverNode) {
            $serverConfigurations[] = new ServerNodeConfiguration($serverNode);
        }

        // init server array
        $servers = array();

        // start servers by given configurations
        foreach ($serverConfigurations as $serverConfig) {

            // get type definitions
            $serverType = $serverConfig->getType();
            $serverContextType = $serverConfig->getServerContextType();

            // create a new instance server context
            /* @var \TechDivision\WebServer\Interfaces\ServerContextInterface $serverContext */
            $serverContext = new $serverContextType();

            // inject container to be available in specific mods etc. and initialize the module
            $serverContext->injectContainer($this);
            $serverContext->init($serverConfig);

            $serverContext->injectLoggers($this->getInitialContext()->getLoggers());

            // Create the server (which should start it automatically)
            $server = new $serverType($serverContext);
            // Collect the servers we started
            $servers[] = $server;
        }

        // wait for all servers to be started
        $waitForServers = true;
        while ($waitForServers) {

            // iterate over all servers to check the state
            foreach ($servers as $server) {
                if ($server->state === 0) { // if the server has not been started
                    sleep(1);
                    continue 2;
                }
            }

            // if all servers has been started, stop waiting
            $waitForServers = false;
        }

        // the servers has been started and we wait for the servers to finish work now
        $this->containerState = ContainerStateKeys::get(ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL);

        // wait for shutdown signal
        while ($this->containerState->equals(ContainerStateKeys::get(ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL))) {
            sleep(1);
        }

        // wait till all servers has been shutdown
        foreach ($servers as $server) {
            $server->join();
        }
    }

    /**
     * Returns an array with the deployed applications.
     *
     * @return array The array with applications
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Return's the containers config node
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the service instance we need to handle systen configuration tasks.
     *
     * @return \TechDivision\ApplicationServer\Api\AppService The service instance we need
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Return's the initial context instance
     *
     * @return \TechDivision\ApplicationServer\InitialContext
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
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface The service instance
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
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
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * Returns the deployment interface for the container for
     * this container thread.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance for this container thread
     */
    public function getDeployment()
    {
        return $this->newInstance(
            $this->getContainerNode()->getDeployment()->getType(),
            array(
                $this->getInitialContext()
            )
        );
    }

    /**
     * (non-PHPdoc)
     *
     * @param string|null $directoryToAppend Append this directory to the base directory before returning it
     *
     * @return string The base directory
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->getService()->getBaseDirectory($directoryToAppend);
    }

    /**
     * (non-PHPdoc)
     *
     * @return string The application base directory for this container
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getAppBase()
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
    public function getTmpDir($directoryToAppend)
    {
        return $this->getService()->getTmpDir($directoryToAppend);
    }

    /**
     * Connects the passed application to the system configuration.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface $application The application to be prepared
     *
     * @return void
     */
    protected function addApplicationToSystemConfiguration(ApplicationInterface $application)
    {

        // try to load the API app service instance
        $appNode = $this->getService()->loadByWebappPath($application->getWebappPath());

        // check if the application has already been attached to the container
        if ($appNode == null) {
            $appNode = $this->getService()->newFromApplication($application);
        }

        // connect the application to the container
        $application->connect();
    }

    /**
     * Append the deployed application to the deployment instance
     * and registers it in the system configuration.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface $application The application to append
     *
     * @return void
     */
    protected function addApplication(ApplicationInterface $application)
    {

        // adds the application to the system configuration
        $this->addApplicationToSystemConfiguration($application);

        // register the application in this instance
        $this->applications[$application->getName()] = $application;

        // log a message that the app has been started
        $this->getInitialContext()->getSystemLogger()->debug(
            sprintf('Successfully initialized and deployed app %s', $application->getName())
        );
    }
}
