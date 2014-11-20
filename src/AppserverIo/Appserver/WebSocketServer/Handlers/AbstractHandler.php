<?php

/**
 * AppserverIo\Appserver\WebSocketServer\Handlers\AbstractHandler
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
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\WebSocketServer\Handlers;

use TechDivision\WebSocketProtocol\Request;
use TechDivision\WebSocketProtocol\Handler;
use TechDivision\WebSocketProtocol\HandlerConfig;

/**
 * Abstract base class for all handlers.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
abstract class AbstractHandler implements Handler
{

    /**
     * The handler configuration instance.
     *
     * @var \TechDivision\WebSocketProtocol\HandlerConfig
     */
    protected $config;

    /**
     * Current request on a handled connection
     *
     * @var \TechDivision\WebSocketProtocol\Request $request
     */
    protected $request;

    /**
     * Initializes the handler with the passed configuration.
     *
     * @param \TechDivision\WebSocketProtocol\HandlerConfig $config The configuration to initialize the handler with
     *
     * @return void
     * @throws \TechDivision\WebSocketProtocol\HandlerException Is thrown if the configuration has errors
     */
    public function init(HandlerConfig $config)
    {
        $this->config = $config;
    }

    /**
     * Injects the request instance when the connection has been created.
     *
     * @param \TechDivision\WebSocketProtocol\Request $request The request instance
     *
     * @return void
     */
    public function injectRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Return's the servlet's configuration.
     *
     * @return \TechDivision\WebSocketProtocol\HandlerConfig The handler's configuration
     */
    public function getHandlerConfig()
    {
        return $this->config;
    }

    /**
     * Returns the handler context instance
     *
     * @return \TechDivision\WebSocketProtocol\HandlerContext The handler context instance
     */
    public function getHandlerContext()
    {
        return $this->getHandlerConfig()->getHandlerContext();
    }

    /**
     * Returns the request instance.
     *
     * @return \TechDivision\WebSocketProtocol\Request The request instance
     */
    public function getRequest()
    {
        return $this->request;
    }
}
