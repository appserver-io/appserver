<?php

/**
 * AppserverIo\Appserver\WebSocketServer\ResourceLocatorInterface
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

namespace AppserverIo\Appserver\WebSocketServer;

use TechDivision\WebSocketProtocol\Request;
use TechDivision\WebSocketProtocol\HandlerContext;

/**
 * Interface for the resource locator instances.
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
interface ResourceLocatorInterface
{

    /**
     * Tries to locate the handler that handles the request and returns the instance if one can be found.
     *
     * @param \TechDivision\WebSocketProtocol\HandlerContext $handlerManager The handler manager
     * @param \TechDivision\WebSocketProtocol\Request        $request        The request instance
     *
     * @return \Ratchet\MessageComponentInterface The handler that maps the request instance
     */
    public function locate(HandlerContext $handlerManager, Request $request);
}
