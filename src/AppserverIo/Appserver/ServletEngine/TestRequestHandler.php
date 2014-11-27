<?php

/**
 * AppserverIo\Appserver\ServletEngine\TestRequestHandler
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ServletModule
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Psr\Servlet\Http\HttpServletRequest;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * This is a request handler that is necessary to process each request of an
 * application in a separate context.
 *
 * @category  Appserver
 * @package   TechDivision_ServletModule
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class TestRequestHandler extends \Thread
{

    public function injectMutex($mutex)
    {
        $this->mutex = $mutex;
    }

    /**
     * Injects the valves to be processed.
     *
     * @param \AppserverIo\Storage\GenericStackable $valves The valves to process
     *
     * @return void
     */
    public function injectValves(GenericStackable $valves)
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
     * Handles the request processing.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequest $servletRequest The actual request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponse $servletResponse The actual response instance
     *
     * @return void
     */
    public function handleRequest(HttpServletRequest $servletRequest, HttpServletResponse $servletResponse)
    {

        // lock the method
        \Mutex::lock($this->mutex);

        do {

            // create a counter
            $counter = 0;

            // if this is the first loop
            if ($counter === 0) {

                // we're not dispatched
                $this->dispatched = false;

                // synchronize request/response
                $this->servletRequest = $servletRequest;
                $this->servletResponse = $servletResponse;

                // notify the thread
                $this->synchronized(function ($self) {
                    $self->notify();
                }, $this);

            }

            // raise the counter
            $counter++;

            if ($counter > 100) { // we wait for 100 iterations
                throw new \Exception('Can\'t handle request');
            }

            // lower system load a bit
            usleep(100);

        } while ($this->dispatched === false);

        // unlock the method
        \Mutex::unlock($this->mutex);
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

        // reset request/response instance
        $application = $this->application;

        // register class loaders
        $application->registerClassLoaders();

        // synchronize the valves
        $valves = $this->valves;

        while (true) { // run forever

            try {

                // wait until we've been notified
                $this->synchronized(function ($self) {
                    $self->wait();
                }, $this);

                // servlet request/response
                $servletRequest = $this->servletRequest;
                $servletResponse = $this->servletResponse;

                // prepare and set the applications context path
                $servletRequest->setContextPath($contextPath = '/' . $application->getName());

                // prepare the path information depending if we're in a vhost or not
                if ($application->isVhostOf($servletRequest->getServerVar(ServerVars::SERVER_NAME)) === false) {
                    $servletRequest->setServletPath(str_replace($contextPath, '', $servletRequest->getServletPath()));
                }

                // inject the found application into the servlet request
                $servletRequest->injectContext($application);

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
                error_log($e->__toString());
                $servletResponse->appendBodyStream($e->__toString());
                $servletResponse->setStatusCode(500);
            }

            // we're dispatched now
            $this->dispatched = true;
        }

        // shutdown the thread
        $this->__shutdown();
    }

    /**
     * Thread shutdown function that allows you to cleanup
     * garbage.
     *
     * @return void
     * @since appserver.io/pthreads >= 1.0.2
     */
    public function __shutdown()
    {
        $this->servletRequest->__cleanup();
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

            // synchronize the servlet response
            $servletResponse = $this->servletResponse;

            // set the status code and append the error message to the body
            $servletResponse->setStatusCode(500);
            $servletResponse->appendBodyStream($lastError['message']);
        }

        // shutdown the thread
        $this->__shutdown();
    }
}
