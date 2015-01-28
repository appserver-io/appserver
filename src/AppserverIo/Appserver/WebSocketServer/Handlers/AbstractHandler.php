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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\WebSocketServer\Handlers;

use AppserverIo\Appserver\WebSocketProtocol\RequestInterface;
use AppserverIo\Appserver\WebSocketProtocol\HandlerInterface;
use AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface;

/**
 * Abstract base class for all handlers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractHandler implements HandlerInterface
{

    /**
     * The handler configuration instance.
     *
     * @var \AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface
     */
    protected $config;

    /**
     * Current request on a handled connection
     *
     * @var \AppserverIo\Appserver\WebSocketProtocol\RequestInterface $request
     */
    protected $request;

    /**
     * Initializes the handler with the passed configuration.
     *
     * @param \AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface $config The configuration to initialize the handler with
     *
     * @return void
     * @throws \AppserverIo\Appserver\WebSocketProtocol\HandlerException Is thrown if the configuration has errors
     */
    public function init(HandlerConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * Injects the request instance when the connection has been created.
     *
     * @param \AppserverIo\Appserver\WebSocketProtocol\RequestInterface $request The request instance
     *
     * @return void
     */
    public function injectRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * Return's the servlet's configuration.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface The handler's configuration
     */
    public function getHandlerConfig()
    {
        return $this->config;
    }

    /**
     * Returns the handler context instance
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerContextInterface The handler context instance
     */
    public function getHandlerContext()
    {
        return $this->getHandlerConfig()->getHandlerContext();
    }

    /**
     * Returns the request instance.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\RequestInterface The request instance
     */
    public function getRequest()
    {
        return $this->request;
    }
}
