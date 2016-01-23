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
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\Http\HttpSessionInterface;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\ServletEngine\Authenticator\Utils\SessionKeys;
use AppserverIo\Appserver\ServletEngine\Authenticator\Utils\FormKeys;

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

        // start the session, if not already done
        /** @var \AppserverIo\Psr\Servlet\Http\HttpSessionInterface $session */
        $session = $servletRequest->getSession(true);
        $session->start();

        // try to load username/password from the session if available
        if ($session->hasKey(SessionKeys::USERNAME) && $session->hasKey(SessionKeys::PASSWORD)) {
            // load username/password from the session
            $this->username = $session->getData(SessionKeys::USERNAME);
            $this->password = $session->getData(SessionKeys::PASSWORD);

            // load the realm to authenticate this request for
            /** @var AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm */
            $realm = $this->getAuthenticationManager()->getRealm($this->getRealmName());

            // authenticate the request and initialize the user principal
            if ($userPrincipal = $realm->authenticate($this->getUsername(), $this->getPassword())) {
                // set the the principal and the authentication method in the request
                $servletRequest->setUserPrincipal($userPrincipal);
                $servletRequest->setAuthType($this->getAuthType());

                // set username and password in the session
                if ($session = $servletRequest->getSession()) {
                    $session->putData(SessionKeys::USERNAME, $this->getUsername());
                    $session->putData(SessionKeys::PASSWORD, $this->getPassword());
                }

                return true;
            }
        }

        // is this the re-submit of the original request URI after successful
        // authentication? If so, forward the *original* request instead
        if ($this->matchRequest($servletRequest)) {
            // restore the old request from the session
            $this->restoreRequest($servletRequest, $session);

            return true;
        }

        // Is this the action request from the login page?
        if (!preg_match(sprintf('/.*\%s/', FormKeys::FORM_ACTION), $servletRequest->getRequestUri())) {
            // save the request so we can redirect after a successful login
            $this->saveRequest($servletRequest, $session);

            // query whether or not we've a valid form login configuration
            if ($formLoginConfig = $this->getConfigData()->getFormLoginConfig()) {
                // load the configured error page
                $formLoginPage = $formLoginConfig->getFormLoginPage()->__toString();
                // redirect to the configured login page
                $servletResponse->setStatusCode(307);
                $servletResponse->addHeader(Protocol::HEADER_LOCATION, $formLoginPage);
            }

            return false;
        }

        // try to load username and password from the request instead
        if ($servletRequest->hasParameter(FormKeys::USERNAME) && $servletRequest->hasParameter(FormKeys::PASSWORD)) {
            // load username/password from the request
            $this->username = new String($servletRequest->getParameter(FormKeys::USERNAME));
            $this->password = new String($servletRequest->getParameter(FormKeys::PASSWORD));
        }

        // load the realm to authenticate this request for
        /** @var AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm */
        $realm = $this->getAuthenticationManager()->getRealm($this->getRealmName());

        // authenticate the request and initialize the user principal
        $userPrincipal = $realm->authenticate($this->getUsername(), $this->getPassword());

        // query whether or not the realm returned an authenticated user principal
        if ($userPrincipal == null) {
            // query whether or not we've a valid form login configuration
            if ($formLoginConfig = $this->getConfigData()->getFormLoginConfig()) {
                // load the configured error page
                $formErrorPage = $formLoginConfig->getFormErrorPage()->__toString();
                // redirect to the configured error page
                $servletResponse->setStatusCode(307);
                $servletResponse->addHeader(Protocol::HEADER_LOCATION, $formErrorPage);
            }

            return false;
        }

        // add the user principal and the authentication type to the request
        $servletRequest->setUserPrincipal($userPrincipal);
        $servletRequest->setAuthType($this->getAuthType());

        // set username and password in the session
        if ($session = $servletRequest->getSession()) {
            $session->putData(SessionKeys::USERNAME, $this->getUsername());
            $session->putData(SessionKeys::PASSWORD, $this->getPassword());
        }

        // query whether or not we found the original request to redirect to
        if ($session->hasKey(SessionKeys::FORM_REQUEST)) {
            // load the original request
            $req = $session->getData(SessionKeys::FORM_REQUEST);

            // prepare URI + query string to redirect to
            $location = $req->requestUri;
            if ($queryString = $req->queryString) {
                $location .= '?' . $queryString;
            }

            // redirect to the original location
            $servletResponse->setStatusCode(307);
            $servletResponse->addHeader(Protocol::HEADER_LOCATION, $location);
        }

        return false;
    }

    /**
     * Tries the login the passed username/password combination for the login configuration.
     *
     * @param \AppserverIo\Lang\String                                  $username       The username used to login
     * @param \AppserverIo\Lang\String                                  $password       The password used to authenticate the user
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request instance
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface The authenticated user principal
     */
    public function login(String $username, String $password, HttpServletRequestInterface $servletRequest)
    {

        // load the realm to authenticate this request for
        /** @var AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm */
        $realm = $this->getAuthenticationManager()->getRealm($this->getRealmName());

        // authenticate the request and initialize the user principal
        $userPrincipal = $realm->authenticate($username, $password);

        // query whether or not we can authenticate the user
        if ($userPrincipal == null) {
            throw new ServletException(sprintf('Can\'t authenticate user %s', $username));
        }

        // return's the user principal
        return $userPrincipal;
    }

    /**
     * Logout the actual user from the session.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request instance
     *
     * @return void
     */
    public function logout(HttpServletRequestInterface $servletRequest)
    {

        // remove user principal and authenticatio method from request
        $servletRequest->setUserPrincipal(null);
        $servletRequest->setAuthMethod(null);

        // destroy the session explicit
        if ($session = $servletRequest->getSession()) {
            $session->destroy('Explicit logout by user!');
        }
    }

    /**
     * Does this request match the saved one, so that it must be the redirect we signaled after
     * successful authentication?
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request instance
     *
     * @return boolean TRUE if the request matches the saved one, else FALSE
     */
    protected function matchRequest(HttpServletRequestInterface $servletRequest)
    {

        // load the session from the request
        $session = $servletRequest->getSession();

        // query wheter or not a session is available
        if ($session == null) {
            return false;
        }

        // query whether or not we can find the original request data
        if ($session->hasKey(SessionKeys::FORM_REQUEST) === false) {
            return false;
        }

        // if yes, compare the request URI and check for a valid princial
        if ($req = $session->getData(SessionKeys::FORM_REQUEST)) {
            // query whether or not we've a valid princial
            if (isset($req->principal) === false) {
                return false;
            }

            // compare the request URI
            return $servletRequest->getRequestUri() === $req->requestUri;
        }

        return false;
    }

    /**
     * Stores the data of the passed request in the also passed session.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpSessionInterface        $session        The session instance
     *
     * @return void
     */
    protected function saveRequest(HttpServletRequestInterface $servletRequest, HttpSessionInterface $session)
    {

        // initialize an empyt instance
        $req = new \stdClass();

        // set the data of the passed request
        $req->requestUri = $servletRequest->getRequestUri();
        $req->method = $servletRequest->getMethod();
        $req->queryString = $servletRequest->getQueryString();
        $req->documentRoot = $servletRequest->getDocumentRoot();
        $req->serverName = $servletRequest->getServerName();
        $req->bodyContent = $servletRequest->getBodyContent();
        $req->cookies = $servletRequest->getCookies();
        $req->headers = $servletRequest->getHeaders();
        $req->principal = $servletRequest->getUserPrincipal();
        $req->requestUrl = $servletRequest->getRequestUrl();

        // store the data in the session
        $session->putData(SessionKeys::FORM_REQUEST, $req);
    }

    /**
     * Populates the passed request with the request data of the original request
     * found in the also passed session.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpSessionInterface        $session        The session instance
     *
     * @return void
     */
    protected function restoreRequest(HttpServletRequestInterface $servletRequest, HttpSessionInterface $session)
    {

        // query whether or not we can find the original request in the session
        if ($session->hasKey(SessionKeys::FORM_REQUEST)) {
            // load the origin request from the session
            $req = $session->getData(SessionKeys::FORM_REQUEST);

            // restore the original request data
            $servletRequest->setHeaders($req->headers);
            $servletRequest->setCookies($req->cookies);
            $servletRequest->setUserPrincipal($req->userPrincipal);
            $servletRequest->setServerName($req->serverName);
            $servletRequest->setQueryString($req->queryString);
            $servletRequest->setRequestUri($req->requestUri);
            $servletRequest->setDocumentRoot($req->documentRoot);
            $servletRequest->setRequestUrl($req->requestUrl);

            // set the body content if we can find one
            if ($servletRequest->getHeader(Protocol::HEADER_CONTENT_LENGTH) > 0) {
                $servletRequest->setBodyStream($req->bodyContent);
            }
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
