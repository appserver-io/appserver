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
use AppserverIo\Psr\Auth\AuthenticationManagerInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\Utils\Error;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\Utils\ErrorUtil;

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
 * @property array                                                      $errors          The array with the request handler's error stack
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
            set_error_handler(array(&$this, 'errorHandler'));
            register_shutdown_function(array(&$this, 'shutdown'));

            // initialize the array for the errors
            $this->errors = array();

            // synchronize the application instance and register the class loaders
            $application = $this->application;
            $application->registerClassLoaders();

            // synchronize the valves, servlet request/response
            $valves = $this->valves;
            $servletRequest = $this->servletRequest;
            $servletResponse = $this->servletResponse;

            // load the session and the authentication manager
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
            $this->addError(ErrorUtil::singleton()->fromException($e));
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
     * PHP error handler implemenation that replaces the defaulf PHP error handling.
     *
     * As this method will NOT handle Fatal Errors with code E_ERROR or E_USER, so
     * these have to be processed by the shutdown handler itself.
     *
     * @param integer $errno   The intern PHP error number
     * @param string  $errstr  The error message itself
     * @param string  $errfile The file where the error occurs
     * @param integer $errline The line where the error occurs
     *
     * @return boolean Always return TRUE, because we want to disable default PHP error handling
     * @link http://docs.php.net/manual/en/function.set-error-handler.php
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {

        // query whether or not we've to handle the passed error
        if ($errno < error_reporting()) {
            return true;
        }

        // add the passed error information to the array with the errors
        $this->addError(new Error($errno, $errstr, $errfile, $errline));
        return true;
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

        // check if we had a fatal error that caused the shutdown
        if ($lastError = error_get_last()) {
            // add the fatal error
            $this->addError(ErrorUtil::singleton()->fromArray($lastError));
            // in case of fatal errors, we need to override request with perpared
            // instances because error handling needs application access
            $servletRequest = RequestHandler::$requestContext;
        }

        // handle the errors if necessary
        ErrorUtil::singleton()->handleErrors($this, $servletRequest, $servletResponse);

        // copy request/response back to the thread context
        $this->servletRequest = $servletRequest;
        $this->servletResponse = $servletResponse;
    }

    /**
     * Append the passed error to the request handler's stack.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Utils\Error $error The error to append
     *
     * @return void
     */
    public function addError(Error $error)
    {
        // create a local copy of the error stack
        $errors = $this->errors;

        // append the error to the stack
        $errors[] = $error;

        // copy the error stack back to the thread context
        $this->errors = $errors;
    }

    /**
     * Return's the array with the request handler's error stack.
     *
     * @return array The stack with the request handler's errors
     */
    public function getErrors()
    {
        return $this->errors;
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
