<?php

/**
 * AppserverIo\Appserver\ServletEngine\SessionMarshaller
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\ServletSession;

/**
 * Interface for all session marshaller implementations.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 * @link       http://php.net/session
 * @link       http://php.net/setcookie
 */
interface SessionMarshaller
{

    /**
     * Marshalls the passed object.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSession $servletSession The session we want to marshall
     *
     * @return string The marshalled session representation
     */
    public function marshall(ServletSession $servletSession);

    /**
     * Unmarshalls the marshalled session representation.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSession $servletSession The empty session instance we want the unmarshalled data be added to
     * @param string                                  $marshalled     The marshalled session representation
     *
     * @return void
     */
    public function unmarshall(ServletSession $servletSession, $marshalled);
}
