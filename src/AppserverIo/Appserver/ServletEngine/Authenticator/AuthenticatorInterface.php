<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\AuthenticatorInterface
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

namespace AppserverIo\Appserver\ServletEngine\Authenticator;

use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Interface for authentication type implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface AuthenticatorInterface
{


    /**
     * Returns the configuration data given for authentication type.
     *
     * @return object The configuration data
     */
    public function getConfigData();

    /**
     * Return's the authentication manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface The authentication manager instance
     */
    public function getAuthenticationManager();

    /**
     * Returns the authentication type token.
     *
     * @return string
     */
    public function getAuthType();

    /**
     * Try to authenticate against the configured adapter.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the request can't be authenticated
     */
    public function authenticate(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse);

    /**
     * Returns the password parsed from the request.
     *
     * @return string|null The password
     */
    public function getPassword();

    /**
     * Returns the username parsed from the request.
     *
     * @return string|null The username
     */
    public function getUsername();

    /**
     * Mark's the authenticator as the default one.
     *
     * @return void
     */
    public function setDefaultAuthenticator();

    /**
     * Query whether or not this is the default authenticator.
     *
     * @return boolean TRUE if this is the default authenticator, else FALSE
     */
    public function isDefaultAuthenticator();
}
