<?php

/**
 * AppserverIo\Appserver\MemcacheProtocol\MemcacheConnectionHandler
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
 * @subpackage MemcacheProtocol
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/memcacheprotocol
 * @link       http://www.appserver.io
 * @link       https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */

namespace AppserverIo\Appserver\MemcacheProtocol;

use AppserverIo\Server\Interfaces\ConnectionHandlerInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\WorkerInterface;
use AppserverIo\Psr\Socket\SocketInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\MemcacheServer\MemcacheServer;
use AppserverIo\Appserver\MemcacheServer\GarbageCollector;

/**
 * This is a connection handler to handle Memcache compatible cache requests.
 *
 * @category   Appserver
 * @package    Psr
 * @subpackage MemcacheProtocol
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io-psr/memcacheprotocol
 * @link       http://www.appserver.io
 * @link       https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
class MemcacheConnectionHandler implements ConnectionHandlerInterface
{

    /**
     * The server context instance
     *
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Hold's the request's context instance
     *
     * @var \AppserverIo\Server\Interfaces\RequestContextInterface
     */
    protected $requestContext;

    /**
     * The connection instance
     *
     * @var \AppserverIo\Server\Sockets\SocketInterface
     */
    protected $connection;

    /**
     * The worker instance
     *
     * @var \AppserverIo\Server\Interfaces\WorkerInterface
     */
    protected $worker;

    /**
     * Hold's an array of modules to use for connection handler
     *
     * @var array
     */
    protected $modules;

    /**
     * The server API implementation.
     *
     * @var \AppserverIo\Appserver\MemcacheServer\MemcacheServer
     */
    protected $cache;

    /**
     * The garbage collector for the API implementation.
     *
     * @var \AppserverIo\Appserver\MemcacheServer\GarbageCollector
     */
    protected $gc;

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

        // initialize the cache API and the garbage collector
        $this->cache = new MemcacheServer(new GenericStackable());
        $this->gc = new GarbageCollector($this->cache);

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));
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
     * Injects the request context
     *
     * @param \AppserverIo\Server\Interfaces\RequestContextInterface $requestContext The request's context instance
     *
     * @return void
     */
    public function injectRequestContext(RequestContextInterface $requestContext)
    {
        $this->requestContext = $requestContext;
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
     * Returns the cache API implementation.
     *
     * @return \AppserverIo\Appserver\MemcacheServer\Cache The cache API implementation.
     */
    public function getCache()
    {
        return $this->cache;
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
     * Return's the request's context instance
     *
     * @return \AppserverIo\Server\Interfaces\RequestContextInterface
     */
    public function getRequestContext()
    {
        return $this->requestContext;
    }

    /**
     * Returns the servers configuration
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
     */
    public function handle(SocketInterface $connection, WorkerInterface $worker)
    {

        // add connection ref to self
        $this->connection = $connection;
        $this->worker = $worker;

        // get instances for short calls
        $cache = $this->getCache();
        $serverContext = $this->getServerContext();
        $serverConfig = $serverContext->getServerConfig();

        // init keep alive settings
        $keepAliveTimeout = (int) $serverConfig->getKeepAliveTimeout();
        $keepAliveMax = (int) $serverConfig->getKeepAliveMax();
        $keepAliveConnection = true;

        // create Memcache request instance
        $vo = new MemcacheRequest();

        do {

            // receive a line from the connection
            $line = $connection->readLine(1024, $keepAliveTimeout);

            // push message into ValueObject
            $vo->push($line);

            // check if we've to load data also
            if ($vo->isComplete() === false) { // if yes, load it (+ 2 for the \r\n send by the Memcache client)
                $vo->push($connection->read($vo->bytesToRead() + 2, $keepAliveTimeout));
            }

            // check if the VO is already complete
            if ($vo->isComplete() === true) {

                // handle the request
                $cache->request($vo);

                // send response to client (even if response is empty)
                $connection->write($cache->getResponse() . $cache->getNewLine());

                // select current state
                switch ($cache->getState()) {

                    case MemcacheProtocol::STATE_RESET:

                        $vo->reset();
                        $cache->reset();

                        break;

                    case MemcacheProtocol::STATE_CLOSE:

                        $vo->reset();
                        $cache->reset();

                        $keepAliveConnection = false;

                        break;

                    default:

                        $vo->reset();
                        $cache->reset();

                        $connection->write("SERVER ERROR unknown state\r\n");

                        break;

                }

            } else {


                $vo->reset();
                $cache->reset();

                $connection->write("SERVER ERROR unknown state\r\n");

                $keepAliveConnection = false;

            }

        } while ($keepAliveConnection === true);

        // finally close connection
        $connection->close();
    }

    /**
     * Does shutdown logic for worker if something breaks in process.
     *
     * @return void
     */
    public function shutdown()
    {
        // get refs to local vars
        $connection = $this->getConnection();
        $worker = $this->getWorker();

        // check if connections is still alive
        if ($connection) {

            // close client connection
            $this->getConnection()->close();
        }

        // check if worker is given
        if ($worker) {
            // call shutdown process on worker to respawn
            $this->getWorker()->shutdown();
        }
    }
}
