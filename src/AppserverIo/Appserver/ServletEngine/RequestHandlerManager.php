<?php

/**
 * AppserverIo\Appserver\ServletEngine\RequestHandlerManager
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

use AppserverIo\Storage\GenericStackable;

/**
 * Manager that handles the creation of request handlers.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class RequestHandlerManager extends \Thread
{

    /**
     * The pool size of request handlers for each application.
     *
     * @var integer
     */
    const POOL_SIZE = 4;

    /**
     * Wait timeout in microseconds until we'll be notfied to check if we've to create new request handlers.
     *
     * @var integer
     */
    const WAIT_TIMEOUT = 5000000;

    /**
     * The number of waiting handlers we always want to have in spare.
     *
     * @var integer
     */
    const MINIMUM_SPARE_HANDLERS = 2;

    /**
     * The system logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $systemLogger;

    /**
     * The request handlers we have to manage.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $requestHandlers;

    /**
     * The valves the request handler has to process for each request.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $valves;

    /**
     * The applications that has to be bound to a request handler.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $applications;

    /**
     * Initializes the request handler manager instance.
     *
     * @param \Psr\Log\LoggerInterface               $systemLogger    The system logger instance
     * @param \AppserverIo\Storage\GenericStackable $requestHandlers The request handlers we have to manage
     * @param \AppserverIo\Storage\GenericStackable $applications    The valves the request handler has to process for each request
     * @param \AppserverIo\Storage\GenericStackable $valves          The applications that has to be bound to a request handler
     */
    public function __construct($systemLogger, $requestHandlers, $applications, $valves)
    {

        // set the passed variables
        $this->systemLogger = $systemLogger;
        $this->requestHandlers = $requestHandlers;
        $this->applications = $applications;
        $this->valves = $valves;

        // autostart the manager
        $this->start(PTHREADS_INHERIT_ALL | PTHREADS_ALLOW_HEADERS);
    }

    /**
     * Starts the request handler manager-
     *
     * @return void
     */
    public function run()
    {

        // create a local copy of the valves
        $valves = $this->valves;
        $applications = $this->applications;
        $systemLogger = $this->systemLogger;
        $requestHandlers = $this->requestHandlers;

        while (true) { // we run forever and make sure that enough request handlers are available

            // log a message that we're waiting to manage request handlers
            $systemLogger->debug(
                sprintf(
                    'Waiting %d s to manage request handlers',
                    RequestHandlerManager::WAIT_TIMEOUT / 1000000
                )
            );

            // wait (max. 10 s) until we'll be notfied to check if we've to create new request handlers
            $this->wait(RequestHandlerManager::WAIT_TIMEOUT);

            // we want to prepare an request for each application and each worker
            foreach ($applications as $applicationName => $application) {

                // initialize the counter for the actual pool size
                $actualPoolSize = sizeof($requestHandlers[$applicationName]);
                $waitingRequestHandlers = 0;

                // shutdown the outdated request handlers
                foreach ($requestHandlers[$applicationName] as $threadId => $requestHandler) {

                    // this one is waiting, so we've one in spare
                    if ($requestHandlers[$applicationName][$threadId]->shouldRestart() === false &&
                        $requestHandlers[$applicationName][$threadId]->isWaiting() === true
                    ) {
                        $waitingRequestHandlers++;
                        continue;
                    }

                    // check if a handler should be restarted
                    if ($requestHandlers[$applicationName][$threadId]->shouldRestart()) {

                        // remove the handler
                        unset($requestHandlers[$applicationName][$threadId]);

                        // log a debug message to the system log
                        $systemLogger->debug(
                            sprintf(
                                'Successfully removed request handler %s for application \'%s\' from pool, pool size is: %d',
                                $threadId,
                                $applicationName,
                                sizeof($requestHandlers[$applicationName])
                            )
                        );
                    }
                }

                // we want at least 2 request handlers in spare
                if ($actualPoolSize < RequestHandlerManager::POOL_SIZE ||
                    $waitingRequestHandlers < RequestHandlerManager::MINIMUM_SPARE_HANDLERS
                ) {

                    // initialize the number request handlers to start
                    $z = 0;

                    // make sure, that at least the minimum pool size of request handlers is available
                    if (($y = RequestHandlerManager::POOL_SIZE - $actualPoolSize) > 0) {
                        $systemLogger->debug(
                            sprintf(
                                'Actual pool size %d is lower than requested pool size (%d)',
                                $actualPoolSize,
                                RequestHandlerManager::POOL_SIZE
                            )
                        );
                        $z += $y;
                    }

                    // if the pool size of worker is available, make sure, that at least the minimum
                    // number of spare request handlers are available
                    if (($x = RequestHandlerManager::MINIMUM_SPARE_HANDLERS - $waitingRequestHandlers) > 0) {
                        $systemLogger->debug(
                            sprintf(
                                'Waiting spare handlers %d is lower than minium value (%d)',
                                $waitingRequestHandlers,
                                RequestHandlerManager::MINIMUM_SPARE_HANDLERS
                            )
                        );
                        $z += $x;
                    }

                    // create the free handlers
                    for ($i = 0; $i < $z; $i++) {

                        // create and start a new request handler
                        $requestHandler = new RequestHandler($application, $valves);
                        $requestHandlers[$applicationName][$requestHandler->getThreadId()] = $requestHandler;

                        // log a debug message to the system log
                        $systemLogger->debug(
                            sprintf(
                                'Successfully started a new request handler %s for application %s',
                                $applicationName,
                                $requestHandler->getThreadId()
                            )
                        );
                    }

                    // log a debug message to the system log
                    $systemLogger->debug(
                        sprintf(
                            'Pool size of request handlers for application %s is: %d',
                            $applicationName,
                            sizeof($requestHandlers[$applicationName])
                        )
                    );
                }
            }
        }
    }
}
