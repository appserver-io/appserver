<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\Request
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

use Guzzle\Http\Message\RequestInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * Request interface to be implemented by web socket request implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface Request
{

    /**
     * Injects the guzzle request instance passed from ratchet.
     *
     * @param \Guzzle\Http\Message\RequestInterface $request The guzzle request passed from ratchet
     *
     * @return void
     */
    public function injectRequest(RequestInterface $request);

    /**
     * Injects the request context that is the web application almost.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $context The request context
     *
     * @return void
     */
    public function injectContext(ApplicationInterface $context);

    /**
     * Returns the guzzle request instance passed from ratchet.
     *
     * @return \Guzzle\Http\Message\RequestInterface The guzzle request instance
     */
    public function getRequest();

    /**
     * Returns the request context that is the web application almost.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The request context
     */
    public function getContext();

    /**
     * Returns the path the handler that is always absolute from the context path.
     *
     * @return string The path to the handler
     */
    public function getHandlerPath();

    /**
     * Returns the context path (application name).
     *
     * @return string The context path
     */
    public function getContextPath();

    /**
     * Returns the host that handles this request.
     *
     * @return string The host name that handles this request
     */
    public function getHost();

    /**
     * Returns the request path, that will contain the application
     * name if we're not in an virtual host.
     *
     * @return string The request path
     */
    public function getPath();
}
