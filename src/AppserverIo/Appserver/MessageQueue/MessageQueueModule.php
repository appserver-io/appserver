<?php

/**
 * AppserverIo\Appserver\MessageQueue\MessageQueueModule
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use TechDivision\Storage\GenericStackable;
use AppserverIo\Http\HttpResponseStates;
use AppserverIo\Http\HttpRequestInterface;
use AppserverIo\Http\HttpResponseInterface;
use TechDivision\Server\Dictionaries\ServerVars;
use TechDivision\Server\Dictionaries\ModuleHooks;
use TechDivision\Server\Interfaces\RequestContextInterface;
use TechDivision\Server\Interfaces\ServerContextInterface;
use TechDivision\Server\Exceptions\ModuleException;
use TechDivision\Connection\ConnectionRequestInterface;
use TechDivision\Connection\ConnectionResponseInterface;
use TechDivision\MessageQueueProtocol\QueueContext;
use TechDivision\MessageQueueProtocol\Utils\PriorityKeys;
use TechDivision\MessageQueueProtocol\MessageQueueProtocol;

/**
 * A message queue module implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
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
     *
     * @return void
     */
    public function __construct()
    {

        // initialize the members
        $this->queues = new GenericStackable();
        $this->messageWrapperFactory = new MessageWrapperFactory();
    }

    /**
     * Prepares the module for upcoming request in specific context
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
    }

    /**
     * Initializes the module.
     *
     * @param \TechDivision\Server\Interfaces\ServerContextInterface $serverContext The servers context instance
     *
     * @return void
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function init(ServerContextInterface $serverContext)
    {
        try {

            // create a queue worker for each application
            foreach ($serverContext->getContainer()->getApplications() as $application) {

                // load the queue manager to check if there are queues registered for the application
                if ($queueManager = $application->search('QueueContext')) {

                    // if yes, initialize and start the queue worker
                    foreach ($queueManager->getQueues() as $queue) {

                        // initialize the queues storage for the priorities
                        $this->queues[$queueName = $queue->getName()] = new GenericStackable();

                        // create a separate queue for each priority
                        foreach (PriorityKeys::getAll() as $priorityKey) {
                            $this->queues[$queueName][$priorityKey] = new QueueWorker($priorityKey, $application);
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
     * @param \TechDivision\Connection\ConnectionRequestInterface     $request        A request object
     * @param \TechDivision\Connection\ConnectionResponseInterface    $response       A response object
     * @param \TechDivision\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                     $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function process(ConnectionRequestInterface $request, ConnectionResponseInterface $response, RequestContextInterface $requestContext, $hook)
    {

        try {

            // In php an interface is, by definition, a fixed contract. It is immutable.
            // So we have to declair the right ones afterwards...
            /** @var $request \AppserverIo\Http\HttpRequestInterface */
            /** @var $request \AppserverIo\Http\HttpResponseInterface */

            // if false hook is comming do nothing
            if (ModuleHooks::REQUEST_POST !== $hook) {
                return;
            }

            // check if we are the handler that has to process this request
            if ($requestContext->getServerVar(ServerVars::SERVER_HANDLER) !== $this->getModuleName()) {
                return;
            }

            // unpack the message from the request body
            $message = $this->messageWrapperFactory->emptyInstance();
            $message->init(MessageQueueProtocol::unpack($request->getBodyContent()));

            // load queue name and priority key
            $queueName = $message->getDestination()->getName();
            $priorityKey = $message->getPriority();

            // prevents to attach message to none existing queue
            if (!isset($this->queues[$queueName][$priorityKey])) {
                throw new ModuleException(sprintf("Queue %s not found", $queueName));
            }

            // attach the message to the queue found as message destination
            $this->queues[$queueName][$priorityKey]->attach($message);

            // set response state to be dispatched after this without calling other modules process
            $response->setState(HttpResponseStates::DISPATCH);

        } catch (ModuleException $me) {
            throw $me;
        } catch (\Exception $e) {
            throw new ModuleException($e, 500);
        }
    }
}
