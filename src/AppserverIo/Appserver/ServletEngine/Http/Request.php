<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Http\Request
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

namespace AppserverIo\Appserver\ServletEngine\Http;

use AppserverIo\Lang\String;
use AppserverIo\Http\HttpProtocol;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Psr\Context\ContextInterface;
use AppserverIo\Psr\HttpMessage\PartInterface;
use AppserverIo\Psr\HttpMessage\CookieInterface;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Psr\Auth\AuthenticationManagerInterface;
use AppserverIo\Appserver\ServletEngine\SessionManagerInterface;

/**
 * A Http servlet request implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Request implements HttpServletRequestInterface, ContextInterface
{

    /**
     * The path (URI) to the servlet.
     *
     * @var string
     */
    protected $servletPath;

    /**
     * The request context.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Http\RequestContextInterface
     */
    protected $requestHandler;

    /**
     * The request URI.
     *
     * @var string
     */
    protected $requestUri;

    /**
     * The request URL.
     *
     * @var string
     */
    protected $requestUrl;

    /**
     * The new session name.
     *
     * @var string
     */
    protected $requestedSessionName;

    /**
     * The new session-ID.
     *
     * @var string
     */
    protected $requestedSessionId;

    /**
     * The servlet response instance.
     *
     * @var \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface
     */
    protected $response;

    /**
     * The server name.
     *
     * @var string
     */
    protected $serverName;

    /**
     * The query string.
     *
     * @var string
     */
    protected $queryString;

    /**
     * The absolute path info.
     *
     * @var string
     */
    protected $pathInfo;

    /**
     * The document root.
     *
     * @var string
     */
    protected $documentRoot;

    /**
     * Base modifier which allows for base path generation within rewritten URL environments.
     *
     * @var string
     */
    protected $baseModifier;

    /**
     * The body content stream resource.
     *
     * @var resource
     */
    protected $bodyStream;

    /**
     * The request context instance.
     *
     * @var \AppserverIo\Psr\Context\ContextInterface
     */
    protected $context;

    /**
     * The application context name.
     *
     * @var string
     */
    protected $contextPath;

    /**
     * Whether or not the request has been dispatched.
     *
     * @var boolean
     */
    protected $dispatched = false;

    /**
     * The HTTP request instance.
     *
     * @var \AppserverIo\Psr\HttpMessage\RequestInterface
     */
    protected $httpRequest;

    /**
     * The server variables.
     *
     * @var array
     */
    protected $serverVars = array();

    /**
     * The uploaded part instances.
     *
     * @var array
     */
    protected $parts = array();

    /**
     * The available request handlers.
     *
     * @var array
     */
    protected $handlers = array();

    /**
     * Array that contains the attributes of this context.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * The user principal of the authenticated user or NULL if the user has not been authenticated.
     *
     * @var \AppserverIo\Psr\Security\PrincipalInterface
     */
    protected $userPrincipal;

    /**
     * The request's authentication type.
     *
     * @var string
     */
    protected $authType;

    /**
     * The session manager instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * The session manager instance.
     *
     * @var \AppserverIo\Psr\Auth\AuthenticationManagerInterface
     */
    protected $authenticationManager;

    /**
     * Initializes the request object with the default properties.
     */
    public function __construct()
    {

        // init body stream
        $this->bodyStream = '';

        // the request has not been dispatched initially
        $this->dispatched = false;

        // initialize the instances
        $this->context = null;
        $this->response = null;
        $this->httpRequest = null;
        $this->userPrincipal = null;
        $this->requestHandler = null;
        $this->sessionManager = null;
        $this->authenticationManager = null;

        // initialize the strings
        $this->pathInfo = '';
        $this->serverName = '';
        $this->requestUri = '';
        $this->requestUrl = '';
        $this->queryString = '';
        $this->servletPath = '';
        $this->baseModifier = '';
        $this->documentRoot = '';
        $this->requestedSessionId = '';
        $this->requestedSessionName = '';

        // reset the server variables and the parts
        $this->parts = array();
        $this->handlers = array();
        $this->serverVars = array();
        $this->attributes = array();
    }

    /**
     * Adds the attribute with the passed name to this context.
     *
     * @param string $key   The key to add the value with
     * @param mixed  $value The value to add to the context
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     * @see \AppserverIo\Psr\Context\Context::getAttribute($key)
     */
    public function getAttribute($key)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
    }

    /**
     * Initializes the servlet request with the data from the injected HTTP request instance.
     *
     * @return void
     */
    public function init()
    {

        // reset the servlet request
        $httpRequest = $this->getHttpRequest();

        // initialize the parts
        foreach ($httpRequest->getParts() as $part) {
            $this->addPart(Part::fromHttpRequest($part));
        }

        // set the body content if we can find one
        if ($httpRequest->getHeader(HttpProtocol::HEADER_CONTENT_LENGTH) > 0) {
            $this->setBodyStream($httpRequest->getBodyContent());
        }

        // copy server variables to members
        $this->setServerName($this->getServerVar(ServerVars::SERVER_NAME));
        $this->setQueryString($this->getServerVar(ServerVars::QUERY_STRING));
        $this->setRequestUri($this->getServerVar(ServerVars::X_REQUEST_URI));
        $this->setDocumentRoot($this->getServerVar(ServerVars::DOCUMENT_ROOT));
        $this->setRequestUrl($this->getServerVar(ServerVars::HTTP_HOST) . $this->getServerVar(ServerVars::X_REQUEST_URI));
    }

    /**
     * Prepares the request instance.
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if the request can't be prepared, because no file handle exists
     */
    public function prepare()
    {

        // prepare the context path
        $contextPath = str_replace($this->getContext()->getAppBase(), '', $this->getContext()->getWebappPath());

        // set the context path
        $this->setContextPath($contextPath);

        // Fixed #735 - Endless Loop for URLs without servlet name
        // Load the request URI and query string from the server vars, because we have to
        // take care about changes from other modules like directory or rewrite module!
        $uri = $this->getRequestUri();
        $queryString = $this->getQueryString();

        // get uri without querystring
        $uriWithoutQueryString = str_replace('?' . $queryString, '', $uri);

        // initialize the path information and the directory to start with
        list ($dirname, $basename, $extension) = array_values(pathinfo($uriWithoutQueryString));

        // make the registered handlers local
        $handlers = $this->getHandlers();

        // descent the directory structure down to find the (almost virtual) servlet file
        do {
            // bingo we found a (again: almost virtual) servlet file
            if (isset($handlers[".$extension"])) {
                // prepare the servlet path (we've to take care, because the
                // pathinfo() function converts / to \ on Windows OS
                if ($dirname === DIRECTORY_SEPARATOR) {
                    $servletPath = '/' . $basename;
                } else {
                    $servletPath = $dirname . '/' . $basename;
                }

                // we set the basename, because this is the servlet path
                $this->setServletPath($servletPath);

                // we set the path info, what is the request URI with stripped dir- and basename
                $this->setPathInfo(str_replace($servletPath, '', $uriWithoutQueryString));

                // we've found what we were looking for, so break here
                break;
            }

            // break if we finally can't find a servlet to handle the request
            if ($dirname === '/') {
                throw new ServletException(
                    sprintf('Can\'t find a handler for URI %s, either ', $uri)
                );
            }

            // descend down the directory tree
            list ($dirname, $basename, $extension) = array_values(pathinfo($dirname));

        } while ($dirname !== false); // stop until we reached the root of the URI

        // prepare and set the servlet path
        $this->setServletPath(str_replace($contextPath, '', $this->getServletPath()));

        // prepare the base modifier which allows our apps to provide a base URL
        $webappsDir = str_replace($this->getContext()->getBaseDirectory(), '', $this->getContext()->getAppBase());
        $relativeRequestPath = strstr($this->getDocumentRoot(), $webappsDir);
        $proposedBaseModifier = str_replace(DIRECTORY_SEPARATOR, '/', str_replace($webappsDir, '', $relativeRequestPath));

        //  prepare the base modifier
        if (strpos($proposedBaseModifier, $contextPath) === 0) {
            $this->setBaseModifier('');
        } else {
            $this->setBaseModifier($contextPath);
        }
    }

    /**
     * Injects the context that allows access to session and
     * server information.
     *
     * @param \AppserverIo\Psr\Context\ContextInterface $context The request context instance
     *
     * @return void
     */
    public function injectContext(ContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Injects the server variables.
     *
     * @param array $serverVars The server variables
     *
     * @return void
     */
    public function injectServerVars(array $serverVars)
    {
        $this->serverVars = $serverVars;
    }

    /**
     * Injects the Http request instance.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface $httpRequest The Http request instance
     *
     * @return void
     */
    public function injectHttpRequest(RequestInterface $httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * Injects the session manager instance.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionManagerInterface $sessionManager The session manager instance
     *
     * @return void
     */
    public function injectSessionManager(SessionManagerInterface $sessionManager)
    {
        $this->sessionManager = $sessionManager;
    }

    /**
     * Return's the session manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionManagerInterface The session manager instance
     */
    public function getSessionManager()
    {
        return $this->sessionManager;
    }

    /**
     * Injects the authentication manager instance.
     *
     * @param \AppserverIo\Psr\Auth\AuthenticationManagerInterface $authenticationManager The authentication manager instance
     *
     * @return void
     */
    public function injectAuthenticationManager(AuthenticationManagerInterface $authenticationManager)
    {
        $this->authenticationManager = $authenticationManager;
    }

    /**
     * Return's the authentication manager instance.
     *
     * @return \AppserverIo\Psr\Auth\AuthenticationManagerInterface The authentication manager instance
     */
    public function getAuthenticationManager()
    {
        return $this->authenticationManager;
    }

    /**
     * Returns the Http request instance.
     *
     * @return \AppserverIo\Psr\HttpMessage\RequestInterface The Http request instance
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * Injects the available file handlers registered by the webserver configuration.
     *
     * @param array $handlers The available file handlers
     *
     * @return void
     */
    public function injectHandlers(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * Returns the available file handlers registered by the webserver configuration.
     *
     * @return array The file handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }

    /**
     * Returns the base modifier which allows for base path generation within rewritten URL environments
     *
     * @return string
     */
    public function getBaseModifier()
    {
        return $this->baseModifier;
    }

    /**
     * Returns the context that allows access to session and
     * server information.
     *
     * @return \AppserverIo\Psr\Context\ContextInterface The request context
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Returns a part instance
     *
     * @return \AppserverIo\Psr\HttpMessage\PartInterface
     */
    public function getHttpPartInstance()
    {
    }

    /**
     * Returns the context that allows access to session and
     * server information.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Http\RequestContextInterface The request context
     */
    public function getRequestHandler()
    {
        return $this->requestHandler;
    }

    /**
     * Sets the absolute path to the document root.
     *
     * @param string $documentRoot The document root
     *
     * @return void
     */
    public function setDocumentRoot($documentRoot)
    {
        return $this->documentRoot = $documentRoot;
    }

    /**
     * Returns the absolut path to the document root.
     *
     * @return string The document root
     */
    public function getDocumentRoot()
    {
        return $this->documentRoot;
    }

    /**
     * Sets the part of this request's URL from the protocol name up to the query string in the first line of the HTTP request.
     *
     * @param string $requestUri The request URI
     *
     * @return void
     */
    public function setRequestUri($requestUri)
    {
        $this->requestUri = $requestUri;
    }

    /**
     * Returns the part of this request's URL from the protocol name up to the query string in the first line of the HTTP request.
     *
     * @return string The request URI
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * Sets the URL the client used to make the request.
     *
     * @param string $requestUrl The request URL
     *
     * @return void
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     * Returns the URL the client used to make the request.
     *
     * @return string The request URL
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * Injects the servlet response bound to this request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $response The servlet response instance
     *
     * @return void
     */
    public function injectResponse(HttpServletResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * Returns the servlet response bound to this request.
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface The response instance
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Returns an array with all request parameters.
     *
     * @param array $parameterMap The array with the request parameters
     *
     * @return void
     */
    public function setParameterMap(array $parameterMap)
    {
        $this->getHttpRequest()->setParams($parameterMap);
    }

    /**
     * Returns an array with all request parameters.
     *
     * @return array The array with the request parameters
     */
    public function getParameterMap()
    {
        return $this->getHttpRequest()->getParams();
    }

    /**
     * Return content
     *
     * @return string $content
     */
    public function getBodyContent()
    {
        return $this->getBodyStream();
    }

    /**
     * Returns the body stream as a resource.
     *
     * @return resource The body stream
     */
    public function getBodyStream()
    {
        return $this->bodyStream;
    }

    /**
     * Set the base modifier
     *
     * @param string $baseModifier Base modifier which allows for base path generation within rewritten URL environments
     *
     * @return null
     */
    public function setBaseModifier($baseModifier)
    {
        $this->baseModifier = $baseModifier;
    }

    /**
     * Resets the stream resource pointing to body content.
     *
     * @param resource $bodyStream The body content stream resource
     *
     * @return void
     */
    public function setBodyStream($bodyStream)
    {
        $this->bodyStream = $bodyStream;
    }

    /**
     * Set protocol version
     *
     * @param string $version The http protocol version
     *
     * @return void
     */
    public function setVersion($version)
    {
        $this->getHttpRequest()->setVersion($version);
    }

    /**
     * Returns protocol version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->getHttpRequest()->getVersion();
    }

    /**
     * Queries whether the request contains a parameter or not.
     *
     * @param string $name The name of the param we're query for
     *
     * @return boolean TRUE if the parameter is available, else FALSE
     */
    public function hasParameter($name)
    {
        return $this->getHttpRequest()->hasParam($name);
    }

    /**
     * Returns the parameter with the passed name if available or null
     * if the parameter not exists.
     *
     * @param string $name The name of the parameter to return
     *
     * @return string|null The requested value
     */
    public function getParam($name)
    {
        return $this->getHttpRequest()->getParam($name);
    }

    /**
     * Returns the parameter with the passed name if available or null
     * if the parameter not exists.
     *
     * @param string  $name   The name of the parameter to return
     * @param integer $filter The filter to use
     *
     * @return string|null
     */
    public function getParameter($name, $filter = FILTER_SANITIZE_STRING)
    {
        $parameterMap = $this->getParameterMap();
        if (isset($parameterMap[$name])) {
            return filter_var($parameterMap[$name], $filter);
        }
    }

    /**
     * Returns a part object by given name
     *
     * @param string $name The name of the form part
     *
     * @return \AppserverIo\Http\HttpPart
     */
    public function getPart($name)
    {
        if (isset($this->parts[$name])) {
            return $this->parts[$name];
        }
    }

    /**
     * Returns the parts collection as array
     *
     * @return array A collection of HttpPart objects
     */
    public function getParts()
    {
        return $this->parts;
    }

    /**
     * Adds a part to the parts collection.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Http\Part $part A form part object
     * @param string                                         $name A manually defined name
     *
     * @return void
     */
    public function addPart(PartInterface $part, $name = null)
    {
        if ($name == null) {
            $name = $part->getName();
        }
        $this->parts[$name] = $part;
    }

    /**
     * Sets the application context name (application name prefixed with a slash) for the actual request.
     *
     * @param string $contextPath The application context name
     *
     * @return void
     */
    public function setContextPath($contextPath)
    {
        $this->contextPath = $contextPath;
    }

    /**
     * Returns the application context name (application name prefixed with a slash) for the actual request.
     *
     * @return string The application context name
     */
    public function getContextPath()
    {
        return $this->contextPath;
    }

    /**
     * Sets the path to the servlet used to handle this request.
     *
     * @param string $servletPath The path to the servlet
     *
     * @return void
     */
    public function setServletPath($servletPath)
    {
        $this->servletPath = $servletPath;
    }

    /**
     * Returns the path to the servlet used to handle this request.
     *
     * @return string The relative path to the servlet
     */
    public function getServletPath()
    {
        return $this->servletPath;
    }

    /**
     * Return the session identifier proposed by the actual configuration and request state.
     *
     * @return string The session identifier proposed for this request
     */
    public function getProposedSessionId()
    {

        // if no session has already been load, initialize the session manager
        /** @var \AppserverIo\Appserver\ServletEngine\SessionManagerInterface $manager */
        $manager = $this->getContext()->search('SessionManagerInterface');

        // if no session manager was found, we don't support sessions
        if ($manager == null) {
            return;
        }

        // if we can't find a requested session name, we try to load the default session cookie
        if ($this->getRequestedSessionName() == null) {
            $this->setRequestedSessionName($manager->getSessionSettings()->getSessionName());
        }

        // load the requested session ID and name
        $sessionName = $this->getRequestedSessionName();
        $id = $this->getRequestedSessionId();

        // try to load session ID from session cookie of request/response
        $cookieFound = null;
        if ($id == null && $this->getResponse()->hasCookie($sessionName)) {
            $cookieFound = $this->getResponse()->getCookie($sessionName);
        } elseif ($id == null && $this->hasCookie($sessionName)) {
            $cookieFound = $this->getCookie($sessionName);
        }

        // check if we can find a cookie
        if (is_array($cookieFound)) {
            // iterate over the cookies and try to find one that is not expired
            foreach ($cookieFound as $cookie) {
                if ($cookie instanceof CookieInterface && $cookie->isExpired() === false) {
                    $this->setRequestedSessionId($id = $cookie->getValue());
                }
            }

        // if we found a single cookie instance
        } elseif ($cookieFound instanceof CookieInterface && $cookieFound->isExpired() === false) {
            $this->setRequestedSessionId($id = $cookieFound->getValue());
        }

        // return the requested session
        return $id;
    }

    /**
     * Returns the session for this request.
     *
     * @param boolean $create TRUE to create a new session, else FALSE
     *
     * @return null|\AppserverIo\Psr\Servlet\Http\HttpSessionInterface The session instance
     *
     * @throws \Exception
     */
    public function getSession($create = false)
    {

        // load the proposed session-ID and name
        $id = $this->getProposedSessionId();
        $sessionName = $this->getRequestedSessionName();

        // if no session has already been load, initialize the session manager
        /** @var \AppserverIo\Appserver\ServletEngine\SessionManagerInterface $manager */
        $manager = $this->getSessionManager();

        // if no session manager was found, we don't support sessions
        if ($manager == null) {
            return;
        }

        // find or create a new session (if flag has been set)
        $session = $manager->find($id);

        // if we can't find a session or session has been expired and we want to create a new one
        if ($session == null && $create === true) {
            // check if a session ID has been specified
            if ($id == null) {
                // if not, generate a unique one
                $id = SessionUtils::generateRandomString();
            }

            // create a new session and register ID in request
            $session = $manager->create($id, $sessionName);
        }

        // if we can't find a session and we should NOT create one, return nothing
        if ($create === false && $session == null) {
            return;
        }

        // if we can't find a session although we SHOULD create one, we throw an exception
        if ($create === true && $session == null) {
            throw new \Exception('Can\'t create a new session!');
        }

        // initialize the session wrapper
        $wrapper = new SessionWrapper();
        $wrapper->injectSession($session);
        $wrapper->injectRequest($this);

        // return the found session
        return $wrapper;
    }

    /**
     * Returns the absolute path info started from the context path.
     *
     * @return string The absolute path info
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }

    /**
     * Sets the absolute path info started from the context path.
     *
     * @param string $pathInfo The absolute path info
     *
     * @return void
     */
    public function setPathInfo($pathInfo)
    {
        $this->pathInfo = $pathInfo;
    }

    /**
     * Adds the passed cookie to this request.
     *
     * @param \AppserverIo\Psr\HttpMessage\CookieInterface $cookie The cookie to add
     *
     * @return void
     */
    public function addCookie(CookieInterface $cookie)
    {
        $this->getHttpRequest()->addCookie($cookie);
    }

    /**
     * Returns true if the request has a cookie header with the passed
     * name, else false.
     *
     * @param string $cookieName Name of the cookie header to be checked
     *
     * @return boolean true if the request has the cookie, else false
     */
    public function hasCookie($cookieName)
    {
        return $this->getHttpRequest()->hasCookie($cookieName);
    }

    /**
     * Returns the value of the cookie with the passed name.
     *
     * @param string $cookieName The name of the cookie to return
     *
     * @return \AppserverIo\Psr\HttpMessage\CookieInterface The cookie instance
     */
    public function getCookie($cookieName)
    {
        return $this->getHttpRequest()->getCookie($cookieName);
    }

    /**
     * Return's the array with cookies.
     *
     * @return array The array with cookies
     */
    public function getCookies()
    {
        return $this->getHttpRequest()->getCookies();
    }

    /**
     * Returns header info by given name
     *
     * @param string $name The header key to name
     *
     * @return string|null
     */
    public function getHeader($name)
    {
        return $this->getHttpRequest()->getHeader($name);
    }

    /**
     * Set headers data
     *
     * @param array $headers The headers array to set
     *
     * @return void
     */
    public function setHeaders(array $headers)
    {
        $this->getHttpRequest()->setHeaders($headers);
    }

    /**
     * Returns headers data
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->getHttpRequest()->getHeaders();
    }

    /**
     * Adds a header information got from connection.
     *
     * @param string $name  The header name
     * @param string $value The headers value
     *
     * @return void
     */
    public function addHeader($name, $value)
    {
        $this->getHttpRequest()->addHeader($name, $value);
    }

    /**
     * Checks if header exists by given name.
     *
     * @param string $name The header name to check
     *
     * @return boolean
    */
    public function hasHeader($name)
    {
        return $this->getHttpRequest()->hasHeader($name);
    }

    /**
     * Set the requested session ID for this request.  This is normally called
     * by the HTTP Connector, when it parses the request headers.
     *
     * @param string $requestedSessionId The new session id
     *
     * @return void
     */
    public function setRequestedSessionId($requestedSessionId)
    {
        $this->requestedSessionId = $requestedSessionId;
    }

    /**
     * Return the session identifier included in this request, if any.
     *
     * @return string The session identifier included in this request
     */
    public function getRequestedSessionId()
    {
        return $this->requestedSessionId;
    }

    /**
     * Set the requested session name for this request.
     *
     * @param string $requestedSessionName The new session name
     *
     * @return void
     */
    public function setRequestedSessionName($requestedSessionName)
    {
        $this->requestedSessionName = $requestedSessionName;
    }

    /**
     * Return the session name included in this request, if any.
     *
     * @return string The session name included in this request
     */
    public function getRequestedSessionName()
    {
        return $this->requestedSessionName;
    }

    /**
     * Sets the flag to mark the request dispatched.
     *
     * @param boolean $dispatched TRUE if the request has already been dispatched, else FALSE
     *
     * @return void
     */
    public function setDispatched($dispatched = true)
    {
        $this->dispatched = $dispatched;
    }

    /**
     * Sets the flag that shows if the request has already been dispatched.
     *
     * @return boolean TRUE if the request has already been dispatched, else FALSE
     */
    public function isDispatched()
    {
        return $this->dispatched;
    }

    /**
     * Sets the server name.
     *
     * @param string $serverName The server name
     *
     * @return void
     */
    public function setServerName($serverName)
    {
        $this->serverName = $serverName;
    }

    /**
     * Returns the server name.
     *
     * @return string The server name
     */
    public function getServerName()
    {
        return $this->serverName;
    }

    /**
     * Sets the query string of the actual request.
     *
     * @param string $queryString The query string
     *
     * @return void
     */
    public function setQueryString($queryString)
    {
        $this->queryString = $queryString;
    }

    /**
     * Returns query string of the actual request.
     *
     * @return string|null The query string of the actual request
     */
    public function getQueryString()
    {
        return $this->queryString;
    }

    /**
     * Returns request uri
     *
     * @return string
     */
    public function getUri()
    {
        return $this->getHttpRequest()->getUri();
    }

    /**
     * Sets the URI.
     *
     * @param string $uri The uri
     *
     * @return void
     */
    public function setUri($uri)
    {
        $this->getHttpRequest()->setUri($uri);
    }

    /**
     * Returns request method
     *
     * @return string
     */
    public function getMethod()
    {
        return $this->getHttpRequest()->getMethod();
    }

    /**
     * Sets the method to be performed on the resource identified by the
     * Request-URI.
     *
     * While HTTP method names are typically all uppercase characters, HTTP
     * method names are case-sensitive and thus implementations SHOULD NOT
     * modify the given string.
     *
     * @param string $method Case-insensitive method
     *
     * @return void
     */
    public function setMethod($method)
    {
        $this->getHttpRequest()->setMethod($method);
    }

    /**
     * Returns the array with the server variables.
     *
     * @return array The array with the server variables
     */
    public function getServerVars()
    {
        return $this->serverVars;
    }

    /**
     * Returns the server variable with the requested name.
     *
     * @param string $name The name of the server variable to be returned
     *
     * @return mixed The requested server variable
     */
    public function getServerVar($name)
    {
        if (isset($this->serverVars[$name])) {
            return $this->serverVars[$name];
        }
    }

    /**
     * Set's the passed authentication type.
     *
     * @param string|null $authType The authentication type
     *
     * @return void
     */
    public function setAuthType($authType = null)
    {
        $this->authType = $authType;
    }

    /**
     * Return's the authentication type.
     *
     * @return string|null The authentication type
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * Set's the user principal for this request.
     *
     * @param \AppserverIo\Psr\Security\PrincipalInterface|null $userPrincipal The user principal
     *
     * @return void
     */
    public function setUserPrincipal(PrincipalInterface $userPrincipal = null)
    {
        $this->userPrincipal = $userPrincipal;
    }

    /**
     * Return's a PrincipalInterface object containing the name of the current authenticated user.
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface|null The user principal
     */
    public function getUserPrincipal()
    {
        return $this->userPrincipal;
    }

    /**
     * Return's the login of the user making this request, if the user has been authenticated, or null if the
     * user has not been authenticated. Whether the user name is sent with each subsequent request depends on
     * the browser and type of authentication. Same as the value of the CGI variable REMOTE_USER.
     *
     * @return \AppserverIo\Lang\String|null A string specifying the login of the user making this request, or null if the user login is not known
     */
    public function getRemoteUser()
    {
        if ($userPrincipal = $this->getUserPrincipal()) {
            return $userPrincipal->getName();
        }
    }

    /**
     * Return_s a boolean indicating whether the authenticated user is included in the specified logical "role".
     *
     * @param \AppserverIo\Lang\String $role The role we want to test for
     *
     * @return boolean TRUE if the user has the passed role, else FALSE
     */
    public function isUserInRole(String $role)
    {

        // query whether or not we've an user principal
        if ($principal = $this->getUserPrincipal()) {
            return $principal->getRoles()->contains($role);
        }

        // user is not in passed role
        return false;
    }

    /**
     * Use the container login mechanism configured for the servlet context to authenticate the user making this
     * request. This method may modify and commit the passed servlet response.
     *
     * @param AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The servlet response
     *
     * @return boolean TRUE when non-null values were or have been established as the values returned by getRemoteUser, else FALSE
     */
    public function authenticate(HttpServletResponseInterface $servletResponse)
    {

        // load the authentication manager and try to authenticate this request
        /** @var \AppserverIo\Psr\Auth\AuthenticationManagerInterface $authenticationManager */
        if ($authenticationManager = $this->getAuthenticationManager()) {
            return $authenticationManager->handleRequest($this, $servletResponse);
        }

        // also return TRUE if we can't find an authentication manager
        return true;
    }

    /**
     * Validate the provided username and password in the password validation realm used by the web
     * container login mechanism configured for the ServletContext.
     *
     * @param \AppserverIo\Lang\String $username The username to login
     * @param \AppserverIo\Lang\String $password The password used to authenticate the user
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if no default authenticator can be found
     */
    public function login(String $username, String $password)
    {

        // query whether or not we're already authenticated or not
        if ($this->getAuthType() != null || $this->getRemoteUser() != null || $this->getUserPrincipal() != null) {
            throw new ServletException('Already authenticated');
        }

        // load the authentication manager and try to authenticate this request
        /** @var \AppserverIo\Psr\Auth\AuthenticationManagerInterface $authenticationManager */
        if ($authenticationManager = $this->getAuthenticationManager()) {
            // try to load the authentication managers default authenticator
            if (($authenticator = $authenticationManager->getAuthenticator()) == null) {
                throw new ServletException('Can\'t find default authenticator');
            }

            // authenticate the passed username/password combination
            $authenticator->login($username, $password, $this);
        }
    }

    /**
     * Establish null as the value returned when getUserPrincipal, getRemoteUser, and getAuthType is
     * called on the request.
     *
     * @return void
     * @throws \AppserverIo\Psr\Servlet\ServletException Is thrown if no default authenticator can be found
     */
    public function logout()
    {

        // load the authentication manager and try to authenticate this request
        /** @var \AppserverIo\Psr\Auth\AuthenticationManagerInterface $authenticationManager */
        if ($authenticationManager = $this->getAuthenticationManager()) {
            // try to load the authentication managers default authenticator
            if (($authenticator = $authenticationManager->getAuthenticator()) == null) {
                throw new ServletException('Can\'t find default authenticator');
            }

            // logout the actual user
            $authenticator->logout($this);
        }
    }
}
