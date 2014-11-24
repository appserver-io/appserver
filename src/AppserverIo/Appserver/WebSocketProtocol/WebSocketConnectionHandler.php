<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\WebSocketConnectionHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage WebSocketProtocol
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 * @link       http://ca.php.net/manual/en/ref.http.php
 * @link       http://dev.w3.org/html5/websockets
 */

namespace AppserverIo\Appserver\WebSocketProtocol;

use Ratchet\Http\HttpRequestParser;
use Ratchet\WebSocket\WsServer;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\WebSocket\Version;
use Ratchet\WebSocket\Encoding\ToggleableValidator;
use Ratchet\WebSocket\VersionManager;
use Ratchet\WebSocket\Version\RFC6455;
use Ratchet\WebSocket\Version\HyBi10;
use Ratchet\WebSocket\Version\Hixie76;
use Guzzle\Http\Message\Response;
use Guzzle\Http\Message\RequestInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;

/**
 * The adapter to handle WebSocket requests/responses.
 *
 * This is a mediator between the Server and the applications provided by
 * the container to handle real-time messaging through a web browser.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage WebSocketProtocol
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 * @link       http://ca.php.net/manual/en/ref.http.php
 * @link       http://dev.w3.org/html5/websockets
 */
class WebSocketConnectionHandler implements MessageComponentInterface
{

    /**
     * Buffers incoming HTTP requests returning a Guzzle Request when coalesced.
     *
     * @var HttpRequestParser @note May not expose this in the future, may do through facade methods
     */
    public $reqParser;

    /**
     * Manage the various WebSocket versions to support.
     *
     * @var VersionManager @note May not expose this in the future, may do through facade methods
     */
    public $versioner;

    /**
     * Array with the applications to handle.
     *
     * @var array
     */
    protected $applications;

    /**
     * Storage for the connections.
     *
     * @var \SplObjectStorage
     */
    protected $connections;

    /**
     * For now, array_push accepted subprotocols to this array.
     *
     * @deprecated @temporary
     */
    protected $acceptedSubProtocols = array();

    /**
     * UTF-8 validator.
     *
     * @var \Ratchet\WebSocket\Encoding\ValidatorInterface
     */
    protected $validator;

    /**
     * Flag if we have checked the decorated component for sub-protocols.
     *
     * @var boolean
     */
    private $isSpGenerated = false;

    /**
     * The server context instance.
     *
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Holds an array of modules to use for connection handler.
     *
     * @var array
     */
    protected $modules;

    /**
     * Initialize the web socket server with the container's applications.
     *
     * @param array $applications The initialized applications
     */
    public function __construct(&$applications)
    {
        // initialize the web socket server instance
        $this->reqParser = new HttpRequestParser();
        $this->versioner = new VersionManager();
        $this->validator = new ToggleableValidator();

        // enable the allowed web socket versions
        $this->versioner->enableVersion(new Version\RFC6455($this->validator))
            ->enableVersion(new Version\HyBi10($this->validator))
            ->enableVersion(new Version\Hixie76());

        // initialize connection pool and applications
        $this->connections = new \SplObjectStorage();
        $this->applications = &$applications;
    }

    /**
     * Inits the connection handler by given context and params
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The servers context
     * @param array                                                 $params        The params for connection handler
     *
     * @return void
     */
    public function init(ServerContextInterface $serverContext, array $params = null)
    {

        // set server context
        $this->serverContext = $serverContext;

        // register shutdown handler
        register_shutdown_function(array(
            &$this,
            "shutdown"
        ));
    }

    /**
     * Does shutdown logic for worker if something breaks in process.
     *
     * @return void
     */
    public function shutdown()
    {
        // do nothing here
    }

    /**
     * Injects all needed modules for connection handler to process
     *
     * @param array $modules An array of modules
     *
     * @return void
     */
    public function injectModules($modules)
    {
        $this->modules = $modules;
    }

    /**
     * Returns all needed modules as array for connection handler to process
     *
     * @return array An array of Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Returns the worker instance which starte this worker thread
     *
     * @return \AppserverIo\Server\Interfaces\WorkerInterface
     */
    protected function getWorker()
    {
        return $this->worker;
    }

