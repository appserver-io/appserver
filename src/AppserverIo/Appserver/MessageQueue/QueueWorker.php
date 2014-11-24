<?php

/**
 * AppserverIo\Appserver\MessageQueue\QueueWorker
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\Naming\InitialContext;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\MessageQueueProtocol\Message;
use AppserverIo\Psr\MessageQueueProtocol\QueueContext;
use AppserverIo\Psr\MessageQueueProtocol\Utils\PriorityKey;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStateActive;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStateFailed;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStateInProgress;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStatePaused;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStateProcessed;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStateToProcess;
use AppserverIo\Psr\MessageQueueProtocol\Utils\MQStateUnknown;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\PersistenceContainerProtocol\BeanContext;
use AppserverIo\Psr\MessageQueueProtocol\Utils\PriorityMedium;

/**
 * A message queue worker implementation listening to a queue, defined in the passed application.
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
class QueueWorker extends \Thread
{

    /**
     * Initializes the queue worker with the application and the storage it should work on.
     *
     * @param \AppserverIo\Psr\MessageQueueProtocol\Utils\PriorityKey $priorityKey The priority of this queue worker
     * @param \AppserverIo\Psr\Application\ApplicationInterface       $application The application instance with the queue manager/locator
     *
     * @return void
     */
    public function __construct(PriorityKey $priorityKey, ApplicationInterface $application)
    {

        // bind the worker to the application
        $this->priorityKey = $priorityKey;
        $this->application = $application;

        // initialize the message and priority storage
        $this->storage = new GenericStackable();

        // start the worker
        $this->start();
    }

    /**
     * Attach a new message to the queue.
     *
     * @param \AppserverIo\Psr\MessageQueueProtocol\Message $message the message to be attached to the queue
     *
     * @return void
     */
    protected function attach(Message $message)
    {

        // add the new message to the message and priority storage
        $this->storage[$message->getMessageId()] = $message;
    }

    /**
     * Removes the message from the queue.
     *
     * @param \AppserverIo\Psr\MessageQueueProtocol\Message $message The message to be removed from the queue
     *
     * @return void
     */
    protected function remove(Message $message)
    {
        unset($this->storage[$message->getMessageId()]);
    }

    /**
     * We process the messages here.
     *
     * @return void
     */
    public function run()
    {

        // create a local instance of appication and storage
        $application = $this->application;

        // register the class loader again, because each thread has its own context
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext(sprintf('queue-worker-%s', $this->priorityKey));
        }

        /*
         * Reduce CPU load depending on the queues priority, whereas priority
         * can be 1, 2 or 3 actually, so possible values for usleep are:
         *
         * PriorityHigh:         100 === 0.0001 s
         * PriorityMedium:    10.000 === 0.01 s
         * PriorityLow:    1.000.000 === 1 s
         */
        $sleepFor = pow(10, $this->priorityKey->getPriority() * 2);

        while (true) { // run forever

            // iterate over all messages found in the message storage
            foreach ($this->storage as $messageId => $message) {

                // check the message state
                switch ($message->getState()) {

                    case MQStateActive::get(): // message is active and ready to be processed

                        // message is ready to be processed
                        $message->setState(MQStateToProcess::get());
                        break;

                    case MQStatePaused::get(): // message is paused
                    case MQStateInProgress::get(): // message is in progress

                        // do nothing here because everything is OK!
                        break;

                    case MQStateFailed::get(): // message processing has been failure
                    case MQStateProcessed::get(): // message processing has been successfully processed

                        // we remove the message to free the memory
                        $this->remove($message);
                        break;

                    case MQStateToProcess::get(): // message has to be processed now

                        // load class name and session ID from remote method
                        $queueProxy = $message->getDestination();
                        $sessionId = $message->getSessionId();

                        // lookup the queue and process the message
                        if ($queue = $application->search('QueueContext')->locate($queueProxy)) {

                            // lock the message
                            $message->setState(MQStateInProgress::get());

                            // the queues receiver type
                            $queueType = $queue->getType();

                            // create an intial context instance
                            $initialContext = new InitialContext();
                            $initialContext->injectApplication($application);

                            // lookup the bean instance
                            $instance = $initialContext->lookup($queueType);

                            // inject the application to the receiver and process the message
                            $instance->onMessage($message, $sessionId);

                            // remove the message from the storage
                            $message->setState(MQStateProcessed::get());
                        }

                        break;

                    case MQStateUnknown::get(): // message is in an unknown state -> this is weired and should never happen!

                        // throw an exception, because this should never happen
                        throw \Exception(sprintf('Message %s has state %s', $messageId, $message->getState()));
                        break;

                    default: // we don't know the message state -> this is weired and should never happen!

                        // throw an exception, because this should never happen
                        throw \Exception(sprintf('Message %s has an invalid state', $messageId));
                        break;
                }

                // reduce CPU load depending on queue priority
                usleep($sleepFor);
            }

            if ($profileLogger) { // profile the size of the session pool
                $profileLogger->debug(
                    sprintf('Processed queue worker with priority %s, size of queue size is: %d', $this->priorityKey, sizeof($this->storage))
                );
            }

            // we maximal check the storage once a second
            sleep(1);
        }
    }
}
