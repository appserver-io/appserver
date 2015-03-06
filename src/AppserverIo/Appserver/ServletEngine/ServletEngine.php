<?php

/**
 * AppserverIo\Appserver\ServletEngine\ServletEngine
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Http\HttpProtocol;
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Exceptions\ModuleException;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Http\Part;

/**
 * A servlet engine implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletEngine extends AbstractServletEngine
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
     * Returns the module name.
     *
     * @return string The module name
     */
    public function getModuleName()
    {
        return ServletEngine::MODULE_NAME;
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
            $this->initApplications();

        } catch (\Exception $e) {
            throw new ModuleException($e);
        }
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

        // if false hook is coming do nothing
        if (ModuleHooks::REQUEST_POST !== $hook) {
            return;
        }

        // check if we are the handler that has to process this request
        if ($requestContext->getServerVar(ServerVars::SERVER_HANDLER) !== $this->getModuleName()) {
            return;
        }

        // create a copy of the valve instances
        $valves = $this->valves;

        // initialize servlet session, request + response
        $servletResponse = new Response();
        $servletRequest = new Request();

        // reset the servlet request
        $servletRequest->init();
        $servletRequest->injectHttpRequest($request);
        $servletRequest->injectServerVars($requestContext->getServerVars());

        // initialize the parts
        foreach ($request->getParts() as $part) {
            $servletRequest->addPart(Part::fromHttpRequest($part));
        }

        // set the body content if we can find one
        if ($request->getHeader(HttpProtocol::HEADER_CONTENT_LENGTH) > 0) {
            $servletRequest->setBodyStream($request->getBodyContent());
        }

        // prepare the servlet request
        $this->prepareServletRequest($servletRequest);

        // load the application associated with this request
        $application = $this->findRequestedApplication($requestContext);

        // prepare and set the applications context path
        $servletRequest->setContextPath($contextPath = '/' . $application->getName());
        $servletRequest->setServletPath(str_replace($contextPath, '', $servletRequest->getServletPath()));

        // prepare the base modifier which allows our apps to provide a base URL
        $webappsDir = $this->getServerContext()->getServerConfig()->getDocumentRoot();
        $relativeRequestPath = strstr($servletRequest->getServerVar(ServerVars::DOCUMENT_ROOT), $webappsDir);
        $proposedBaseModifier = str_replace($webappsDir, '', $relativeRequestPath);

        //  prepare the base modifier
        if (strpos($proposedBaseModifier, $contextPath) === 0) {
            $servletRequest->setBaseModifier('');
        } else {
            $servletRequest->setBaseModifier($contextPath);
        }

        // reset the servlet response
        $servletResponse->init();

        // inject the servlet response with the Http response values
        $servletRequest->injectResponse($servletResponse);

        // initialize the request handler instance
        $requestHandler = new RequestHandler();
        $requestHandler->injectValves($valves);
        $requestHandler->injectApplication($application);
        $requestHandler->injectRequest($servletRequest);
        $requestHandler->injectResponse($servletResponse);
        $requestHandler->start();
        $requestHandler->join();

        // re-load the servlet response from the request handler
        $servletResponse = $requestHandler->getServletResponse();

        // query whether an exception has been thrown, if yes, re-throw it
        if ($servletResponse->hasException()) {
            throw $servletResponse->getException();
        }

        // copy the values from the servlet response back to the HTTP response
        $response->setStatusCode($servletResponse->getStatusCode());
        $response->setStatusReasonPhrase($servletResponse->getStatusReasonPhrase());
        $response->setVersion($servletResponse->getVersion());
        $response->setState($servletResponse->getState());

        // append the content to the body stream
        $response->appendBodyStream($servletResponse->getBodyStream());

        // transform the servlet headers back into HTTP headers
        foreach ($servletResponse->getHeaders() as $name => $header) {
            $response->addHeader($name, $header);
        }

        // copy the servlet response cookies back to the HTTP response
        foreach ($servletResponse->getCookies() as $cookieName => $cookieValue) {
            // load the cookie and check if we've an array or a single cookie instance
            if (is_array($cookie = $servletResponse->getCookie($cookieName))) {
                foreach ($cookie as $c) {
                    // add all the cookies
                    $response->addCookie($c);
                }

            } else {
                // add the cookie instance directly
                $response->addCookie($cookie);
            }
        }

        // set response state to be dispatched after this without calling other modules process
        $response->setState(HttpResponseStates::DISPATCH);
    }

    /**
     * Tries to find a request handler that matches the actual request and injects it into the request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request we need a request handler to handle for
     *
     * @return string The application name of the application to handle the request
     * @deprecated This method is deprecated since 0.8.0
     */
    protected function requestHandlerFromPool(HttpServletRequestInterface $servletRequest)
    {
        // nothing to do here
    }

    /**
     * After a request has been processed by the injected request handler we remove
     * the thread ID of the request handler from the array with the working handlers.
     *
     * @param \AppserverIo\Appserver\ServletEngine\RequestHandler $requestHandler The request handler instance we want to re-attach to the pool
     *
     * @return void
     * @deprecated This method is deprecated since 0.8.0
     */
    protected function requestHandlerToPool($requestHandler)
    {
        // nothing to do here
    }
}
