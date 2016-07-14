<?php

/**
 * \AppserverIo\Appserver\MessageQueue\ConnectionHandlers\AmqpConnectionHandler
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
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/webserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue\ConnectionHandlers;

use AppserverIo\Server\Dictionaries\EnvVars;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Interfaces\ConnectionHandlerInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\WorkerInterface;
use AppserverIo\Psr\Socket\SocketInterface;
use AppserverIo\Psr\Socket\SocketReadException;
use AppserverIo\Psr\Socket\SocketReadTimeoutException;
use AppserverIo\Psr\Socket\SocketServerException;

/**
 * Class HttpConnectionHandler
 *
 * @author Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link https://github.com/appserver-io/webserver
 * @link http://www.appserver.io
 */
class AmqpConnectionHandler implements ConnectionHandlerInterface
{

    /**
     * Defines the read length for AMQP connections
     *
     * @var int
     */
    const AMQP_CONNECTION_READ_LENGTH = 2048;

    /**
     * Holds parser instance
     *
     * @var \AppserverIo\Http\HttpRequestParserInterface
     */
    protected $parser;

    /**
     * Holds the server context instance
     *
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Holds the request's context instance
     *
     * @var \AppserverIo\Server\Interfaces\RequestContextInterface
     */
    protected $requestContext;

    /**
     * Holds an array of modules to use for connection handler
     *
     * @var array
     */
    protected $modules;

    /**
     * Holds the connection instance
     *
     * @var \AppserverIo\Psr\Socket\SocketInterface
     */
    protected $connection;

    /**
     * Holds the worker instance
     *
     * @var \AppserverIo\Server\Interfaces\WorkerInterface
     */
    protected $worker;

    /**
     * Flag if a shutdown function was registered or not
     *
     * @var boolean
     */
    protected $hasRegisteredShutdown = false;

    /**
     * Inits the connection handler by given context and params
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The server's context
     * @param array                                                 $params        The params for connection handler
     *
     * @return void
     */
    public function init(ServerContextInterface $serverContext, array $params = null)
    {
        // set server context
        $this->serverContext = $serverContext;

        // get request context type
        $requestContextType = $this->getServerConfig()->getRequestContextType();

        /**
         * @var \AppserverIo\Server\Interfaces\RequestContextInterface $requestContext
         */
        // instantiate and init request context
        $this->requestContext = new $requestContextType();
        $this->requestContext->init($this->getServerConfig());
    }

    /**
     * Injects all needed modules for connection handler to process
     *
     * @param array $modules An array of Modules
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
     * Returns a specific module instance by given name
     *
     * @param string $name The modules name to return an instance for
     *
     * @return \AppserverIo\WebServer\Interfaces\HttpModuleInterface|null
     */
    public function getModule($name)
    {
        if (isset($this->modules[$name])) {
            return $this->modules[$name];
        }
    }

    /**
     * Returns the server context instance
     *
     * @return \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Returns the request's context instance
     *
     * @return \AppserverIo\Server\Interfaces\RequestContextInterface
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /**
     * Returns the server's configuration
     *
     * @return \AppserverIo\Server\Interfaces\ServerConfigurationInterface
     */
    public function getServerConfig()
    {
        return $this->getServerContext()->getServerConfig();
    }

    /**
     * Returns the connection used to handle with
     *
     * @return \AppserverIo\Psr\Socket\SocketInterface
     */
    protected function getConnection()
    {
        return $this->connection;
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
     * Handles the connection with the connected client in a proper way the given
     * protocol type and version expects for example.
     *
     * @param \AppserverIo\Psr\Socket\SocketInterface        $connection The connection to handle
     * @param \AppserverIo\Server\Interfaces\WorkerInterface $worker     The worker how started this handle
     *
     * @return bool Weather it was responsible to handle the firstLine or not.
     * @throws \Exception
     */
    public function handle(SocketInterface $connection, WorkerInterface $worker)
    {
        // register shutdown handler once to avoid strange memory consumption problems
        $this->registerShutdown();

        // add connection ref to self
        $this->connection = $connection;
        $this->worker = $worker;

        // load the server configuration
        $serverConfig = $this->getServerConfig();

        // create a local reference to the request context
        $requestContext = $this->getRequestContext();

        // reset connection info to server vars
        $requestContext->setServerVar(ServerVars::REMOTE_ADDR, $connection->getAddress());
        $requestContext->setServerVar(ServerVars::REMOTE_PORT, $connection->getPort());

        // init keep alive settings
        $keepAliveTimeout = (int) $serverConfig->getKeepAliveTimeout();

        // init keep alive connection flag
        $keepAliveConnection = true;

        // time settings
        $requestContext->setServerVar(ServerVars::REQUEST_TIME, time());

        $this->getServerContext()->getLogger()->error("Now try to read AMQP header sent by client");

        // set first line from connection
        $line = $connection->readLine(9, $keepAliveTimeout);

        $this->getServerContext()->getLogger()->error($line);

        $version = '';
        foreach (unpack('C*', $line) as $chr) {
            $version .= chr($chr);
        }

        $this->getServerContext()->getLogger()->error("Found requested version: $version");

        $str = 'AMQP0091';
        $result = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $result .= pack('C*', ord($str[$i]));
        }

        $connection->write($result);

        do {
            // try to handle request if its a http request
            try {

                $this->getServerContext()->getLogger()->error("Now try to read line");

                // set first line from connection
                $line = $connection->readLine(2048, $keepAliveTimeout);

                $this->getServerContext()->getLogger()->error("Found line: $line");

            } catch (SocketReadTimeoutException $e) {
                // break the request processing due to client timeout
                break;
            } catch (SocketReadException $e) {
                // break the request processing due to peer reset
                break;
            } catch (SocketServerException $e) {
                // break the request processing
                break;
            } catch (\Exception $e) {
                // set status code given by exception
                // if 0 is comming set 500 by default
                error_log($e->__toString());
            }

            // init context vars afterwards to avoid performance issues
            $requestContext->initVars();

        } while ($keepAliveConnection === true);

        $this->getServerContext()->getLogger()->error(__METHOD__ . ':' . __LINE__);

        // close connection if not closed yet
        // $connection->close();
    }

    /**
     * Registers the shutdown function in this context
     *
     * @return void
     */
    public function registerShutdown()
    {
        // register shutdown handler once to avoid strange memory consumption problems
        if ($this->hasRegisteredShutdown === false) {
            register_shutdown_function(array(
                &$this,
                "shutdown"
            ));
            $this->hasRegisteredShutdown = true;
        }
    }

    /**
     * Does shutdown logic for worker if something breaks in process
     *
     * @return void
     */
    public function shutdown()
    {

        $connection = $this->getConnection();

        // get last error array
        $lastError = error_get_last();

        // check if it was a fatal error
        if (!is_null($lastError) && $lastError['type'] === 1) {
            // set response code to 500 Internal Server Error
            $response->setStatusCode(500);
            $errorMessage = 'PHP Fatal error: ' . $lastError['message'] . ' in ' . $lastError['file'] . ' on line ' . $lastError['line'];
            error_log($errorMessage);
        }

        $connection->close();
    }
}
