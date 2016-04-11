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
use AppserverIo\Collections\ArrayList;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Security\Utils\Constants;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Servlet\Http\HttpSessionInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Http\Authentication\AuthenticationException;
use AppserverIo\Appserver\ServletEngine\Security\RealmInterface;
use AppserverIo\Appserver\ServletEngine\Utils\RequestHandlerKeys;
use AppserverIo\Appserver\ServletEngine\Authenticator\Utils\FormKeys;

/**
 * A form based authenticator implementation.
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
     * @return boolean TRUE if authentication has already been processed on a request before, else FALSE
     * @throws \AppserverIo\Http\Authentication\AuthenticationException Is thrown if the request can't be authenticated
     */
    public function authenticate(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // start the session, if not already done
        /** @var \AppserverIo\Psr\Servlet\Http\HttpSessionInterface $session */
        $session = $servletRequest->getSession(true);
        // start the session if not already done
        if ($session->isStarted() === false) {
            $session->start();
        }

        // try to load the principal from the session if available
        if ($session->hasKey(Constants::PRINCIPAL)) {
            if ($session->getData(Constants::PRINCIPAL) instanceof PrincipalInterface) {
                // invoke the onCache callback and return
                $this->onCache($servletRequest, $servletResponse);
                return true;
            }
        }

        // is this the re-submit of the original request URI after successful
        // authentication? If so, forward the *original* request instead
        if ($this->matchRequest($servletRequest)) {
            // invoke the onRestore callback and return
            $this->onResubmit($servletRequest, $servletResponse);
            return true;
        }

        // is this the action request from the login page?
        if (FormKeys::FORM_ACTION !== pathinfo($servletRequest->getRequestUri(), PATHINFO_FILENAME)) {
            // invoke the onLogin callback and redirect to the login page
            $this->onLogin($servletRequest, $servletResponse);
            return false;
        }

        // invoke the onCredentials callback to load the credentials from the request
        $this->onCredentials($servletRequest, $servletResponse);

        // load the realm to authenticate this request for
        /** @var AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm */
        $realm = $this->getAuthenticationManager()->getRealm($this->getRealmName());

        // authenticate the request and initialize the user principal
        $userPrincipal = $realm->authenticate($this->getUsername(), $this->getPassword());

        // query whether or not the realm returned an authenticated user principal
        if ($userPrincipal == null) {
            // invoke the onFailure callback and forward the user to the error page
            $this->onFailure($realm, $servletRequest, $servletResponse);
            return false;
        }

        // invoke the onSuccess callback and redirect the user to the original page
        $this->onSuccess($userPrincipal, $servletRequest, $servletResponse);
        return false;
    }

    /**
     * Register's the user principal and the authenticytion in the request and session.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     * @param \AppserverIo\Psr\Security\PrincipalInterface               $userPrincipal   The actual user principal
     *
     * @return void
     */
    protected function register(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse,
        PrincipalInterface $userPrincipal
    ) {

        // add the user principal and the authentication type to the request
        $servletRequest->setUserPrincipal($userPrincipal);
        $servletRequest->setAuthType($this->getAuthType());

        // set username and password in the session
        if ($session = $servletRequest->getSession()) {
            $session->putData(Constants::PRINCIPAL, $userPrincipal);
        }
    }

    /**
     * Forward's the request to the stored one or, if the user has not been on
     * any page before, the application's base URL.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function forwardToFormRequest(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // load the session from the request
        $session = $servletRequest->getSession();

        // initialize the location to redirect to
        $location = '/';

        // prepend the base modifier if available
        if ($baseModifier = $servletRequest->getBaseModifier()) {
            $location = $baseModifier;
        }

        // query whether or not we found the original request to redirect to
        if ($session && $session->hasKey(Constants::FORM_REQUEST)) {
            // load the original request
            $req = $session->getData(Constants::FORM_REQUEST);

            // initialize the location to redirect to
            $location = $req->requestUri;

            // prepare URI + query string to redirect to
            if ($queryString = $req->queryString) {
                $location .= '?' . $queryString;
            }
        }

        // redirect to the original location
        $servletRequest->setDispatched(true);
        $servletResponse->setStatusCode(303);
        $servletResponse->addHeader(Protocol::HEADER_LOCATION, $location);
    }

    /**
     * Forward's the request to the configured login page.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function forwardToLoginPage(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // query whether or not we've a valid form login configuration
        if ($formLoginConfig = $this->getConfigData()->getFormLoginConfig()) {
            if ($formLoginPage = $formLoginConfig->getFormLoginPage()) {
                // initialize the location to redirect to
                $location = $formLoginPage->__toString();
                if ($baseModifier = $servletRequest->getBaseModifier()) {
                    $location = $baseModifier . $location;
                }

                // redirect to the configured login page
                $servletRequest->setDispatched(true);
                $servletResponse->setStatusCode(307);
                $servletResponse->addHeader(Protocol::HEADER_LOCATION, $location);
                return;
            }
        }

        // redirect to the default error page
        $servletRequest->setAttribute(
            RequestHandlerKeys::ERROR_MESSAGE,
            'Please configure a form-login-page when using auth-method \'Form\' in the login-config of your application\'s web.xml'
        );
        $servletRequest->setDispatched(true);
        $servletResponse->setStatusCode(500);
    }

    /**
     * Forward's the request to the configured error page.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function forwardToErrorPage(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // query whether or not we've an error page configured
        if ($formLoginConfig = $this->getConfigData()->getFormLoginConfig()) {
            if ($formErrorPage = $formLoginConfig->getFormErrorPage()) {
                // initialize the location to redirect to
                $location = $formErrorPage->__toString();
                if ($baseModifier = $servletRequest->getBaseModifier()) {
                    $location = $baseModifier . $location;
                }

                // redirect to the configured error page
                $servletRequest->setDispatched(true);
                $servletResponse->setStatusCode(307);
                $servletResponse->addHeader(Protocol::HEADER_LOCATION, $location);
                return;
            }
        }

        // redirect to the default error page
        $servletRequest->setAttribute(
            RequestHandlerKeys::ERROR_MESSAGE,
            'Please configure a form-error-page when using auth-method \'Form\' in the login-config of your application\'s web.xml'
        );
        $servletRequest->setDispatched(true);
        $servletResponse->setStatusCode(500);
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
    public function login(
        String $username,
        String $password,
        HttpServletRequestInterface $servletRequest
    ) {

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

        // remove user principal and authentication method from request
        $servletRequest->setUserPrincipal();
        $servletRequest->setAuthType();

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
        if ($session->hasKey(Constants::FORM_REQUEST) === false) {
            return false;
        }

        // if yes, compare the request URI and check for a valid princial
        if ($req = $session->getData(Constants::FORM_REQUEST)) {
            // query whether or not we've a valid princial
            if (isset($req->principal) === false) {
                return false;
            }
            // compare the request URI
            return $servletRequest->getRequestUri() === $req->requestUri;
        }

        // return FALSE if the request doesn't match
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
    protected function saveRequest(
        HttpServletRequestInterface $servletRequest,
        HttpSessionInterface $session
    ) {

        // initialize an empty instance
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
        $session->putData(Constants::FORM_REQUEST, $req);
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
    protected function restoreRequest(
        HttpServletRequestInterface $servletRequest,
        HttpSessionInterface $session
    ) {

        // query whether or not we can find the original request in the session
        if ($session->hasKey(Constants::FORM_REQUEST)) {
            // load the origin request from the session
            $req = $session->getData(Constants::FORM_REQUEST);

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
     * Will be invoked to load the credentials from the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function onCredentials(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // try to load username and password from the request instead
        if ($servletRequest->hasParameter(FormKeys::USERNAME) &&
            $servletRequest->hasParameter(FormKeys::PASSWORD)
        ) {
            // load username/password from the request
            $this->username = new String($servletRequest->getParameter(FormKeys::USERNAME));
            $this->password = new String($servletRequest->getParameter(FormKeys::PASSWORD));
        }
    }

    /**
     * Will be invoked to handle a cached authentication request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function onCache(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // load the session from the request
        if ($session = $servletRequest->getSession()) {
            // add the user principal and the authentication type to the request
            $this->register($servletRequest, $servletResponse, $session->getData(Constants::PRINCIPAL));
        }
    }

    /**
     * Will be invoked when login fails for some reasons.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Security\RealmInterface $realm           The realm instance containing the exception stack
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface    $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface   $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function onFailure(
        RealmInterface $realm,
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // load the session from the request
        if ($session = $servletRequest->getSession()) {
            // prepare the ArrayList for the login errors
            $formErrors = new ArrayList();

            // transform the realm's exception stack into simple error messages
            foreach ($realm->getExceptionStack() as $e) {
                $formErrors->add($e->getMessage());
            }

            // add the error messages to the session
            $session->putData(Constants::FORM_ERRORS, $formErrors);
        }

        // forward to the configured error page
        $this->forwardToErrorPage($servletRequest, $servletResponse);
    }

    /**
     * Will be invoked on a successfull login.
     *
     * @param \AppserverIo\Psr\Security\PrincipalInterface               $userPrincipal   The user principal logged into the system
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function onSuccess(
        PrincipalInterface $userPrincipal,
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // add the user principal and the authentication type to the request
        $this->register($servletRequest, $servletResponse, $userPrincipal);

        // forward to the stored request
        $this->forwardToFormRequest($servletRequest, $servletResponse);
    }

    /**
     * Will be invoked to request authentication.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function onLogin(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // load the session from the request
        if ($session = $servletRequest->getSession()) {
            // save the request (to redirect after a successful login)
            $this->saveRequest($servletRequest, $session);
        }

        // forward the user to the login page
        $this->forwardToLoginPage($servletRequest, $servletResponse);
    }

    /**
     * Will be invoked when login will be re-submitted.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The servlet request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response instance
     *
     * @return void
     */
    protected function onResubmit(
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // load the session from the request
        if ($session = $servletRequest->getSession()) {
            // restore the old request from the session
            $this->restoreRequest($servletRequest, $session);
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
