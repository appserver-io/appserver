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
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Application\VirtualHost;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Psr\Servlet\ServletRequest;
use AppserverIo\Psr\Servlet\ServletResponse;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\WebServer\Interfaces\HttpModuleInterface;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Exceptions\ModuleException;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Http\Part;
use AppserverIo\Appserver\ServletEngine\BadRequestException;
use AppserverIo\Appserver\ServletEngine\Authentication\AuthenticationValve;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

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
class DynamicServletEngine extends AbstractServletEngine
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
     * Initialize the module
     */
    public function __construct()
    {

        // don't forget about the parent constructor
        parent::__construct();

        /**
         * Storage with the registered request handlers.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->requestHandlers = new GenericStackable();

        /**
         * Storage with the thread ID's of the request handlers actually handling a request.
         *
         * @var \AppserverIo\Storage\GenericStackable
         */
        $this->workingRequestHandlers = new GenericStackable();
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
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The servers context instance
     *
     * @return void
     * @throws \AppserverIo\Server\Exceptions\ModuleException
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
     * Process servlet request.
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface          $request        A request object
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface         $response       A response object
     * @param \AppserverIo\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                    $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function process(
        RequestInterface $request,
        ResponseInterface $response,
        RequestContextInterface $requestContext,
        $hook
    ) {

        try {

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
     * @throws \AppserverIo\Appserver\ServletEngine\BadRequestException
     * @throws \AppserverIo\Appserver\ServletEngine\RequestHandlerTimeoutException
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

        // after bootstrapping we can use the parent functionality for further preparation
        parent::prepareServletRequest($servletRequest);
    }
}
