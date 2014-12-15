<?php

/**
 * \AppserverIo\Appserver\WebSocketServer\Servers\AsyncServer
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
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Appserver\WebSocketServer\Servers;

use AppserverIo\Server\Dictionaries\ModuleVars;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Dictionaries\ServerStateKeys;
use AppserverIo\Server\Interfaces\ServerConfigurationInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Interfaces\ServerInterface;
use AppserverIo\Server\Exceptions\ModuleNotFoundException;
use AppserverIo\Server\Exceptions\ConnectionHandlerNotFoundException;

/**
 * A asynchronous server implementation based on Ratchet.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */
class AsyncServer extends \Thread implements ServerInterface
{

    /**
     * Hold's the server context instance
     *
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface The server context instance
     */
    protected $serverContext;

    /**
     * TRUE if the server has been started successfully, else FALSE.
     *
     * @var \AppserverIo\Server\Dictionaries\ServerStateKeys
     */
    protected $serverState;

    /**
     * Constructs the server instance
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The server context instance
     */
    public function __construct(ServerContextInterface $serverContext)
    {
        // initialize the server state
        $this->serverState = ServerStateKeys::get(ServerStateKeys::WAITING_FOR_INITIALIZATION);
        // set context
        $this->serverContext = $serverContext;
        // start server thread
        $this->start();
    }

    /**
     * Return's the config instance
     *
     * @return \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Start's the server's worker as defined in configuration
     *
     * @return void
     *
     * @throws \AppserverIo\Server\Exceptions\ModuleNotFoundException
     * @throws \AppserverIo\Server\Exceptions\ConnectionHandlerNotFoundException
     */
    public function run()
    {
        // set current dir to base dir for relative dirs
        chdir(SERVER_BASEDIR);

        // setup autoloader
        require SERVER_AUTOLOADER;

        // init server context
        $serverContext = $this->getServerContext();

        // init config var for shorter calls
        $serverConfig = $serverContext->getServerConfig();

        // init server name
        $serverName = $serverConfig->getName();

        // init logger
        $logger = $serverContext->getLogger();

        $logger->debug(
            sprintf("starting %s (%s)", $serverName, __CLASS__)
        );

        // initialization has been successful
        $this->serverState = ServerStateKeys::get(ServerStateKeys::INITIALIZATION_SUCCESSFUL);

        // initialize the connection handler
        $connectionHandler = null;

        // initiate server connection handlers
        $connectionHandlersTypes = $serverConfig->getConnectionHandlers();
        foreach ($connectionHandlersTypes as $connectionHandlerType) {

            // check if conenction handler type exists
            if (!class_exists($connectionHandlerType)) {
                throw new ConnectionHandlerNotFoundException($connectionHandlerType);
            }
            // instantiate connection handler type
            $applications = $serverContext->getContainer()->getApplications();
            $connectionHandler = new $connectionHandlerType($applications);

            $logger->debug(
                sprintf("%s init connectionHandler (%s)", $serverName, $connectionHandlerType)
            );

            // init connection handler with serverContext (this)
            $connectionHandler->init($serverContext);

            // inject modules
            $connectionHandler->injectModules(array());

            // stop, because we only support one connection handler
            break;
        }

        // get class names
        $socketType = $serverConfig->getSocketType();

        // setup server bound on local adress
        $serverConnection = $socketType::getServerInstance(
            $connectionHandler,
            $serverConfig->getPort(),
            $serverConfig->getAddress()
        );

        $logger->debug(
            sprintf("%s started socket (%s)", $serverName, $socketType)
        );

        // sockets has been started
        $this->serverState = ServerStateKeys::get(ServerStateKeys::SERVER_SOCKET_STARTED);

        $logger->info(
            sprintf("%s listing on %s:%s...", $serverName, $serverConfig->getAddress(), $serverConfig->getPort())
        );

        // start the server connection
        $serverConnection->run();
    }
}