    /**
     * This method will be invoked after the connection has been established.
     *
     * @param \Ratchet\ConnectionInterface $conn The connection that has been established
     *
     * @return void
     * @see \Ratchet\ComponentInterface::onOpen()
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $conn->WebSocket = new \stdClass();
        $conn->WebSocket->established = false;
    }

    /**
     * This method will be invoked when a new message, that has to be processed,
     * came in.
     *
     * @param \Ratchet\ConnectionInterface $from The connection that has been established
     * @param string                       $msg  The message itself
     *
     * @return void
     * @see \Ratchet\MessageInterface::onMessage()
     */
    public function onMessage(ConnectionInterface $from, $msg)
    {
        if (true === $from->WebSocket->established) {
            return $from->WebSocket->version->onMessage($this->connections[$from], $msg);
        }

        if (isset($from->WebSocket->request)) {
            $from->WebSocket->request->getBody()->write($msg);
        } else {

            try {
                if (null === ($request = $this->reqParser->onMessage($from, $msg))) {
                    return;
                }
            } catch (\OverflowException $oe) {
                return $this->close($from, 413);
            }

            if (!$this->versioner->isVersionEnabled($request)) {
                return $this->close($from);
            }

            $from->WebSocket->request = $request;
            $from->WebSocket->version = $this->versioner->getVersion($request);
        }

        try {
            $response = $from->WebSocket->version->handshake($from->WebSocket->request);
        } catch (\UnderflowException $e) {
            return;
        }

        if (null !== ($subHeader = $from->WebSocket->request->getHeader('Sec-WebSocket-Protocol'))) {
            if ('' !== ($agreedSubProtocols = $this->getSubProtocolString($subHeader->normalize()))) {
                $response->setHeader('Sec-WebSocket-Protocol', $agreedSubProtocols);
            }
        }

        $response->setHeader('X-Powered-By', \Ratchet\VERSION);
        $from->send((string) $response);

        if (101 != $response->getStatusCode()) {
            return $from->close();
        }

        // locate handler and initialize it
        $handler = $this->locateHandler($request);
        $upgraded = $from->WebSocket->version->upgradeConnection($from, $handler);
        $this->connections->attach($from, $upgraded);
        $upgraded->WebSocket->established = true;
        return $handler->onOpen($upgraded);
    }

    /**
     * Locates the web socket handler for the passed request.
     *
     * @param \Guzzle\Http\Message\RequestInterface $guzzleRequest The request to find and return the application instance for
     *
     * @return \Ratchet\MessageComponentInterface The handler instance
     */
    public function locateHandler(RequestInterface $guzzleRequest)
    {

        // initialize a new web socket request
        $request = new WebSocketRequest();
        $request->injectRequest($guzzleRequest);

        // load the application
        $application = $this->findApplication($request);

        // register the applications class loader
        $application->registerClassLoaders();

        // load the handler
        $handler = $application->getManager(HandlerContext::IDENTIFIER)->locate($request);
        $handler->injectRequest($request);

        // return the initialized handler instance
        return $handler;
    }

    /**
     * Tries to find and return the application for the passed request.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request The request to find and return the application instance for
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     * @throws \AppserverIo\Appserver\WebSocketProtocol\BadRequestException Is thrown if no application can be found for the passed application name
     */
    public function findApplication(RequestInterface $request)
    {

        // load the path information and the server name
        $host = $request->getHost();
        $pathInfo = $request->getPath();

        // initialize the handler path
        $request->setHandlerPath($request->getPath());

        // strip the leading slash and explode the application name
        list ($applicationName, ) = explode('/', substr($pathInfo, 1));

        // if not, check if the request matches a folder
        if (array_key_exists($applicationName, $this->applications)) {

            // load the application from the array
            $application = $this->applications[$applicationName];

        } else { // iterate over the applications and check if one of the virtual hosts match the request

            foreach ($this->applications as $application) {
                if ($application->isVhostOf($host)) {
                    break;
                }
            }
        }

        // if not throw an exception if we can't find an application
        if ($application == null) {
            throw new BadRequestException("Can't find application for '$applicationName'");
        }

        // prepare and set the applications context path
        $request->setContextPath($contextPath = '/' . $application->getName());

        // prepare the path information depending if we're in a vhost or not
        if ($application->isVhostOf($host) === false) {
            $request->setHandlerPath(str_replace($contextPath, '', $request->getHandlerPath()));
        }

        // inject the application context into the handler request
        $request->injectContext($application);

        // return, because request has been prepared successfully
        return $application;
    }

