<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\HandlerContext
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

use AppserverIo\Psr\Application\ManagerInterface;

/**
 * The handler context inteface for all handler managers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface HandlerContext extends ManagerInterface
{

    /**
     * The unique identifier to be registered in the application context.
     *
     * @var string
     */
    const IDENTIFIER = 'HandlerContext';

    /**
     * Returns the registered handlers.
     *
     * @return array An array with the initialized web socket handlers
     */
    public function getHandlers();

    /**
     * Returns the registered handlers.
     *
     * @param string $key The key the handler to be returned has been registered with.
     *
     * @return \Ratchet\MessageComponentInterface The requested handler
     */
    public function getHandler($key);

    /**
     * Returns the path to the webapp.
     *
     * @return string The path to the webapp
     */
    public function getWebappPath();
}
