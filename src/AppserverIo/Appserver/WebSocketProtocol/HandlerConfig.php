<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\HandlerConfig
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

/**
 * Interface for the handler configuration.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage WebSocketProtocol
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface HandlerConfig
{

    /**
     * Returns the handlers name from the handler.xml configuration file.
     *
     * @return string The handler name
     */
    public function getHandlerName();

    /**
     * Returns the handler context instance.
     *
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerContext The handler context instance
     */
    public function getHandlerContext();

    /**
     * Returns the webapp base path.
     *
     * @return string The webapp base path
     */
    public function getWebappPath();
}
