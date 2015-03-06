<?php

/**
 * AppserverIo\Appserver\ServletEngine\RequestHandler
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * This is a request handler that is necessary to process each request of an
 * application in a separate context.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class RequestHandler extends \Thread
{

    /**
     * Injects the valves to be processed.
     *
     * @param array $valves The valves to process
     *
     * @return void
     */
    public function injectValves(array $valves)
    {
        $this->valves = $valves;
    }

    /**
     * Injects the application of the request to be handled
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Inject the actual servlet request.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The actual request instance
     *
     * @return void
     */
    public function injectRequest(HttpServletRequestInterface $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * The main method that handles the thread in a separate context.
     *
     * @return void
     */
    public function run()
    {

        try {
            // register shutdown handler
            register_shutdown_function(array(&$this, "shutdown"));

            // synchronize the application instance
            $application = $this->application;

            // register class loaders
            $application->registerClassLoaders();

            // synchronize the valves, servlet request/response
            $valves = $this->valves;
            $servletRequest = $this->servletRequest;

            // initialize servlet session, request + response
            $servletResponse = new Response();
            $servletResponse->init();

            // inject the sapplication and servlet response
            $servletRequest->injectResponse($servletResponse);
            $servletRequest->injectContext($application);

            // prepare the request instance
            $servletRequest->prepare();

            // process the valves
            foreach ($valves as $valve) {
                $valve->invoke($servletRequest, $servletResponse);
                if ($servletRequest->isDispatched() === true) {
                    break;
                }
            }

            // profile the request if the profile logger is available
            if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
                $profileLogger->appendThreadContext('request-handler');
                $profileLogger->debug($servletRequest->getUri());
            }

        } catch (\Exception $e) {
            // bind the exception in the respsonse
            $this->exception = $e;
        }

        // copy the the response values
        $this->statusCode = $servletResponse->getStatusCode();
        $this->statusReasonPhrase = $servletResponse->getStatusReasonPhrase();
        $this->version = $servletResponse->getVersion();
        $this->state = $servletResponse->getState();

        // copy the content of the body stream
        $this->bodyStream = $servletResponse->getBodyStream();

        // copy headers and cookies
        $this->headers = $servletResponse->getHeaders();
        $this->cookies = $servletResponse->getCookies();
    }

    /**
     * Copies the values from the request handler back to the passed HTTP response instance.
     *
     * \AppserverIo\Psr\HttpMessage\ResponseInterface $response A HTTP response object
     *
     * @return void
     */
    public function copyToHttpResponse(ResponseInterface $httpResponse)
    {

        // copy response values to the HTTP response
        $httpResponse->setStatusCode($this->statusCode);
        $httpResponse->setStatusReasonPhrase($this->statusReasonPhrase);
        $httpResponse->setVersion($this->version);
        $httpResponse->setState($this->state);

        // copy the body content to the HTTP response
        $httpResponse->appendBodyStream($this->bodyStream);

        // copy headers to the HTTP response
        foreach ($this->headers as $headerName => $headerValue) {
            $httpResponse->addHeader($headerName, $headerValue);
        }

        // copy cookies to the HTTP response
        $httpResponse->setCookies($this->cookies);

        // query whether an exception has been thrown, if yes, re-throw it
        if ($this->exception instanceof \Exception) {
            throw $this->exception;
        }
    }

    /**
     * Does shutdown logic for request handler if something went wrong and produces
     * a fatal error for example.
     *
     * @return void
     */
    public function shutdown()
    {

        // check if there was a fatal error caused shutdown
        $lastError = error_get_last();
        if ($lastError['type'] === E_ERROR || $lastError['type'] === E_USER_ERROR) {
            // set the status code and append the error message to the body
            $this->statusCode = 500;
            $this->bodyStream = $lastError['message'];
        }
    }
}
