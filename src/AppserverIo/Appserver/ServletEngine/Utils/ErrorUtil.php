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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Psr\Servlet\ServletException;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\Utils\RequestHandlerKeys;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\Core\Utilities\LoggerUtils;

/**
 * Utility class that providing functionality to handle PHP errors.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ErrorUtil extends \AppserverIo\Appserver\Core\Utilities\ErrorUtil
{

    /**
     * Create's a new error instance from the passed exception.
     *
     * @param \Exception $e The exception to create the error instance from
     *
     * @return \AppserverIo\Appserver\Core\Utilities\ErrorInterface The error instance
     */
    public function fromException(\Exception $e)
    {
        return new Error(E_EXCEPTION, $e->__toString(), $e->getFile(), $e->getLine(), $e->getCode() ? $e->getCode() : 500);
    }

    /**
     * Create's a new error instance with the values from the passed array.
     *
     * @param array $error The array containing the error information
     *
     * @return \AppserverIo\Appserver\Core\Utilities\ErrorInterface The error instance
     */
    public function fromArray(array $error)
    {

        // extract the array with the error information
        list ($type, $message, $file, $line) = array_values($error);

        // initialize and return the error instance
        return new Error($type, $message, $file, $line, ErrorUtil::singleton()->isFatal($type) ? 500 : 0);
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
                LoggerUtils::log(ErrorUtil::mapLogLevel($error), $message);
            }

            // we prepend the errors to the body stream if display_errors is on
            if (Boolean::valueOf(new String(ini_get('display_errors')))->booleanValue()) {
                $bodyContent = $servletResponse->getBodyContent();
                $servletResponse->resetBodyStream();
                $servletResponse->appendBodyStream(sprintf('%s<br/>%s', $message, $bodyContent));
            }

            // query whether or not, the error has an status code
            if ($statusCode = $error->getStatusCode()) {
                $servletResponse->setStatusCode($statusCode);
            }
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
                // we add the filtered errors (status code > 399) to the servlet request
                $servletRequest->setAttribute(
                    RequestHandlerKeys::ERROR_MESSAGES,
                    array_filter($errors, function ($message) {
                        return $message->getStatusCode() > 399;
                    })
                );
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
