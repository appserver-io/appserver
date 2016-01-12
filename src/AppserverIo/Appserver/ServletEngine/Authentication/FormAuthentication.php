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

use AppserverIo\Collections\HashMap;
use AppserverIo\Lang\String;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
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
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function parse(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // load username and password from the request
        $username = $servletRequest->getParam('username');
        $password = $servletRequest->getParam('password');

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
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the request can't be authenticated
     */
    public function authenticate(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        try {
            // parse authentication data from the servlet request
            $this->parse($servletRequest, $servletResponse);

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

                    // initialize initialize subject, callback handler and params
                    $subject = new Subject();
                    $principal = $loginModule->createIdentity(new String($this->getUsername()));
                    $callbackHandler = new SecurityAssociationHandler($principal, new String($this->getPassword()));
                    $params = new HashMap($loginModuleNode->getParamsAsArray());

                    // initialize the login module, try to login and commit
                    $loginModule->initialize($subject, $callbackHandler, $sharedState, $params);
                    $loginModule->login();
                    $loginModule->commit();

                    // finally add the the subject to the request
                    $servletRequest->setSubject($subject);
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
