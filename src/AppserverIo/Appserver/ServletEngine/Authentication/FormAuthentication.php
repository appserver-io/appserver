<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\FormAuthentication
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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Collections\HashMap;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Http\Authentication\Adapters\HtpasswdAdapter;
use AppserverIo\Appserver\ServletEngine\Authentication\Callback\SecurityAssociationHandler;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FormAuthentication extends AbstractAuthentication
{

    /**
     * Defines the auth type which should match the client request type definition
     *
     * @var string AUTH_TYPE
     */
    const AUTH_TYPE = 'Form';

    /**
     * Defines the default authentication adapter used if none was specified
     *
     * @var string DEFAULT_ADAPTER
     */
    const DEFAULT_ADAPTER = HtpasswdAdapter::ADAPTER_TYPE;

    /**
     * Constructs the authentication type.
     *
     * @param \AppserverIo\Configuration\NodeInterface                                           $configData            The configuration data for auth type instance
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationManagerInterface $authenticationManager The authentication manager instance
     */
    public function __construct(NodeInterface $configData, AuthenticationManagerInterface $authenticationManager)
    {

        // initialize the supported adapter types
        $this->addSupportedAdapter(HtpasswdAdapter::getType());

        // initialize the instance
        parent::__construct($configData, $authenticationManager);
    }

    /**
     * Returns the authentication header for response to set
     *
     * @return string
     */
    public function getAuthenticateHeader()
    {
        return '';
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

        // load username and password from the request
        $username = $request->getParam('username');
        $password = $request->getParam('password');

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
     * Try to authenticate against the configured adapter.
     *
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface $response The response sent back to the client
     *
     * @return void
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the request can't be authenticated
     */
    public function authenticate(ResponseInterface $response)
    {

        try {
            // verify everything to be ready for auth if not return false
            if ($this->verify() === false) {
                throw new \Exception('Invalid username or password');
            }

            // load the security domains authentication configuration
            /** @var \AppserverIo\Appserver\Core\Api\Node\AuthConfigNodeInterface $authConfigNode */
            if ($authConfigNode = $this->securityDomain->getConfiguration()->getAuthConfig()) {
                // prepare the map for the shared state data
                $sharedState = new HashMap();
                // prepare the login modules of the security domain
                /** @var \AppserverIo\Appserver\Core\Api\Node\LoginModuleNodeInterface $loginModuleNode */
                foreach ($authConfigNode->getLoginModules() as $loginModuleNode) {
                    // create a new instance of the login module and add it to the array list
                    $reflectionClass = new ReflectionClass($loginModuleNode->getType());

                    // initialize the login module instance
                    /** @var \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginModuleInterface $loginModule */
                    $loginModule = $reflectionClass->newInstance();

                    // initialize the principal instance with the username found in the request
                    $principal = $loginModule->createIdentity(new String($this->getUsername()));

                    // initialize the callback handler with the principal and the credential
                    $callbackHandler = new SecurityAssociationHandler($principal, new String($this->getPassword()));

                    // initialize the login module and
                    $loginModule->initialize($callbackHandler, $sharedState, new HashMap($loginModuleNode->getParamsAsArray()));
                    $loginModule->login();
                }
            }

        } catch (\Exception $e) {
            // log the exception
            $this->getAuthenticationManager()
                 ->getApplication()
                 ->getNamingDirectory()
                 ->search(NamingDirectoryKeys::SYSTEM_LOGGER)->error($e->__toString());

            // throw an authentication exception
            throw new AuthenticationException($e->getMessage(), 401);
        }
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
