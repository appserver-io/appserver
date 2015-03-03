<?php

/**
 * \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\ServletSessionInterface;

/**
 * Interface for all session marshaller implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @see       http://php.net/session
 * @see       http://php.net/setcookie
 */
interface SessionMarshallerInterface
{

    /**
     * Marshalls the passed object.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The session we want to marshall
     *
     * @return string The marshalled session representation
     */
    public function marshall(ServletSessionInterface $servletSession);

    /**
     * Un-marshals the marshaled session representation.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The empty session instance we want the un-marshaled data be added to
     * @param string                                           $marshalled     The marshaled session representation
     *
     * @return void
     */
    public function unmarshall(ServletSessionInterface $servletSession, $marshalled);
}
