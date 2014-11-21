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
 * @category   Server
 * @package    Appserver
 * @subpackage WebSocketProtocol
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
namespace AppserverIo\Appserver\WebSocketProtocol;

use Guzzle\Http\Message\RequestInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A simple websocket request implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage WebSocketProtocol
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class WebSocketRequest implements Request
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
     * @var \TechDivision\ApplicationServer\Interfaces\ApplicationInterface
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
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::injectRequest()
     */
    public function injectRequest(RequestInterface $request)
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
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::getRequest()
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Returns the request context that is the web application almost.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The request context
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::getContext()
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
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::getHandlerPath()
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
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::getHandlerPath()
     */
    public function getContextPath()
    {
        return $this->contextPath;
    }

    /**
     * Returns the host that handles this request.
     *
     * @return string The host name that handles this request
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::getHost()
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
     * @see \AppserverIo\Appserver\WebSocketProtocol\Request::getPath()
     */
    public function getPath()
    {
        return $this->getRequest()->getPath();
    }
}
