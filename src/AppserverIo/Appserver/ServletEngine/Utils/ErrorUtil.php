<?php

/**
 * AppserverIo\Appserver\ServletEngine\Utils\ErrorUtil
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

namespace AppserverIo\Appserver\ServletEngine\Utils;

use Psr\Log\LogLevel;
use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\Utils\RequestHandlerKeys;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\RequestHandler;

// define a custom user exception constant
define('E_EXCEPTION', 0);

/**
 * Utility class that providing functionality to handle PHP errors.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ErrorUtil
{

    /**
     * The singleton instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Utils\ErrorUtil
     */
    protected static $instance;

    /**
     * Create's and return's the singleton instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Utils\ErrorUtil The singleton instance
     */
    public static function singleton()
    {

        // query whether or not the instance has already been created
        if (ErrorUtil::$instance == null) {
            ErrorUtil::$instance = new ErrorUtil();
        }

        // return the singleton instance
        return ErrorUtil::$instance;
    }

    /**
     * Create's a new error instance with the values from the passed array.
     *
     * @param array $error The array containing the error information
     *
     * @return \AppserverIo\Appserver\ServletEngine\Utils\ErrorInterface The error instance
     */
    public function fromArray(array $error)
    {

        // initialize the variables
        $type  = 0;
        $message = '';
        $file = '';
        $line = 0;

        // extract the array with the error information
        extract($error);

        // initialize and return the error instance
        return new Error($type, $message, $file, $line);
    }

    /**
     * Create's a new error instance from the passed exception.
     *
     * @param \Exception $e The exception to create the error instance from
     *
     * @return \AppserverIo\Appserver\ServletEngine\Utils\ErrorInterface The error instance
     */
    public function fromException(\Exception $e)
    {
        return new Error(E_EXCEPTION, $e->__toString(), $e->getFile(), $e->getLine());
    }

    /**
     * Prepare's the error message for logging/rendering purposes.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Utils\ErrorInterface $error The error instance to create the message from
     *
     * @return string The error message
     */
    public function prepareMessage(ErrorInterface $error)
    {
        return sprintf('PHP %s: %s in %s on line %d', $this->mapErrorCode($error), $error->getMessage(), $error->getFile(), $error->getLine());
    }

    /**
     * Return's the log level for the passed error instance.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Utils\ErrorInterface $error The error instance to map the log level for
     *
     * @return string
     */
    public function mapLogLevel(ErrorInterface $error)
    {

        // initialize the log level, default is 'error'
        $logLevel = LogLevel::ERROR;

        // query the error type
        switch ($error->getType()) {
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $logLevel = LogLevel::WARNING;
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $logLevel = LogLevel::NOTICE;
                break;

            default:
                break;
        }

        // return the log level
        return $logLevel;
    }

    /**
     * Return's the a human readable error representation for the passed error instance.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Utils\ErrorInterface $error The error instance
     *
     * @return string The human readable error representation
     */
    public function mapErrorCode(ErrorInterface $error)
    {

        // initialize the error representation
        $wrapped = 'Unknown';

        // query the error type
        switch ($error->getType()) {
            case E_EXCEPTION:
                $wrapped = 'Exception';
                break;

            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $wrapped = 'Fatal Error';
                break;

            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $wrapped = 'Warning';
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                $wrapped = 'Notice';
                break;

            case E_STRICT:
                $wrapped = 'Strict';
                break;

            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $wrapped = 'Deprecated';
                break;

            default:
                break;
        }

        // return the human readable error representation
        return $wrapped;
    }

    /**
     * This method finally handles all PHP and user errors as well as the exceptions that
     * have been thrown through the servlet processing.
     *
     * @param \AppserverIo\Appserver\ServletEngine\RequestHandler        $requestHandler  The request handler instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The actual request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The actual request instance
     *
     * @return void
     */
    public function handleErrors(
        RequestHandler $requestHandler,
        HttpServletRequestInterface $servletRequest,
        HttpServletResponseInterface $servletResponse
    ) {

        // return immediately if we don't have any errors
        if (sizeof($errors = $requestHandler->getErrors()) === 0) {
            return;
        }

        // iterate over the errors to process each of them
        foreach ($errors as $error) {
            // prepare the error message
            $message = $this->prepareMessage($error);

            // query whether or not we have to log the error
            if (Boolean::valueOf(new String(ini_get('log_errors')))->booleanValue()) {
                // create a local copy of the application
                if ($application = $servletRequest->getContext()) {
                    // try to load the system logger from the application
                    if ($systemLogger = $application->getLogger(LoggerUtils::SYSTEM)) {
                        $systemLogger->log($this->mapLogLevel($error), $message);
                    }
                }
            }

            // query whether or not, the error has an status code
            if ($statusCode = $error->getStatusCode()) {
                $servletResponse->setStatusCode($statusCode);
            }
        }

        // we add the error to the servlet request
        $servletRequest->setAttribute(RequestHandlerKeys::ERROR_MESSAGES, $errors);
        // we append the the errors to the body stream if display_errors is on
        if (Boolean::valueOf(new String(ini_get('display_errors')))->booleanValue()) {
            $servletResponse->appendBodyStream(implode('<br/>', $errors));
        }

        // query whether or not we've a client or an server error
        if ($servletResponse->getStatusCode() > 399) {
            try {
                // create a local copy of the application
                $application = $servletRequest->getContext();

                // inject the application and servlet response
                $servletRequest->injectResponse($servletResponse);
                $servletRequest->injectContext($application);

                // load the servlet context instance
                $servletManager = $application->search(ServletContextInterface::IDENTIFIER);

                // initialize the request URI for the error page to be rendered
                $requestUri = null;

                // iterate over the configured error pages to find a matching one
                foreach ($servletManager->getErrorPages() as $errorCodePattern => $errorPage) {
                    // query whether or not we found an error page configured for the actual status code
                    if (fnmatch($errorCodePattern, $servletResponse->getStatusCode())) {
                        $requestUri = $errorPage;
                        break;
                    }
                }

                // query whether or not we've an found a configured error page
                if ($requestUri == null) {
                    throw new ServletException(
                        sprintf(
                            'Please configure an error page for status code %s',
                            $servletResponse->getStatusCode()
                        )
                    );
                }

                // initialize the request URI
                $servletRequest->setRequestUri($requestUri);
                // prepare the request with the new data
                $servletRequest->prepare();
                // reset the body stream to remove content, that has already been appended
                $servletResponse->resetBodyStream();
                // load the servlet path and session-ID
                $servletPath = $servletRequest->getServletPath();
                $sessionId = $servletRequest->getProposedSessionId();
                // load and process the servlet
                $servlet = $servletManager->lookup($servletPath, $sessionId);
                $servlet->service($servletRequest, $servletResponse);

            } catch (\Exception $e) {
                // finally log the exception
                $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
                // append the exception message to the body stream
                $servletResponse->appendBodyStream($e->__toString());
            }
        }
    }
}
