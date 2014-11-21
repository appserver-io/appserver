<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\Handler
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

use Ratchet\MessageComponentInterface;
use AppserverIo\Appserver\WebSocketProtocol\HandlerConfig;

/**
 * Interface for all handlers.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage WebSocketProtocol
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface Handler extends MessageComponentInterface
{

    /**
     * Initializes the handler with the passed configuration.
     *
     * @param \AppserverIo\Appserver\WebSocketProtocol\HandlerConfig $config The configuration to initialize the handler with
     *
     * @return void
     * @throws \AppserverIo\Appserver\WebSocketProtocol\HandlerException Is thrown if the configuration has errors
     */
    public function init(HandlerConfig $config);

    /**
     * Returns the servlets configuration.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerConfig The handlers configuration
     */
    public function getHandlerConfig();

    /**
     * Returns the handler context instance
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerContext The handler context instance
     */
    public function getHandlerContext();

    /**
     * Returns the request instance.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\Request The request instance
     */
    public function getRequest();
}