    /**
     * This method will be invoked before the connection will be closed.
     *
     * @param \Ratchet\ConnectionInterface $conn The connection that will be closed
     *
     * @return void
     * @see \Ratchet\ComponentInterface::onClose()
     */
    public function onClose(ConnectionInterface $conn)
    {
        if ($this->connections->contains($conn)) {
            $decor = $this->connections[$conn];
            $this->connections->detach($conn);
            foreach ($this->applications as $application) {
                foreach ($application->getManager(HandlerContext::IDENTIFIER)->getHandlers() as $handler) {
                    $handler->onClose($decor);
                }
            }
        }
    }

    /**
     * This method will be invoked if an error on a connection has been occured.
     *
     * @param \Ratchet\ConnectionInterface $conn The connection throwing an error
     * @param \Exception                   $e    The exception that has been thrown
     *
     * @return void
     * @see \Ratchet\ComponentInterface::onError()
     */
    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        if ($conn->WebSocket->established) {
            foreach ($this->applications as $application) {
                foreach ($application->getManager(HandlerContext::IDENTIFIER)->getHandlers() as $handler) {
                    $handler->onError($this->connections[$conn], $e);
                }
            }
        } else {
            $conn->close();
        }
    }

    /**
     * Disable a specific version of the WebSocket protocol
     *
     * @param integer $versionId Version ID to disable
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\WebSocketConnectionHandler The handler itself
     */
    public function disableVersion($versionId)
    {
        $this->versioner->disableVersion($versionId);
        return $this;
    }

    /**
     * Toggle weather to check encoding of incoming messages
     *
     * @param boolean $opt TRUE if encoding has to be checked, else FALSE
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\WebSocketConnectionHandler The handler itself
     */
    public function setEncodingChecks($opt)
    {
        $this->validator->on = (boolean) $opt;
        return $this;
    }

    /**
     * This method checks if the sub protocol with the passed name
     * is supported by this connection handler.
     *
     * @param string $name The sub protocol name to be checked
     *
     * @return boolean TRUE if the sub protocol is supported, else FALSE
     */
    public function isSubProtocolSupported($name)
    {
        if ($this->isSpGenerated === false) {
            foreach ($this->applications as $application) {
                foreach ($application->getManager(HandlerContext::IDENTIFIER)->getHandlers() as $handler) {
                    if ($this->_decorating instanceof WsServerInterface) {
                        $this->acceptedSubProtocols = array_merge($this->acceptedSubProtocols, array_flip($handler->getSubProtocols()));
                    }
                }
            }
            $this->isSpGenerated = true;
        }
        return array_key_exists($name, $this->acceptedSubProtocols);
    }

    /**
     * Returns the sub protocol, if supported, as string.
     *
     * @param \Traversable|null $requested The list with sub protocols
     *
     * @return string The sub protocol name
     */
    protected function getSubProtocolString(\Traversable $requested = null)
    {
        if (null === $requested) {
            return '';
        }

        $result = array();

        foreach ($requested as $sub) {
            if ($this->isSubProtocolSupported($sub)) {
                $result[] = $sub;
            }
        }

        return implode(',', $result);
    }

    /**
     * Close a connection with an HTTP response.
     *
     * @param \Ratchet\ConnectionInterface $conn The connection to be closed
     * @param integer                      $code HTTP status code
     *
     * @return void
     */
    protected function close(ConnectionInterface $conn, $code = 400)
    {
        $response = new Response($code, array(
            'Sec-WebSocket-Version' => $this->versioner->getSupportedVersionString(),
            'X-Powered-By' => "Ratchet/" . $this->versioner->getSupportedVersionString()
        ));
        $conn->send((string) $response);
        $conn->close();
    }
}
