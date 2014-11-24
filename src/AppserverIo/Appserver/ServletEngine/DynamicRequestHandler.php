<?php

/**
 * AppserverIo\Appserver\ServletEngine\DynamicRequestHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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

use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Server\Dictionaries\ServerVars;

/**
 * This is a request handler that is necessary to process each request of an
 * application in a separate context.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DynamicRequestHandler extends \Thread
{

    /**
     * The number of request to be handled.
     *
     * @var integer
     */
    const HANDLE_REQUESTS = 10;

    /**
     * The minimum number of seconds requests can be handled.
     *
     * @var integer
     */
    const TIME_TO_LIVE_MINIMUM = 10;

    /**
     * The maximum number of seconds requests can be handled.
     *
     * @var integer
     */
    const TIME_TO_LIVE_MAXIMUM = 50;

    /**
     * The application instance we're processing requests for.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ApplicationInterface
     */
    protected $application;

    /**
     * The valves we're processing each request with.
     *
     * @return \AppserverIo\Storage\GenericStackable
     */
    protected $valves;

    /**
     * The actual request instance we have to process.
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpServletRequest
     */
    protected $servletRequest;

    /**
     * The actual response instance we have to process.
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpServletResponse
     */
    protected $servletResponse;

    /**
     * Flag to allow/disallow request handling.
     *
     * @return boolean
     */
    protected $handleRequest;

    /**
     * Flag if request handler should be restarted by servlet engine
     *
     * @var boolean
     */
    protected $shouldRestart;

    /**
     * Initializes the request handler with the application and the
     * valves to be processed
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     * @param \AppserverIo\Storage\GenericStackable             $valves      The valves to process
     */
    public function __construct(ApplicationInterface $application, $valves)
    {

        // initialize the request handlers application
        $this->application = $application;
        $this->valves = $valves;

        // we don't want to restart now
        $this->handleRequest = false;

        // autostart the handler
        $this->start();
    }

    /**
     * Returns the valves we're processing each request with.
     *
     * @return \AppserverIo\Storage\GenericStackable The valves
     */
    protected function getValves()
    {
        return $this->valves;
    }

    /**
     * The main method that handles the thread in a separate context.
     *
     * @return void
     */
    public function run()
    {

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // set should restart initial flag
        $this->shouldRestart = false;

        // start handling requests
        $handledRequests = 0;

        // initialize the time we've been started to handle requests
        $createdAt = time();

        // initialize the TTL in seconds for this request handler
        $ttl = rand(DynamicRequestHandler::TIME_TO_LIVE_MINIMUM, DynamicRequestHandler::TIME_TO_LIVE_MAXIMUM);

        do { // let's start handling requests

            // synchronize the response data
            $this->synchronized(function ($self, $timeToLive) {

                // wait until we've to handle a new request
                $self->wait(1000000 * $timeToLive);

                // check if we've to handle a request
                if ($self->handleRequest) {

                    try {

                        // reset the flag
                        $self->handleRequest = false;

                        // reset request/response instance
                        $application = $self->application;

                        // register the class loader again, because each thread has its own context
                        $application->registerClassLoaders();

                        // synchronize the servlet request/response
                        $servletRequest = $self->servletRequest;
                        $servletResponse = $self->servletResponse;

                        // prepare and set the applications context path
                        $servletRequest->setContextPath($contextPath = '/' . $application->getName());

                        // prepare the path information depending if we're in a vhost or not
                        if ($application->isVhostOf($servletRequest->getServerVar(ServerVars::SERVER_NAME)) === false) {
                            $servletRequest->setServletPath(str_replace($contextPath, '', $servletRequest->getServletPath()));
                        }

                        // inject the found application into the servlet request
                        $servletRequest->injectContext($application);

                        // process the valves
                        foreach ($this->getValves() as $valve) {
                            $valve->invoke($servletRequest, $servletResponse);
                            if ($servletRequest->isDispatched() === true) {
                                break;
                            }
                        }

                    } catch (\Exception $e) {
                        error_log($e->__toString());
                        $servletResponse->appendBodyStream($e->__toString());
                        $servletResponse->setStatusCode(500);
                    }

                    // set the request state to dispatched
                    $servletResponse->setState(HttpResponseStates::DISPATCH);
                }

            }, $this, $ttl);

            // raise the number of handled requests
            $handledRequests++;

        // check if we've to handle anymore requests
        } while ($handledRequests < DynamicRequestHandler::HANDLE_REQUESTS || $createdAt + $ttl > time());
    }

    /**
     * Returns TRUE if the request handler should be restarted by the servlet engine.
     *
     * @return boolean TRUE if the request handler should be restarted
     */
    public function shouldRestart()
    {
        return $this->shouldRestart;
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
            error_log($lastError['message']);
        }

        // this request handler has to be restarted
        $this->shouldRestart = true;
    }
}
