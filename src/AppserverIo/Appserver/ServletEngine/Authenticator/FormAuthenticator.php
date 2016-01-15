<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authenticator\FormAuthenticator
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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Collections\HashMap;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\ServletEngine\Security\Auth\Callback\SecurityAssociationHandler;
use AppserverIo\Appserver\ServletEngine\Security\SimplePrincipal;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FormAuthenticator extends AbstractAuthenticator
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

            $callbackHandler = new SecurityAssociationHandler(new SimplePrincipal($this->getUsername()), $this->getPassword());

            $this->getAuthenticationManager()->getRealm($this->getRealmName())->authenticate($this->getUsername(), $callbackHandler);

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
