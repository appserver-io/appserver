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
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\Naming\Utils\NamingDirectoryKeys;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\ServletEngine\Authenticator\Utils\SessionKeys;
use AppserverIo\Appserver\ServletEngine\Security\SimplePrincipal;
use AppserverIo\Appserver\ServletEngine\Security\Auth\Callback\SecurityAssociationHandler;
use AppserverIo\Psr\Security\Auth\Login\LoginException;

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

        // start the session, if not already done
        /** @var \AppserverIo\Psr\Servlet\Http\HttpSessionInterface $session */
        $session = $servletRequest->getSession(true);
        $session->start();

        // try to load username and password from the request instead
        if ($servletRequest->hasParameter('username') &&
            $servletRequest->hasParameter('password')
        ) {
            $this->username = new String($servletRequest->getParameter('username'));
            $this->password = new String($servletRequest->getParameter('password'));

            error_log(sprintf('Found %s/%s in request', $this->username, $this->password));

            return true;
        }

        // try to load username/password from the session if available
        if ($session->hasKey(SessionKeys::USERNAME) &&
            $session->hasKey(SessionKeys::PASSWORD)
        ) {
            $this->username = $session->getData(SessionKeys::USERNAME);
            $this->password = $session->getData(SessionKeys::PASSWORD);

            error_log(sprintf('Found %s/%s in session %s', $this->username, $this->password, $session->getId()));

            return true;
        }

        // we can't find username/password in session or request
        return false;
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
            if ($this->parse($servletRequest, $servletResponse) === false) {
                throw new \Exception('Invalid username or password');
            }

            // prepare the callback handler
            $callbackHandler = new SecurityAssociationHandler(new SimplePrincipal($this->username), $this->password);

            // load the realm to authenticate this request for
            /** @var AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm */
            $realm = $this->getAuthenticationManager()->getRealm($this->getRealmName());

            // authenticate the request and initialize the user principal
            $userPrincipal = $realm->authenticate($this->username, $callbackHandler);

            // authenticate/re-authenticate the user and set the principal in the request
            $servletRequest->setUserPrincipal($userPrincipal);
            $servletRequest->setAuthType(FormAuthenticator::AUTH_TYPE);

            // set username and password in the session
            if ($session = $servletRequest->getSession()) {
                $session->putData(SessionKeys::USERNAME, $this->username);
                $session->putData(SessionKeys::PASSWORD, $this->password);
            }

        } catch (LoginException $le) {
            // query whether or not we've a valid form login configuration
            if ($formLoginConfig = $this->getConfigData()->getFormLoginConfig()) {
                // load the configured error page
                $formErrorPage = $formLoginConfig->getFormErrorPage()->__toString();
                // redirect to the configured error page
                $servletResponse->addHeader(Protocol::HEADER_LOCATION, $formErrorPage);
            }

            // re-throw the exception
            throw $le;

        } catch (\Exception $e) {
            // query whether or not we've a valid form login configuration
            if ($formLoginConfig = $this->getConfigData()->getFormLoginConfig()) {
                // load the configured error page
                $formLoginPage = $formLoginConfig->getFormLoginPage()->__toString();
                // redirect to the configured login page
                $servletResponse->addHeader(Protocol::HEADER_LOCATION, $formLoginPage);
            }

            // re-throw the exception
            throw $e;
        }
    }

    /**
     * Returns the parsed password.
     *
     * @return \AppserverIo\Lang\String The password
     */
    public function getPassword()
    {
        return $this->password ? $this->password : null;
    }
}
