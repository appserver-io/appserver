<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Login\BasicAuthentication
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

namespace AppserverIo\Appserver\ServletEngine\Security\Auth\Login;

use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Http\Authentication\AbstractAuthentication;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Http\Authentication\Adapters\HtpasswdAdapter;

/**
 * Class BasicAuthentication
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class BasicAuthentication extends AbstractAuthentication
{

    /**
     * Defines the auth type which should match the client request type definition
     *
     * @var string AUTH_TYPE
     */
    const AUTH_TYPE = 'Basic';

    /**
     * Defines the default authentication adapter used if none was specified
     *
     * @var string DEFAULT_ADAPTER
     */
    const DEFAULT_ADAPTER = HtpasswdAdapter::ADAPTER_TYPE;

    /**
     * Constructs the authentication type
     *
     * @param array $configData The configuration data for auth type instance
     */
    public function __construct(array $configData = array())
    {

        // initialize the supported adapter types
        $this->addSupportedAdapter(HtpasswdAdapter::getType());

        // initialize the instance
        parent::__construct($configData);
    }

    /**
     * Initialize by the authentication type with the data from the request.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface  $request  The request with the content of authentication data sent by client
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface $response The response sent back to the client
     *
     * @return void
     * @throws \AppserverIo\Http\Authentication\AuthenticationException If the authentication type can't be initialized
     */
    public function init(RequestInterface $request, ResponseInterface $response)
    {

        // check if auth header is not set in coming request headers
        if ($request->hasHeader(Protocol::HEADER_AUTHORIZATION) === false) {
            // send header for challenge authentication against client
            $response->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            // throw exception for auth required
            throw new AuthenticationException('Request is not authorized', 401);
        }

        // initialize the authentication adapter
        parent::init($request, $response);
    }

    /**
     * Try to authenticate against the configured adapter.
     *
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface $response The response sent back to the client
     *
     * @return void
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the request can't be authenticated
     */
    public function authenticate(ResponseInterface $response)
    {

        // verify everything to be ready for auth if not return false
        if ($this->verify() === false) {
            $response->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            throw new AuthenticationException('Invalid or missing username and/or password', 401);
        }

        // do actual authentication check
        if ($this->getAuthAdapter()->authenticate($this->getAuthData()) === false) {
            $response->addHeader(Protocol::HEADER_WWW_AUTHENTICATE, $this->getAuthenticateHeader());
            throw new AuthenticationException('Password doesn\'t match username', 401);
        }
    }

    /**
     * Parses the request for the necessary, authentication adapter specific, login credentials.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface $request The request with the content of authentication data sent by client
     *
     * @return void
     */
    protected function parse(RequestInterface $request)
    {

        // load the raw login credentials
        $rawAuthData = $request->getHeader(Protocol::HEADER_AUTHORIZATION);

        // set auth hash got from auth data request header
        $authHash = trim(strstr($rawAuthData, " "));

        // check if username and password has been passed
        if (strstr($credentials = base64_decode($authHash), ':') === false) {
            return false;
        }

        // get out username and password
        list ($username, $password) = explode(':', $credentials);

        // check if either username or password was not found and return false
        if (($password === null) || ($username === null)) {
            return false;
        }

        // fill the auth data array
        $this->authData['username'] = $username;
        $this->authData['password'] = $password;
        return true;
    }

    /**
     * Returns the authentication header for response to set
     *
     * @return string
     */
    public function getAuthenticateHeader()
    {
        return $this->getType() . ' realm="' . $this->configData["realm"] . '"';
    }

    /**
     * Returns the parsed password
     *
     * @return string
     */
    public function getPassword()
    {
        $authData = $this->getAuthData();
        return isset($authData['password']) ? $authData['password'] : null;
    }
}
