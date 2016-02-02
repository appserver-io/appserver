<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\BasicAuthenticator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Authenticator;

use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Lang\String;

/**
 * An authenticator implementation providing HTTP basic authentication.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class BasicAuthenticator extends AbstractAuthenticator
{

    /**
     * Defines the auth type which should match the client request type definition
     *
     * @var string AUTH_TYPE
     */
    const AUTH_TYPE = 'Basic';

    /**
     * The password to authenticate the user with.
     *
     * @var string
     */
    protected $password;

    /**
     * Try to authenticate the user making this request, based on the specified login configuration.
     *
     * Return TRUE if any specified constraint has been satisfied, or FALSE if we have created a response
     * challenge already.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the request can't be authenticated
     */
    public function authenticate(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // check if auth header is not set in coming request headers
        if ($servletRequest->hasHeader(Protocol::HEADER_AUTHORIZATION) === false) {
            // stop processing immediately
            $servletRequest->setDispatched(true);
            $servletResponse->setStatusCode(401);
            $servletResponse->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            return false;
        }

        // load the raw login credentials
        $rawAuthData = $servletRequest->getHeader(Protocol::HEADER_AUTHORIZATION);

        // set auth hash got from auth data request header and check if username and password has been passed
        if (strstr($credentials = base64_decode(trim(strstr($rawAuthData, " "))), ':') === false) {
            // stop processing immediately
            $servletRequest->setDispatched(true);
            $servletResponse->setStatusCode(401);
            $servletResponse->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            return false;
        }

        // get out username and password
        list ($username, $password) = explode(':', $credentials);

        // query whether or not a username and a password has been passed
        if (($password === null) || ($username === null)) {
            // stop processing immediately
            $servletRequest->setDispatched(true);
            $servletResponse->setStatusCode(401);
            $servletResponse->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            return false;
        }

        // set username and password
        $this->username = new String($username);
        $this->password = new String($password);

        // load the realm to authenticate this request for
        /** @var AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm */
        $realm = $this->getAuthenticationManager()->getRealm($this->getRealmName());

        // authenticate the request and initialize the user principal
        $userPrincipal = $realm->authenticate($this->getUsername(), $this->getPassword());

        // query whether or not the realm returned an authenticated user principal
        if ($userPrincipal == null) {
            // stop processing immediately
            $servletRequest->setDispatched(true);
            $servletResponse->setStatusCode(401);
            $servletResponse->setBodyStream('Unauthorized');
            $servletResponse->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            return false;
        }

        // add the user principal and the authentication type to the request
        $servletRequest->setUserPrincipal($userPrincipal);
        $servletRequest->setAuthType($this->getAuthType());
    }

    /**
     * Returns the authentication header for response to set
     *
     * @return string
     */
    public function getAuthenticateHeader()
    {
        return sprintf('%s realm="%s"', $this->getAuthType(), $this->getRealmName());
    }

    /**
     * Returns the parsed password
     *
     * @return string
     */
    public function getPassword()
    {
        return isset($this->password) ? $this->password : null;
    }
}
