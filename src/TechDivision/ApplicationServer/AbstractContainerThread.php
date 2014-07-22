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
     * Default timeout for the container when waiting on a single(!) server.
     * Time is in microseconds, so 1000000 => 1 second.
     *
     * @const integer
     */
    const DEFAULT_WAIT_TIMEOUT = 1000000;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \TechDivision\ApplicationServer\InitialContext         $initialContext The initial context
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode  The container node
     */
    public function __construct($initialContext, $containerNode)
    {

        // initialize the initial context + the container node
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;

        // initialize instance that contains the applications
        $this->applications = new GenericStackable();

        // create a new API app service instance
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\AppService');
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
        define(
            'SERVER_AUTOLOADER',
            SERVER_BASEDIR .
            'app' . DIRECTORY_SEPARATOR . 'code' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php'
        );

        // deploy and initialize the applications for this container
        $this->getDeployment()->deploy($this);

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

            // Synchronize the server so we can wait until preparation of the server finished.
            // This is used e.g. to wait for port opening or other important dependencies to proper server functionality.
            // We will also check if the wait timed out our if the server notified as it should. If not we will return false
            $gotNotified = $server->synchronized(
                function ($server, $timeout) {
                    $startedWaiting = microtime(true);
                    $server->wait($timeout);

                    // Did the waiting take longer as the timeout? If so we definitely ran into it.
                    // Don't forget to convert the units
                    if (microtime(true) - $startedWaiting > ($timeout / 1000000)) {

                        return false;
                    }

                    // Return true otherwise
                    return true;
                },
                $server,
                $this->getWaitTimeout()
            );

            // If we did not get notified we have to tell the user
            if (!$gotNotified) {

                // Log the issue
                $this->getInitialContext()->getSystemLogger()->error(
                    sprintf(
                        'The server at %s did not notify for a ready state in time! It might be unavailable.',
                        $serverConfig->getAddress() . ':' . $serverConfig->getPort()
                    )
                );
            }
        }
        // We have to notify the logical parent thread, the appserver, as it has to
        // know the port has been opened
        $this->synchronized(
            function () {
                $this->notify();
            }
        );

        /*
         * IMPORTANT: This is necessary to allow access of stackables
         * 	          inside of applications.
         *
         * @author: Tim Wagner
         * @date:   2014-05-28
         */
        foreach ($servers as $server) {
            $server->join();
        }
    }

    /**
     * Will return the wait timeout currently in use.
     *
     * @return integer
     *
     * @todo make this look up the appserver config as well if it might be needed in the future (longer server boot, etc)
     */
    public function getWaitTimeout()
    {
        return self::DEFAULT_WAIT_TIMEOUT;
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
            sprintf('Successfully initialized and deployed app', $application->getName())
        );
    }
}
