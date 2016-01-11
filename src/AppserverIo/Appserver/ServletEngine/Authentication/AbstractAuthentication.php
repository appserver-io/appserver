<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\AbstractAuthentication
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

namespace AppserverIo\Appserver\ServletEngine\Authentication;

use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Http\Authentication\AuthenticationInterface;

/**
 * Class AbstractAuthentication
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractAuthentication implements AuthenticationInterface
{

    /**
     * Holds the auth data got from http authentication header.
     *
     * @var \AppserverIo\Http\Authentication\Adapters\AdapterInterface $authAdapter
     */
    protected $authAdapter;

    /**
     * Holds the auth data got from http authentication header.
     *
     * @var string $authData
     */
    protected $authData;

    /**
     * Holds the requests method.
     *
     * @var string $reqMethod
     */
    protected $reqMethod;

    /**
     * Holds the configuration data given for authentication type.
     *
     * @var \AppserverIo\Configuration\NodeInterface
     */
    protected $configData;

    /**
     * The authentication manager instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * Array with the supported adapter types.
     *
     * @var array
     */
    protected $supportedAdapters = array();

    /**
     * Constructs the authentication type.
     *
     * @param \AppserverIo\Configuration\NodeInterface                                           $configData            The configuration data for auth type instance
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface $authenticationManager The authentication manager instance
     */
    public function __construct(NodeInterface $configData, AuthenticationManagerInterface $authenticationManager)
    {
        // set vars internally
        $this->configData = $configData;
        $this->authenticationManager = $authenticationManager;

        // verify the configuration
        $this->verifyConfig();

        // prepare our chosen adapter
        $this->prepareAdapter();
    }

    /**
     * Return's the auth data got from http authentication header.
     *
     * @return \AppserverIo\Http\Authentication\Adapters\AdapterInterface The authentication adapter to use
     */
    public function getAuthAdapter()
    {
        return $this->authAdapter;
    }

    /**
     * Returns the authentication data content.
     *
     * @return string The authentication data content
     */
    public function getAuthData()
    {
        return $this->authData;
    }

    /**
     * Returns the configuration data given for authentication type.
     *
     * @return object The configuration data
     */
    public function getConfigData()
    {
        return $this->configData;
    }

    /**
     * Return's the authentication manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface The authentication manager instance
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Returns the request method.
     *
     * @return string The request method
     */
    public function getRequestMethod()
    {
        return $this->reqMethod;
    }

    /**
     * Returns the authentication type token.
     *
     * @return string
     */
    public function getType()
    {
        return static::AUTH_TYPE;
    }

    /**
     * Return's the realm name.
     *
     * @return string The realm name
     */
    public function getRealmName()
    {
        return $this->getConfigData()->getRealmName();
    }

    /**
     * Returns the parsed username.
     *
     * @return string|null
     */
    public function getUsername()
    {
        $authData = $this->getAuthData();
        return isset($authData['username']) ? $authData['username'] : null;
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
        // set vars internally
        $this->reqMethod = $request->getMethod();

        // parse auth data
        $this->parse($request, $response);
    }

    /**
     * Parses the request for the necessary, authentication adapter specific, login credentials.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface $request The request with the content of authentication data sent by client
     *
     * @return void
     */
    abstract protected function parse(RequestInterface $request);

    /**
     * Will prepare the authentication class's authentication adapter based on its configuration.
     *
     * @return void
     *
     * @throws \AppserverIo\Http\Authentication\AuthenticationException
     */
    protected function prepareAdapter()
    {
        $this->securityDomain = $this->getAuthenticationManager()->lookup($this->getRealmName());
    }

    /**
     * Verifies everything to be ready for authenticate for specific type.
     *
     * @return boolean
     *
     * @throws \AppserverIo\Http\Authentication\AuthenticationException
     */
    public function verify()
    {
        // set internal var refs
        $authData = $this->getAuthData();

        // check if credentials are empty
        if (empty($authData)) {
            return false;
        }

        return true;
    }

    /**
     * Verifies configuration setting and throws exception.
     *
     * @return void
     *
     * @throws \AppserverIo\Http\Authentication\AuthenticationException
     */
    protected function verifyConfig()
    {
    }

    /**
     * Add's a new adapter type to this authentication type.
     *
     * @param string $adapterType The supported adapter type
     *
     * @return void
     */
    public function addSupportedAdapter($adapterType)
    {
        $this->supportedAdapters[] = $adapterType;
    }

    /**
     * Whether or not the adapter is supported with a this authentication type.
     *
     * @param string $adapterType The adapter type
     *
     * @return boolean
     */
    public function isAdapterSupported($adapterType)
    {
        return in_array($adapterType, $this->supportedAdapters);
    }
}
