<?php

/**
 * \AppserverIo\Appserver\MessageQueue\MessageQueueModule
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Server\Dictionaries\ServerVars;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;
use AppserverIo\Server\Exceptions\ModuleException;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Messaging\Utils\PriorityKeys;
use AppserverIo\Messaging\MessageQueueProtocol;
use AppserverIo\Messaging\Utils\StateActive;

/**
 * A message queue module implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageQueueModule extends GenericStackable
{

    /**
     * The unique module name in the web server context.
     *
     * @var string
     */
    const MODULE_NAME = 'message-queue';

    /**
     * Returns the module name.
     *
     * @return string The module name
     */
    public function getModuleName()
    {
        return MessageQueueModule::MODULE_NAME;
    }

    /**
     * Initialize the module and the necessary members.
     */
    public function __construct()
    {

        // initialize the mutex
        $this->mutex = \Mutex::create();

        // initialize the members
        $this->queues = new GenericStackable();
        $this->messages = new GenericStackable();

        // initialize the array containing the worker specific stackables
        $this->jobsExecuting = new GenericStackable();
        $this->jobsToExecute = new GenericStackable();
        $this->messageStates = new GenericStackable();
    }

    /**
     * Prepares the module for upcoming request in specific context
     *
     * @return bool
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
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
            // initialize the job counter
            $jobCounter = 0;

            // create a queue worker for each application
            foreach ($serverContext->getContainer()->getApplications() as $application) {
                // load the queue manager to check if there are queues registered for the application
                if ($queueManager = $application->search('QueueContextInterface')) {
                    // if yes, initialize and start the queue worker
                    foreach ($queueManager->getQueues() as $queue) {
                        // initialize the queues storage for the priorities
                        $this->queues[$queueName = $queue->getName()] = new GenericStackable();

                        // create a separate queue for each priority
                        foreach (PriorityKeys::getAll() as $priorityKey) {
                            // initialize the stackable for the job storage and the jobs executing
                            $this->jobsExecuting[$jobCounter] = array();
                            $this->jobsToExecute[$jobCounter] = new GenericStackable();
                            $this->messageStates[$jobCounter] = new GenericStackable();

                            // initialize and start the queue worker
                            $queueWorker = new QueueWorker();
                            $queueWorker->injectPriorityKey($priorityKey);
                            $queueWorker->injectApplication($application);
                            $queueWorker->injectMessages($this->messages);
                            $queueWorker->injectJobsExecuting($this->jobsExecuting[$jobCounter]);
                            $queueWorker->injectJobsToExecute($this->jobsToExecute[$jobCounter]);
                            $queueWorker->injectMessageStates($this->messageStates[$jobCounter]);
                            $queueWorker->start();

                            // add the queue instance to the module
                            $this->queues[$queueName][$priorityKey] = $queueWorker;

                            // raise the job counter
                            $jobCounter++;
                        }
                    }
                }
            }

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

        try {
            // if false hook is coming do nothing
            if (ModuleHooks::REQUEST_POST !== $hook) {
                return;
            }

            // check if we are the handler that has to process this request
            if ($requestContext->getServerVar(ServerVars::SERVER_HANDLER) !== $this->getModuleName()) {
                return;
            }

            $message = MessageQueueProtocol::unpack($request->getBodyContent());
            $message->setState(StateActive::get());

            // load queue name and priority key
            $queueName = $message->getDestination()->getName();
            $priorityKey = $message->getPriority();

            // prevents to attach message to none existing queue
            if (isset($this->queues[$queueName][$priorityKey]) === false) {
                throw new ModuleException(sprintf("Queue %s not found", $queueName));
            }

            // add the message to the queue
            $this->messages[$message->getMessageId()] = $message;

            // attach the message to the queue found as message destination
            $queue = $this->queues[$queueName][$priorityKey];
            $queue->attach($message);

            // set response state to be dispatched after this without calling other modules process
            $response->setState(HttpResponseStates::DISPATCH);

        } catch (ModuleException $me) {
            throw $me;
        } catch (\Exception $e) {
            throw new ModuleException($e, 500);
        }
    }
}
