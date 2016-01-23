<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Security\Mapping
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;

/**
 * The interface for an URL pattern to an authenticator mapping implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface MappingInterface
{

    /**
     * Return's the URL pattern.
     *
     * @return string The URL pattern
     */
    public function getUrlPattern();

    /**
     * Return's the authenticator serial.
     *
     * @return string The authenticator serial
     */
    public function getAuthenticatorSerial();

    /**
     * Return's the role names.
     *
     * @return array The role names
     */
    public function getRoleNames();

    /**
     * Return's the HTTP methods that has to be authenticated.
     *
     * @return array The HTTP methods
     */
    public function getHttpMethods();

    /**
     * Return's the HTTP methods that has to b omissed from authentication
     *
     * @return array The HTTP methods
     */
    public function getHttpMethodOmissions();

    /**
     * Return's TRUE if the passed request matches the mappings URL patter.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The request to match
     *
     * @return boolean TRUE if the request matches, else FALSE
     */
    public function match(HttpServletRequestInterface $servletRequest);
}
