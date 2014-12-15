<?php
/**
 * AppserverIo\Appserver\Core\AbstractWorker
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

/**
 * The worker implementation that handles the request.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractWorker extends AbstractContextThread
{

    /**
     * Holds the container implementation
     *
     * @var ContainerInterface
     */
    public $container;

    /**
     * Holds the main socket resource
     *
     * @var resource
     */
    public $resource;

    /**
     * The thread implementation classname
     *
     * @var string
     */
    public $threadType;

    /**
     * Init acceptor with container and acceptable socket resource
     * and thread type class.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container  A container implementation
     * @param resource                                                  $resource   The client socket instance
     * @param string                                                    $threadType The thread type class to init
     *
     * @return void
     */
    public function init(ContainerInterface $container, $resource, $threadType)
    {
        $this->container = $container;
        $this->resource = $resource;
        $this->threadType = $threadType;
    }

    /**
     * Returns the resource class used to receive data over the socket.
     *
     * @return string
     */
    abstract protected function getResourceClass();

    /**
     * Returns the container instance.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ContainerInterface The container instance
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * The main function which will be called by doing start()
     *
     * @return void
     */
    public function main()
    {

        // handle requests as long as container has been started
        while ($this->getContainer()->isStarted()) {

            // reinitialize the server socket
            $serverSocket = $this->initialContext->newInstance($this->getResourceClass(), array(
                $this->resource
            ));

            // accept client connection and process the request
            if ($clientSocket = $serverSocket->accept()) {

                // prepare the request thread params
                $params = array(
                    $this->initialContext,
                    $this->container,
                    $clientSocket->getResource()
                );

                // process the request in a separate thread
                $request = $this->initialContext->newInstance($this->threadType, $params);
                $request->start();
            }
        }
    }
}
