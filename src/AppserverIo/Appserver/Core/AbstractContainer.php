<?php
/**
 * AppserverIo\Appserver\Core\AbstractContainer
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

/**
 * Class AbstractContainer
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractContainer extends \Stackable implements ContainerInterface
{

    /**
     * Array with deployed applications.
     *
     * @var array
     */
    protected $applications = array();

    /**
     * The server instance.
     *
     * @var \AppserverIo\Appserver\Core\Server
     */
    protected $server;

    /**
     * The container node.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The containers base directory.
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * The containers web application base directory.
     *
     * @var string
     */
    protected $webappsDir;

    /**
     * TRUE if the container has been started, else FALSE.
     *
     * @var boolean
     */
    protected $started = false;

    /**
     * @var InitialContext
     */
    protected $initialContext;

    /**
     * Initializes the container with the initial context, the unique container ID
     * and the deployed applications.
     *
     * @param \AppserverIo\Appserver\Core\InitialContext               $initialContext The initial context
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNode       $containerNode  The container's UUID
     * @param array<\AppserverIo\Psr\Application\ApplicationInterface> $applications   The application instance
     *
     * @todo Application deployment only works this way because of Thread compatibilty
     */
    public function __construct($initialContext, $containerNode, $applications)
    {
        $this->initialContext = $initialContext;
        $this->baseDirectory = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService')->getBaseDirectory();
        $this->webappsDir = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService')->getWebappsDir();
        $this->setContainerNode($containerNode);
        $this->setApplications($applications);
    }

    /**
     * Set's the app node the application is belonging to
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNode $containerNode The app node the application is belonging to
     *
     * @return void
     */
    public function setContainerNode($containerNode)
    {
        $this->containerNode = $containerNode;
    }

    /**
     * Return's the container node.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNode The container node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * run
     *
     * @return void
     * @see \Stackable::run()
     */
    public function run()
    {
        $this->setStarted();
        $this->getReceiver()->start();
    }

    /**
     * Returns the receiver instance ready to be started.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ReceiverInterface The receiver instance
     * @see \AppserverIo\Appserver\Core\Interfaces\ContainerInterface::getReceiver()
     */
    public function getReceiver()
    {
        return $this->newInstance($this->getReceiverType(), array(
            $this->initialContext,
            $this
        ));
    }

    /**
     * Sets an array with the deployed applications.
     *
     * @param array $applications Array with the deployed applications
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ContainerInterface The container instance itself
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;
        return $this;
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
     * Return's the class name of the container's receiver type.
     *
     * @return string The class name of the container's receiver type
     */
    public function getReceiverType()
    {
        return $this->getContainerNode()
            ->getReceiver()
            ->getType();
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className The fully qualified class name to return the instance for
     * @param array  $args      Arguments to pass to the constructor of the instance
     *
     * @return object The instance itself
     *
     * @see \AppserverIo\Appserver\Core\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->initialContext->newInstance($className, $args);
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
     * Returns the inital context instance.
     *
     * @return \AppserverIo\Appserver\Core\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Marks the container as started.
     *
     * @return void
     */
    public function setStarted()
    {
        $this->started = true;
    }

    /**
     * Returns TRUE if the container has been started, else FALSE.
     *
     * @return boolean TRUE if the container has been started, else FALSE
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Returns the containers web application base directory.
     *
     * @return string The containers web application base directory
     * @see \AppserverIo\Appserver\Core\Api\ContainerService::getWebappsDir()
     */
    public function getWebappsDir()
    {
        return $this->webappsDir;
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
        if ($directoryToAppend != null) {
            return $this->baseDirectory . $directoryToAppend;
        }
        return $this->baseDirectory;
    }
}
