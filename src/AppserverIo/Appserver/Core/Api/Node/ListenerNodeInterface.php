<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ListenerNodeInterface
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

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Interface for a listener configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ListenerNodeInterface
{

    /**
     * Return's the event the listener is bound to.
     *
     * @return string The unique class loader name
     */
    public function getEvent();

    /**
     * Return's the listener type.
     *
     * @return string The listener type
     */
    public function getType();

    /**
     * Return's the listener priority.
     *
     * @return string The listener priority
     */
    public function getPriority();
}
