<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SessionHandlerNodeInterface
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
 * Interface for all session handler DTO implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionHandlerNodeInterface
{

    /**
     * Return's the session handler name.
     *
     * @return string The session handler name
     */
    public function getName();

    /**
     * Return's the session handler type.
     *
     * @return string The session handler type
     */
    public function getType();

    /**
     * Return's the session handler factory type.
     *
     * @return string The session handler factory type
     */
    public function getFactory();
}
