<?php

/**
 * \AppserverIo\Appserver\ServletEngine\RequestHandler
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
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Utils\RequestHandlerKeys;
use AppserverIo\Appserver\ServletEngine\Security\AuthenticationManagerInterface;

/**
 * This is a request handler that is necessary to process each request of an
 * application in a separate context.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface          $application     The application instance
 * @property \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The actual request instance
 * @property \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The actual response instance
 * @property \AppserverIo\Storage\GenericStackable                      $valves          The valves to process
 */
class RequestHandler extends \Thread
{

    /**
     * The prepared application context.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    public static $applicationContext;

    /**
     * The prepared request context.
     *
     * @var \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface
     */
    public static $requestContext;

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
     * Inject the actual servlet response.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The actual response instance
     *
     * @return void
     */
    public function injectResponse(HttpServletResponseInterface $servletResponse)
    {
        $this->servletResponse = $servletResponse;
    }

    /**
     * The main method that handles the thread in a separate context.
     *
     * @return void
     */
    public function run()
    {

        try {
            // register the default autoloader
            require SERVER_AUTOLOADER;

            // register shutdown handler
            register_shutdown_function(array(&$this, "shutdown"));

            // synchronize the application instance and register the class loaders
            $application = $this->application;
            $application->registerClassLoaders();

            // synchronize the valves, servlet request/response
            $valves = $this->valves;
            $servletRequest = $this->servletRequest;
            $servletResponse = $this->servletResponse;

            // load the session manager
            $sessionManager = $application->search(SessionManagerInterface::IDENTIFIER);
            $authenticationManager = $application->search(AuthenticationManagerInterface::IDENTIFIER);

            // inject the sapplication and servlet response
            $servletRequest->injectContext($application);
            $servletRequest->injectResponse($servletResponse);
            $servletRequest->injectSessionManager($sessionManager);
            $servletRequest->injectAuthenticationManager($authenticationManager);

            // prepare the request instance
            $servletRequest->prepare();

            // initialize static request and application context
            RequestHandler::$requestContext = $servletRequest;
            RequestHandler::$applicationContext = $application;

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
            // log the exception message
            $application->getInitialContext()->getSystemLogger()->error($e->getMessage());
            // set the status code and add the servlet exception to the servlet request attributes
            $servletResponse->setStatusCode($e->getCode());
            $servletRequest->setAttribute(RequestHandlerKeys::ERROR_MESSAGE, $e->getMessage());
        }

        // re-attach request and response instances
        $this->servletRequest = $servletRequest;
        $this->servletResponse = $servletResponse;
    }

    /**
     * Copies the values from the request handler back to the passed HTTP response instance.
     *
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface $httpResponse A HTTP response object
     *
     * @return void
     */
    public function copyToHttpResponse(ResponseInterface $httpResponse)
    {

        // create a local copy of the response
        $servletResponse = $this->servletResponse;

        // copy response values to the HTTP response
        $httpResponse->setStatusCode($servletResponse->getStatusCode());
        $httpResponse->setStatusReasonPhrase($servletResponse->getStatusReasonPhrase());
        $httpResponse->setVersion($servletResponse->getVersion());
        $httpResponse->setState($servletResponse->getState());

        // copy the body content to the HTTP response
        $httpResponse->appendBodyStream($servletResponse->getBodyStream());

        // copy headers to the HTTP response
        foreach ($servletResponse->getHeaders() as $headerName => $headerValue) {
            $httpResponse->addHeader($headerName, $headerValue);
        }

        // copy cookies to the HTTP response
        $httpResponse->setCookies($servletResponse->getCookies());
    }

    /**
     * Does shutdown logic for request handler if something went wrong and
     * produces a fatal error for example.
     *
     * @return void
     */
    public function shutdown()
    {

        // create a local copy of the request/response
        $servletRequest = $this->servletRequest;
        $servletResponse = $this->servletResponse;

        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize type + message
            $type = 0;
            $line = 0;
            $message = '';
            $file = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                // set the apropriate status code
                $servletResponse->setStatusCode(500);
                // add the fatal error to the servlet request attributes
                $servletRequest->setAttribute(
                    RequestHandlerKeys::ERROR_MESSAGE,
                    sprintf("PHP Fatal error: %s in %s on line %d", $message, $file, $line)
                );
            }
        }

        // query whether or not we've a client or an server error
        if ($servletResponse->getStatusCode() > 399) {
            try {
                // create a local copy of the application
                $application = $this->application;

                // inject the application and servlet response
                $servletRequest->injectResponse($servletResponse);
                $servletRequest->injectContext($application);

                // load the servlet context instance
                $servletManager = $application->search(ServletContextInterface::IDENTIFIER);

                // initialize the request URI for the error page to be rendered
                $requestUri = '';

                // iterate over the configured error pages to find a matching one
                foreach ($servletManager->getErrorPages() as $errorCodePattern => $errorPage) {
                    // query whether or not we found an error page configured for the actual status code
                    if (fnmatch($errorCodePattern, $servletResponse->getStatusCode())) {
                        $requestUri = $errorPage;
                        break;
                    }
                }

                // initialize the request URI
                $servletRequest->setRequestUri($requestUri);
                // prepare the request with the new data
                $servletRequest->prepare();
                // load the servlet path and session-ID
                $servletPath = $servletRequest->getServletPath();
                $sessionId = $servletRequest->getProposedSessionId();
                // load and process the servlet
                $servlet = $servletManager->lookup($servletPath, $sessionId);
                $servlet->service($servletRequest, $servletResponse);

            } catch (\Exception $e) {
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());
            }
        }

        // copy request/respons back to the thread context
        $this->servletRequest = $servletRequest;
        $this->servletResponse = $servletResponse;
    }

    /**
     * Returns the actual servlet request instance that has been prepared to
     * handle the actual request and represents the context of this request.
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface The prepared request context
     */
    public static function getRequestContext()
    {
        return RequestHandler::$requestContext;
    }

    /**
     * Returns the actual application instance thatrepresents the application
     * context of this request.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The actual application context
     */
    public static function getApplicationContext()
    {
        return RequestHandler::$applicationContext;
    }
}
