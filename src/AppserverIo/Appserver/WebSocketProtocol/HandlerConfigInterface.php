<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\HandlerConfigInterface
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

/**
 * Interface for the handler configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface HandlerConfigInterface
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
     * @return \AppserverIo\Appserver\WebSocketProtocol\HandlerContextInterface The handler context instance
     */
    public function getHandlerContext();

    /**
     * Returns the webapp base path.
     *
     * @return string The webapp base path
     */
    public function getWebappPath();
}
