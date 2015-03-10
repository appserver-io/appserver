<?php

/**
 * \AppserverIo\Appserver\MessageQueue\QueueWorker
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

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Pms\JobInterface;
use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Psr\Pms\PriorityKeyInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Messaging\Utils\StateActive;
use AppserverIo\Messaging\Utils\StateFailed;
use AppserverIo\Messaging\Utils\StateInProgress;
use AppserverIo\Messaging\Utils\StatePaused;
use AppserverIo\Messaging\Utils\StateProcessed;
use AppserverIo\Messaging\Utils\StateToProcess;
use AppserverIo\Messaging\Utils\StateUnknown;

/**
 * A message queue worker implementation listening to a queue, defined in the passed application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface $application   The application instance with the queue manager/locator
 * @property \AppserverIo\Storage\GenericStackable             $jobsExecuting The storage for the jobs currently executing
 * @property \AppserverIo\Storage\GenericStackable             $jobsToExecute The storage for the jobs to be executed
 * @property \AppserverIo\Storage\GenericStackable             $messages      The storage for the messages
 * @property \AppserverIo\Storage\GenericStackable             $messageStates The storage for the messages' states
 * @property \AppserverIo\Psr\Pms\PriorityKeyInterface         $priorityKey   The priority of this queue worker
 */
class QueueWorker extends \Thread
{

    /**
     * Injects the priority of the queue worker.
     *
     * @param \AppserverIo\Psr\Pms\PriorityKeyInterface $priorityKey The priority of this queue worker
     *
     * @return void
     */
    public function injectPriorityKey(PriorityKeyInterface $priorityKey)
    {
        $this->priorityKey = $priorityKey;
    }

    /**
     * Inject the storage for the jobs to be executed.
     *
     * @param \AppserverIo\Storage\GenericStackable $jobsToExecute The storage for the jobs to be executed
     *
     * @return void
     */
    public function injectJobsToExecute(GenericStackable $jobsToExecute)
    {
        $this->jobsToExecute = $jobsToExecute;
    }

    /**
     * Inject the storage for the message states.
     *
     * @param \AppserverIo\Storage\GenericStackable $messageStates The storage for the message states
     *
     * @return void
     */
    public function injectMessageStates(GenericStackable $messageStates)
    {
        $this->messageStates = $messageStates;
    }

    /**
     * Inject the storage for the executing jobs.
     *
     * @param array $jobsExecuting The storage for the executing jobs
     *
     * @return void
     */
    public function injectJobsExecuting(array $jobsExecuting)
    {
        $this->jobsExecuting = $jobsExecuting;
    }

