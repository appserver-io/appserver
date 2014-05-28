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

use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

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
     * The container's to be deployed.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The applications registered at this container
     *
     * @var array<\TechDivision\ApplicationServer\Interfaces\ApplicationInterface> $applications
     */
    protected $applications;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \TechDivision\ApplicationServer\InitialContext         $initialContext The initial context
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode  The container node
     */
    public function __construct($initialContext, $containerNode)
    {
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;
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

        // application deployment
        $deployment = $this->getDeployment();
        $deployment->deploy();

        // make applications available in container
        $this->applications = $deployment->getApplications();

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
            // This is used e.g. to wait for port opening or other important dependencies to proper server functionality
            $server->synchronized(
                function ($self) {
                    $self->wait();
                },
                $server
            );

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
                $this->getInitialContext(),
                $this->getContainerNode()
            )
        );
    }
}
