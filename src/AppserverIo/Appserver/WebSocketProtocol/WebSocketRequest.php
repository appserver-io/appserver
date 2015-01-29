<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\WebSocketRequest
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

namespace AppserverIo\Appserver\WebSocketProtocol;

use Guzzle\Http\Message\RequestInterface as GuzzleRequest;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A simple websocket request implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class WebSocketRequest implements RequestInterface
{

    /**
     * The guzzle request instance passed from ratchet.
     *
     * @var \Guzzle\Http\Message\RequestInterface
     */
    protected $request;

    /**
     * The application context instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $context;

    /**
     * The context path (application name).
     *
     * @var string
     */
    protected $contextPath;

    /**
     * The path to the handler, that is always absolute to the context path.
     *
     * @var string
     */
    protected $handlerPath;

    /**
     * Injects the guzzle request instance passed from ratchet.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request The guzzle request passed from ratchet
     *
     * @return void
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::injectRequest()
     */
    public function injectRequest(GuzzleRequest $request)
    {
        $this->request = $request;
    }

    /**
     * Injects the request context that is the web application almost.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $context The request context instance
     *
     * @return void
     */
    public function injectContext(ApplicationInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Returns the guzzle request instance passed from ratchet.
     *
     * @return \Guzzle\Http\Message\RequestInterface The guzzle request instance
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::getRequest()
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the request context that is the web application almost.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The request context
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::getContext()
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Sets the path the handler that is always absolute from the context path.
     *
     * @param string $handlerPath The path to the handler
     *
     * @return void
     */
    public function setHandlerPath($handlerPath)
    {
        $this->handlerPath = $handlerPath;
    }

    /**
     * Returns the path the handler that is always absolute from the context path.
     *
     * @return string The path to the handler
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::getHandlerPath()
     */
    public function getHandlerPath()
    {
        return $this->handlerPath;
    }

    /**
     * Sets the context path (application name).
     *
     * @param string $contextPath The context path
     *
     * @return void
     */
    public function setContextPath($contextPath)
    {
        $this->contextPath = $contextPath;
    }

    /**
     * Returns the context path (application name).
     *
     * @return string The context path
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::getHandlerPath()
     */
    public function getContextPath()
    {
        return $this->contextPath;
    }

    /**
     * Returns the host that handles this request.
     *
     * @return string The host name that handles this request
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::getHost()
     */
    public function getHost()
    {
        return $this->getRequest()->getHost();
    }

    /**
     * Returns the request path, that will contain the application
     * name if we're not in an virtual host.
     *
     * @return string The request path
     * @see \AppserverIo\Appserver\WebSocketProtocol\RequestInterface::getPath()
     */
    public function getPath()
    {
        return $this->getRequest()->getPath();
    }
}
