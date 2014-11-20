<?php

/**
 * AppserverIo\Appserver\ServletEngine\DynamicServletEngine
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Http\HttpCookie;
use AppserverIo\Http\HttpProtocol;
use AppserverIo\Http\HttpRequestInterface;
use AppserverIo\Http\HttpResponseInterface;
use AppserverIo\Http\HttpResponseStates;
use TechDivision\Storage\GenericStackable;
use AppserverIo\Appserver\Application\VirtualHost;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use TechDivision\Server\Dictionaries\ModuleHooks;
use TechDivision\Server\Dictionaries\ServerVars;
use TechDivision\Server\Interfaces\ModuleInterface;
use TechDivision\Server\Interfaces\RequestContextInterface;
use TechDivision\Server\Interfaces\ServerContextInterface;
use TechDivision\Server\Exceptions\ModuleException;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Http\Part;
use AppserverIo\Appserver\ServletEngine\BadRequestException;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use TechDivision\Connection\ConnectionRequestInterface;
use TechDivision\Connection\ConnectionResponseInterface;

/**
 * A servlet engine implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DynamicServletEngine extends GenericStackable implements ModuleInterface
{

    /**
     * The unique module name in the web server context.
     *
     * @var string
     */
    const MODULE_NAME = 'servlet';

    /**
     * Timeout to wait for a free request handler: 1 s
     *
     * @var integer
     */
    const REQUEST_HANDLER_WAIT_TIMEOUT = 1000000;

    /**
     * Initialize the module.
     *
     * @return void
     */
    public function __construct()
    {

        /**
         * Storage for the servlet engines valves that handles the request.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->valves = new GenericStackable();

        /**
         * Storage handlers registered in the web server.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->handlers = new GenericStackable();

        /**
         * Storage with the available applications.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->applications = new GenericStackable();

        /**
         * Storage with the available applications.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->dependencies = new GenericStackable();

        /**
         * Storage with the registered virtual hosts.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->virtualHosts = new GenericStackable();

        /**
         * Storage with the registered request handlers.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->requestHandlers = new GenericStackable();

        /**
         * Storage with the thread ID's of the request handlers actually handling a request.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->workingRequestHandlers = new GenericStackable();

        /**
         * Storage with URL => application mappings.
         *
         * @var \TechDivision\Storage\GenericStackable
         */
        $this->urlMappings = new GenericStackable();
    }

    /**
     * Returns an array of module names which should be executed first.
     *
     * @return \TechDivision\Storage\GenericStackable The module names this module depends on
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Returns the module name.
     *
     * @return string The module name
     */
    public function getModuleName()
    {
        return DynamicServletEngine::MODULE_NAME;
    }

    /**
     * Initializes the module.
     *
     * @param \TechDivision\Server\Interfaces\ServerContextInterface $serverContext The servers context instance
     *
     * @return void
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function init(ServerContextInterface $serverContext)
    {
        try {

            // set the servlet context
            $this->serverContext = $serverContext;

            // initialize the servlet engine
            $this->initValves();
            $this->initHandlers();
            $this->initVirtualHosts();
            $this->initApplications();
            $this->initUrlMappings();

            // initialize the request handler manager
            $this->initRequestHandlerManager();

        } catch (\Exception $e) {
            throw new ModuleException($e);
        }
    }

    /**
     * Initialize the valves that handles the requests.
     *
     * @return void
     */
    public function initValves()
    {
        $this->valves[] = new AuthenticationValve();
        $this->valves[] = new ServletValve();
    }

    /**
     * Initialize the web server handlers.
     *
     * @return void
     */
    public function initHandlers()
    {
        foreach ($this->getServerContext()->getServerConfig()->getHandlers() as $extension => $handler) {
            $this->handlers[$extension] = new Handler($handler['name']);
        }
    }

    /**
     * Initialize the configured virtual hosts.
     *
     * @return void
     */
    public function initVirtualHosts()
    {
        // load the document root and the web servers virtual host configuration
        $documentRoot = $this->getServerContext()->getServerConfig()->getDocumentRoot();

        // prepare the virtual host configurations
        foreach ($this->getServerContext()->getServerConfig()->getVirtualHosts() as $domain => $virtualHost) {

            // prepare the applications base directory
            $appBase = str_replace($documentRoot, '', $virtualHost['params']['documentRoot']);

            // append the virtual host to the array
            $this->virtualHosts[] = new VirtualHost($domain, $appBase);
        }
    }

    /**
     * Initialize the applications.
     *
     * @return void
     */
    public function initApplications()
    {

        // iterate over a applications vhost/alias configuration
        foreach ($this->getServerContext()->getContainer()->getApplications() as $applicationName => $application) {

            // iterate over the virtual hosts
            foreach ($this->virtualHosts as $virtualHost) {
                if ($virtualHost->match($application)) {
                    $application->addVirtualHost($virtualHost);
                }
            }

            // finally APPEND a wildcard pattern for each application to the patterns array
            $this->applications[$applicationName] = $application;
        }
    }

    /**
     * Initialize the URL mappings.
     *
     * @return void
     */
    public function initUrlMappings()
    {

        // iterate over a applications vhost/alias configuration
        foreach ($this->getApplications() as $application) {

            // initialize the application name
            $applicationName = $application->getName();

            // iterate over the virtual hosts and add a mapping for each
            foreach ($application->getVirtualHosts() as $virtualHost) {
                $this->urlMappings['/^' . $virtualHost->getName() . '\/(([a-z0-9+\$_-]\.?)+)*\/?/'] = $applicationName;
            }

            // finally APPEND a wildcard pattern for each application to the patterns array
            $this->urlMappings['/^[a-z0-9-.]*\/' . $applicationName . '\/(([a-z0-9+\$_-]\.?)+)*\/?/'] = $applicationName;
        }
    }

    /**
     * Initialize the request handler manager.
     *
     * @return void
     */
    public function initRequestHandlerManager()
    {

        // create a local copy of the valves
        $valves = $this->valves;
        $requestHandlers = $this->requestHandlers;
        $applications = $this->applications;

        // we also want to pass a system logger instance to the request handler manager
        $systemLogger = $this->serverContext->getContainer()->getInitialContext()->getSystemLogger();

        // we want to prepare an request for each application and each worker
        foreach ($this->getApplications() as $applicationName => $application) {
            $this->requestHandlers[$applicationName] = new GenericStackable();
            for ($i = 0; $i < RequestHandlerManager::POOL_SIZE; $i++) {
                $requestHandler = new RequestHandler($application, $valves);
                $this->requestHandlers[$applicationName][$requestHandler->getThreadId()] = $requestHandler;
            }
        }


        // initialize the request handler manager instance
        $this->requestHandlerManager = new RequestHandlerManager($systemLogger, $requestHandlers, $applications, $valves);
    }

    /**
     * Prepares the module for upcoming request in specific context
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
    }

    /**
     * Process servlet request.
     *
     * @param \TechDivision\Connection\ConnectionRequestInterface     $request        A request object
     * @param \TechDivision\Connection\ConnectionResponseInterface    $response       A response object
     * @param \TechDivision\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                     $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function process(ConnectionRequestInterface $request, ConnectionResponseInterface $response, RequestContextInterface $requestContext, $hook)
    {

        try {

            // In php an interface is, by definition, a fixed contract. It is immutable.
            // So we have to declair the right ones afterwards...
            /** @var $request \AppserverIo\Http\HttpRequestInterface */
            /** @var $request \AppserverIo\Http\HttpResponseInterface */

            // if false hook is comming do nothing
            if (ModuleHooks::REQUEST_POST !== $hook) {
                return;
            }

            // check if we are the handler that has to process this request
            if ($requestContext->getServerVar(ServerVars::SERVER_HANDLER) !== $this->getModuleName()) {
                return;
            }

            // notify the request handler to make sure we've at least one free request handler
            $this->requestHandlerManager->notify();

            // intialize servlet session, request + response
            $servletRequest = new Request();
            $servletRequest->injectHttpRequest($request);
            $servletRequest->injectServerVars($requestContext->getServerVars());

            // initialize the parts
            foreach ($request->getParts() as $name => $part) {
                $servletRequest->addPart(Part::fromHttpRequest($part));
            }

            // set the body content if we can find one
            if ($request->getHeader(HttpProtocol::HEADER_CONTENT_LENGTH) > 0) {
                $servletRequest->setBodyStream($request->getBodyContent());
            }

            // prepare the servlet request
            $this->prepareServletRequest($servletRequest);

            // initialize the servlet response with the Http response values
            $servletResponse = new Response();
            $servletRequest->injectResponse($servletResponse);

            // load a NOT working request handler from the pool
            $requestHandler = $this->requestHandlerFromPool($servletRequest);

            // notify the application to handle the request
            $requestHandler->synchronized(function ($self, $request, $response) {

                // set the servlet/response intances
                $self->servletRequest = $request;
                $self->servletResponse = $response;

                // set the flag to start request processing
                $self->handleRequest = true;

                // notify the request handler
                $self->notify();

            }, $requestHandler, $servletRequest, $servletResponse);

            // wait until the response has been dispatched
            while (true) {

                // wait until request has been finished and response is dispatched
                if (($requestHandler->isWaiting() || $requestHandler->shouldRestart()) && $servletResponse->hasState(HttpResponseStates::DISPATCH)) {
                    break;
                }

                // try to reduce system load
                usleep(1000);
            }

            // re-attach the request handler to the pool
            $this->requestHandlerToPool($requestHandler);

            // copy the values from the servlet response back to the HTTP response
            $response->setStatusCode($servletResponse->getStatusCode());
            $response->setStatusReasonPhrase($servletResponse->getStatusReasonPhrase());
            $response->setVersion($servletResponse->getVersion());
            $response->setState($servletResponse->getState());

            // append the content to the body stream
            $response->appendBodyStream($servletResponse->getBodyStream());

            // transform the servlet headers back into HTTP headers
            $headers = array();
            foreach ($servletResponse->getHeaders() as $name => $header) {
                $headers[$name] = $header;
            }

            // set the headers as array (because we don't know if we have to use the append flag)
            $response->setHeaders($headers);

            // copy the servlet response cookies back to the HTTP response
            foreach ($servletResponse->getCookies() as $cookie) {
                $response->addCookie($cookie);
            }

            // set response state to be dispatched after this without calling other modules process
            $response->setState(HttpResponseStates::DISPATCH);

        } catch (ModuleException $me) {
            throw $me;
        } catch (\Exception $e) {
            throw new ModuleException($e, 500);
        }
    }

    /**
     * Tries to find a request handler that matches the actual request and injects it into the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest $servletRequest The servlet request we need a request handler to handle for
     *
     * @return \AppserverIo\Appserver\ServletEngine\RequestHandler The request handler
     */
    protected function requestHandlerFromPool(HttpServletRequest $servletRequest)
    {

        // explode host and port from the host header
        list ($host, ) = explode(':', $servletRequest->getHeader(HttpProtocol::HEADER_HOST));

        // prepare the request URL we want to match
        $url =  $host . $servletRequest->getUri();

        // iterate over all request handlers for the request we has to handle
        foreach ($this->urlMappings as $pattern => $applicationName) {

            // try to match a registered application with the passed request
            if (preg_match($pattern, $url) === 1) {

                // load the applications request handlers
                $requestHandlers = $this->requestHandlers[$applicationName];

                // reset the wait time
                $waited = 0;

                // we search for a request handler as long $handlerFound is empty
                while ($waited < DynamicServletEngine::REQUEST_HANDLER_WAIT_TIMEOUT) {

                    // search a NOT working request handler
                    foreach ($requestHandlers as $requestHandler) {

                        // if we've found a NOT working request handler, we stop
                        if (!isset($this->workingRequestHandlers[$threadId = $requestHandler->getThreadId()]) && !$requestHandler->shouldRestart()) {

                            // mark the request handler working and initialize the found one
                            $this->workingRequestHandlers[$threadId] = true;

                            // return the request handler instance
                            return $requestHandler;
                        }
                    }

                    // reduce CPU load
                    usleep(100); // === 0.1 ms

                    // raise wait time
                    $waited += 100;
                }

                // throw an exception if we can't handle the request within the defined timeout
                throw new RequestHandlerTimeoutException(sprintf('No request handler available to handle request for URI %s', $servletRequest->getUri()));
            }
        }

        // if not throw a bad request exception
        throw new BadRequestException(sprintf('Can\'t find application for URI %s', $servletRequest->getUri()));
    }

    /**
     * After a request has been processed by the injected request handler we remove
     * the thread ID of the request handler from the array with the working handlers.
     *
     * @param \AppserverIo\Appserver\ServletEngine\RequestHandler $requestHandler The request handler instance we want to re-attach to the pool
     *
     * @return void
     */
    protected function requestHandlerToPool($requestHandler)
    {
        unset($this->workingRequestHandlers[$requestHandler->getThreadId()]);
    }

    /**
     * Tries to find an application that matches the passed request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest $servletRequest The request instance to locate the application for
     *
     * @return array The application info that matches the request
     * @throws \AppserverIo\Appserver\ServletEngine\BadRequestException Is thrown if no application matches the request
     */
    protected function prepareServletRequest(HttpServletRequest $servletRequest)
    {

        // transform the cookie headers into real servlet cookies
        if ($servletRequest->hasHeader(HttpProtocol::HEADER_COOKIE)) {

            // explode the cookie headers
            $cookieHeaders = explode('; ', $servletRequest->getHeader(HttpProtocol::HEADER_COOKIE));

            // create real cookie for each cookie key/value pair
            foreach ($cookieHeaders as $cookieHeader) {
                $servletRequest->addCookie(HttpCookie::createFromRawSetCookieHeader($cookieHeader));
            }
        }

        // load the request URI and query string
        $uri = $servletRequest->getUri();
        $queryString = $servletRequest->getQueryString();

        // get uri without querystring
        $uriWithoutQueryString = str_replace('?' . $queryString, '', $uri);

        // initialize the path information and the directory to start with
        list ($dirname, $basename, $extension) = array_values(pathinfo($uriWithoutQueryString));

        // make the registered handlers local
        $handlers = $this->getHandlers();

        do { // descent the directory structure down to find the (almost virtual) servlet file

            // bingo we found a (again: almost virtual) servlet file
            if (array_key_exists(".$extension", $handlers) && $handlers[".$extension"]->getName() === $this->getModuleName()) {

                // prepare the servlet path
                if ($dirname === '/') {
                    $servletPath = '/' . $basename;
                } else {
                    $servletPath = $dirname . '/' . $basename;
                }

                // we set the basename, because this is the servlet path
                $servletRequest->setServletPath($servletPath);

                // we set the path info, what is the request URI with stripped dir- and basename
                $servletRequest->setPathInfo(str_replace($servletPath, '', $uriWithoutQueryString));

                // we've found what we were looking for, so break here
                break;
            }

            // descendent down the directory tree
            list ($dirname, $basename, $extension) = array_values(pathinfo($dirname));

        } while ($dirname !== false); // stop until we reached the root of the URI
    }

    /**
     * Returns the server context instance.
     *
     * @return \TechDivision\Server\ServerContext The actual server context instance
     */
    public function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Returns the initialized applications.
     *
     * @return \TechDivision\Storage\GenericStackable The initialized application instances
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Returns the initialized valves.
     *
     * @return \TechDivision\Storage\GenericStackable The initialized valves
     */
    public function getValves()
    {
        return $this->valves;
    }

    /**
     * Returns the initialized web server handlers.
     *
     * @return \TechDivision\Storage\GenericStackable The initialized web server handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
