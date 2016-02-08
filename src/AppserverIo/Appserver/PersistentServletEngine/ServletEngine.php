<?php

/**
 * \AppserverIo\Appserver\PersistentServletEngine\ServletEngine
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistentServletEngine;

use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Exceptions\ModuleException;
use AppserverIo\Appserver\ServletEngine\Http\Request;
use AppserverIo\Appserver\ServletEngine\Http\Response;
use AppserverIo\Appserver\ServletEngine\AbstractServletEngine;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A servlet engine implementation.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\GenericStackable                 $requestHandlers Collection of available request handlers
 * @property \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext   The servers context instance
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
            $this->initRequestHandlers();

        } catch (\Exception $e) {
            throw new ModuleException($e);
        }
    }

    /**
     * Initialize the pool of persistent request handlers per application.
     *
     * @return void
     */
    public function initRequestHandlers()
    {
        $this->requestHandlers = array();
    }

    /**
     * Return's the application's request handler pool.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to return the request handler pool for
     *
     * @return array The array with the request handler pool
     */
    public function loadRequestHandlersForApplication(ApplicationInterface $application)
    {

        // load the application name
        $applicationName = $application->getName();

        // query whether or not the application's request handlers has already been initialized
        if (isset($this->requestHandlers[$applicationName]) === false) {
            // initialize 10 request handlers per for each application
            for ($i = 0; $i < 10; $i++) {
                // initialize the request handler
                $this->requestHandlers[$applicationName][$i] = new RequestHandler();
                $this->requestHandlers[$applicationName][$i]->injectValves($this->getValves());
                $this->requestHandlers[$applicationName][$i]->injectApplication($application);
                $this->requestHandlers[$applicationName][$i]->start();
            }

        } else {
            // re-initialize the finished request handlers
            for ($i = 0; $i < 10; $i++) {
                // query whether or not, the request handler has been finished or not
                if ($this->requestHandlers[$applicationName][$i]->finished === true) {
                    // if yes, join and unset it
                    $this->requestHandlers[$applicationName][$i]->join();
                    unset($this->requestHandlers[$applicationName][$i]);

                    // initialize a new request handler
                    $this->requestHandlers[$applicationName][$i] = new RequestHandler();
                    $this->requestHandlers[$applicationName][$i]->injectValves($this->getValves());
                    $this->requestHandlers[$applicationName][$i]->injectApplication($application);
                    $this->requestHandlers[$applicationName][$i]->start();
                }
            }
        }

        // return the application's request handler pool
        return $this->requestHandlers[$applicationName];
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

        // load the application associated with this request
        $application = $this->findRequestedApplication($requestContext);

        // check if the application has already been connected
        if ($application->isConnected() === false) {
            throw new \Exception(sprintf('Application %s has not connected yet', $application->getName()), 503);
        }

        // create a copy of the valve instances
        $handlers = $this->handlers;

        // create a new request instance from the HTTP request
        $servletRequest = new Request();
        $servletRequest->injectHandlers($handlers);
        $servletRequest->injectHttpRequest($request);
        $servletRequest->injectServerVars($requestContext->getServerVars());
        $servletRequest->init();

        // initialize servlet response
        $servletResponse = new Response();
        $servletResponse->init();

        // initialize the request handler instance
        $dispatched = false;

        // load the application's request handlers
        $requestHandlers = $this->loadRequestHandlersForApplication($application);

        // initialize a counter to avoid an endless loop
        $counter = 0;

        // try to dispatch the request with one of the handlers
        while (sizeof($requestHandlers) > 0 && $dispatched === false) {
            // try to load a
            if ($requestHandlers[$i = rand(0, sizeof($requestHandlers) - 1)]->isWaiting()) {
                $requestHandlers[$i]->handleRequest($servletRequest, $servletResponse);
                $requestHandlers[$i]->copyToHttpResponse($response);

                // mark the request dispatched
                $dispatched = true;
                break;
            }

            // stop processing after 100 iterations
            if ($counter > 100) {
                throw new \Exception('Can\'t find a request handler to process request!');
            }
        }

        // set response state to be dispatched after this without calling other modules process
        $response->setState(HttpResponseStates::DISPATCH);
    }
}