    /**
     * Inject the storage for the messages.
     *
     * @param \AppserverIo\Storage\GenericStackable $messages The storage for the messages
     *
     * @return void
     */
    public function injectMessages(GenericStackable $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Inject the application instance the worker is bound to.
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
     * Returns the application instance the worker is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Attach a new message to the queue.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The messsage to be attached to the queue
     *
     * @return void
     */
    public function attach(MessageInterface $message)
    {

        // force handling the timer tasks now
        $this->synchronized(function (QueueWorker $self, MessageInterface $m) {

            // store the job-ID and the PK of the message => necessary to load the message later
            $jobWrapper = new \stdClass();
            $jobWrapper->jobId = $m->getMessageId();
            $jobWrapper->messageId = $m->getMessageId();

            // attach the job wrapper
            $self->jobsToExecute[$jobWrapper->jobId] = $jobWrapper;
            $this->messageStates[$jobWrapper->jobId] = StateActive::KEY;

        }, $this, $message);
    }

    /**
     * Removes the message from the queue.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be removed from the queue
     *
     * @return void
     */
    public function remove(MessageInterface $message)
    {
        unset($this->messages[$message->getMessageId()]);
        unset($this->messageStates[$message->getMessageId()]);
    }

    /**
     * Process a message with the state `StateActive.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be processed
     *
     * @return void
     */
    public function processActive(MessageInterface $message)
    {

        // set new state
        $this->messageStates[$message->getMessageId()] = StateToProcess::KEY;
    }

    /**
     * Process a message with the state `StateInProgress.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be processed
     *
     * @return void
     */
    public function processInProgress(MessageInterface $message)
    {

        // make sure the job has been finished
        if (isset($this->jobsExecuting[$message->getMessageId()]) &&
            $this->jobsExecuting[$message->getMessageId()] instanceof JobInterface &&
            $this->jobsExecuting[$message->getMessageId()]->isFinished()
        ) {
            // log a message that the job is still in progress
            $this->getApplication()->getInitialContext()->getSystemLogger()->info(
                sprintf('Job %s has been finished, remove it from job queue now', $message->getMessageId())
            );

            // we also remove the job
            unset($this->jobsExecuting[$message->getMessageId()]);

            // set new state
            $this->messageStates[$message->getMessageId()] = StateProcessed::KEY;

        } else {
            // log a message that the job is still in progress
            $this->getApplication()->getInitialContext()->getSystemLogger()->debug(
                sprintf('Job %s is still in progress', $message->getMessageId())
            );
        }
    }


    /**
     * Process a message with the state `StateProcessed.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be processed
     *
     * @return void
     */
    public function processProcessed(MessageInterface $message)
    {
        // remove the job from the queue with jobs that has to be executed
        unset($this->jobsToExecute[$message->getMessageId()]);
        // remove the message from the queue
        $this->remove($message);
    }

    /**
     * Process a message with the state `StateToProcess.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be processed
     *
     * @return void
     */
    public function processToProcess(MessageInterface $message)
    {

        // count messages in queue
        $inQueue = sizeof($this->jobsExecuting);

        // we only process 50 jobs in parallel
        if ($inQueue < 50) {
            // load application
            $application = $this->getApplication();

            // start the job and add it to the internal array
            $this->jobsExecuting[$message->getMessageId()] = new Job($message, $application);

            // set new state
            $this->messageStates[$message->getMessageId()] = StateInProgress::KEY;

        } else {
            // log a message that queue is actually full
            $this->getApplication()->getInitialContext()->getSystemLogger()->debug(
                sprintf('Job queue full - (%d jobs/%d msg wait)', $inQueue, sizeof($this->messages))
            );
        }
    }

    /**
     * Process a message with the state `StateUnknown.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be processed
     *
     * @return void
     */
    public function processUnknown(MessageInterface $message)
    {

        // set new state
        $this->messageStates[$message->getMessageId()] = StateFailed::KEY;

        // log a message that we've a message with a unknown state
        $this->getApplication()->getInitialContext()->getSystemLogger()->critical(
            sprintf('Message %s has state %s', $message->getMessageId(), StateFailed::KEY)
        );
    }

    /**
     * Process a message with an invalid state.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to be processed
     *
     * @return void
     */
    public function processInvalid(MessageInterface $message)
    {

        // set new state
        $this->messageStates[$message->getMessageId()] = StateFailed::KEY;

        // log a message that we've a message with an invalid state
        $this->getApplication()->getInitialContext()->getSystemLogger()->critical(
            sprintf('Message %s has an invalid state', $message->getMessageId())
        );
    }

    /**
     * We process the messages/jobs here.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function run()
    {

        // create a local instance of application and storage
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

        // run forever
        while (true) {
            // iterate over all job wrappers
            foreach ($this->jobsToExecute as $jobWrapper) {
                try {
                    // load the message
                    $message = $this->messages[$jobWrapper->jobId];
                    // check if we've a message found
                    if ($message instanceof MessageInterface) {
                        // check the message state
                        switch ($this->messageStates[$jobWrapper->jobId]) {

                            // message is active and ready to be processed
                            case StateActive::KEY:

                                $this->processActive($message);
                                break;

                            // message is paused or in progress
                            case StatePaused::KEY:
                            case StateInProgress::KEY:

                                $this->processInProgress($message);
                                break;

                            // message processing failed or has been successfully processed
                            case StateFailed::KEY:
                            case StateProcessed::KEY:

                                $this->processProcessed($message);
                                break;

                            // message has to be processed now
                            case StateToProcess::KEY:

                                $this->processToProcess($message);
                                break;

                            // message is in an unknown state -> this is weired and should never happen!
                            case StateUnknown::KEY:

                                $this->processUnknown($message);
                                break;

                            // we don't know the message state -> this is weired and should never happen!
                            default:

                                $this->processInvalid($message);
                                break;
                        }
                    }

                // catch all exceptions
                } catch (\Exception $e) {
                    $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
                }

                // reduce CPU load depending on queue priority
                usleep($sleepFor);
            }

            // profile the size of the session pool
            if ($profileLogger) {
                $profileLogger->debug(
                    sprintf('Processed queue worker with priority %s, size of queue size is: %d', $this->priorityKey, sizeof($this->storage))
                );
            }

            // we maximal check the storage once a second
            sleep(1);
        }
    }
}
