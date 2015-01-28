<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\HandlerInterface
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

use Ratchet\MessageComponentInterface;

/**
 * Interface for all handlers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface HandlerInterface extends MessageComponentInterface
{

    /**
     * Initializes the handler with the passed configuration.
     *
     * @param \AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface $config The configuration to initialize the handler with
     *
     * @return void
     * @throws \AppserverIo\Appserver\WebSocketProtocol\HandlerException Is thrown if the configuration has errors
     */
    public function init(HandlerConfigInterface $config);

    /**
     * Returns the servlets configuration.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface The handlers configuration
     */
    public function getHandlerConfig();

    /**
     * Returns the handler context instance
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerContextInterface The handler context instance
     */
    public function getHandlerContext();

    /**
     * Returns the request instance.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\RequestInterface The request instance
     */
    public function getRequest();
}
