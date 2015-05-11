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
 * @property boolean                                           $run           Flag to start/stop the worker
 * @property \AppserverIo\Psr\Application\ApplicationInterface $application   The application instance with the queue manager/locator
 * @property \AppserverIo\Storage\GenericStackable             $jobsToExecute The storage for the jobs to be executed
 * @property \AppserverIo\Storage\GenericStackable             $messages      The storage for the messages
 * @property \AppserverIo\Psr\Pms\PriorityKeyInterface         $priorityKey   The priority of this queue worker
 */
class QueueWorker extends \Thread
{

    /**
     * Sets the workers start flag.
     */
    public function __construct()
    {
        $this->run = true;
    }

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
     * Attaches a job for the passed wrapper to the worker instance.
     *
     * @param \stdClass $jobWrapper The job wrapper to attach the job for
     *
     * @return void
     */
    public function attach(\stdClass $jobWrapper)
    {

        // attach the job wrapper
        $this->synchronized(function (QueueWorker $self, \stdClass $jw) {
            $self->jobsToExecute[$jw->jobId] = $jw;
        }, $this, $jobWrapper);
    }

    /**
     * Stops the worker instance.
     *
     * @return void
     */
    public function stop()
    {
        $this->synchronized(function ($self) {
            $self->run = false;
        }, $this);
    }

    /**
     * Does shutdown logic for request handler if something went wrong and
     * produces a fatal error for example.
     *
     * @return void
     */
    public function shutdown()
    {

        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize type + message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                $this->getApplication()->getInitialContext()->getSystemLogger()->error($message);
            }
        }
    }

    /**
     * We process the messages/jobs here.
     *
     * @return void
     */
    public function run()
    {

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // create a local instance of application and storage
        $application = $this->application;

        // create local instances of the storages
        $messages = $this->messages;
        $priorityKey = $this->priorityKey;
        $jobsToExecute = $this->jobsToExecute;

        // initialize the arrays for the message states and the jobs executing
        $messageStates = array();
        $jobsExecuting = array();

        // register the class loader again, because each thread has its own context
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext(sprintf('queue-worker-%s', $priorityKey));
        }

        /*
         * Reduce CPU load depending on the queues priority, whereas priority
         * can be 1, 2 or 3 actually, so possible values for usleep are:
         *
         * PriorityHigh:         100 === 0.0001 s
         * PriorityMedium:    10.000 === 0.01 s
         * PriorityLow:    1.000.000 === 1 s
         */
        $sleepFor = pow(10, $priorityKey->getPriority() * 2);

        // run until we've to stop
        while ($this->run) {
            // iterate over all job wrappers
            foreach ($jobsToExecute as $jobWrapper) {
                try {
                    // load the message
                    $message = $messages[$jobWrapper->jobId];

                    // check if we've a message found
                    if ($message instanceof MessageInterface) {
                        // set the inital message state if not done
                        if (isset($messageStates[$jobWrapper->jobId]) === false) {
                            // initialize the default message state
                            if ($state = $message->getState()) {
                                $messageStates[$jobWrapper->jobId] = $state->getState();
                            } else {
                                $messageStates[$jobWrapper->jobId] = StateUnknown::KEY;
                            }
                        }

                        // check the message state
                        switch ($messageStates[$jobWrapper->jobId]) {

                            // message is active and ready to be processed
                            case StateActive::KEY:

                                // set the new state now
                                $messageStates[$message->getMessageId()] = StateToProcess::KEY;

                                break;

                            // message is paused or in progress
                            case StatePaused::KEY:
                            case StateInProgress::KEY:

                                // make sure the job has been finished
                                if (isset($jobsExecuting[$message->getMessageId()]) &&
                                    $jobsExecuting[$message->getMessageId()] instanceof JobInterface &&
                                    $jobsExecuting[$message->getMessageId()]->isFinished()
                                ) {
                                    // log a message that the job is still in progress
                                    $this->getApplication()->getInitialContext()->getSystemLogger()->info(
                                        sprintf('Job %s has been finished, remove it from job queue now', $message->getMessageId())
                                    );

                                    // set the new state now
                                    $messageStates[$message->getMessageId()] = StateProcessed::KEY;

                                } else {
                                    // log a message that the job is still in progress
                                    $this->getApplication()->getInitialContext()->getSystemLogger()->debug(
                                        sprintf('Job %s is still in progress', $message->getMessageId())
                                    );
                                }

                                break;

                            // message processing failed or has been successfully processed
                            case StateFailed::KEY:
                            case StateProcessed::KEY:

                                // load the unique message-ID
                                $messageId = $message->getMessageId();

                                // remove the job from the queue with jobs that has to be executed
                                unset($jobsToExecute[$messageId]);

                                // also remove the job
                                unset($jobsExecuting[$messageId]);

                                // finally, remove the message states and the message from the queue
                                unset($messageStates[$messageId]);
                                unset($messages[$messageId]);

                                break;

                            // message has to be processed now
                            case StateToProcess::KEY:

                                // count messages in queue
                                $inQueue = sizeof($jobsExecuting);

                                // we only process 50 jobs in parallel
                                if ($inQueue < 200) {
                                    // start the job and add it to the internal array
                                    $jobsExecuting[$message->getMessageId()] = new Job(clone $message, $application);

                                    // set the new state now
                                    $messageStates[$message->getMessageId()] = StateInProgress::KEY;

                                } else {
                                    // log a message that queue is actually full
                                    $application->getInitialContext()->getSystemLogger()->info(
                                        sprintf('Job queue full - (%d jobs/%d msg wait)', $inQueue, sizeof($messages))
                                    );
                                }

                                break;

                            // message is in an unknown state -> this is weired and should never happen!
                            case StateUnknown::KEY:

                                // set new state now
                                $messageStates[$message->getMessageId()] = StateFailed::KEY;

                                // log a message that we've a message with a unknown state
                                $this->getApplication()->getInitialContext()->getSystemLogger()->critical(
                                    sprintf('Message %s has state %s', $message->getMessageId(), StateFailed::KEY)
                                );

                                break;

                            // we don't know the message state -> this is weired and should never happen!
                            default:

                                // set the failed message state
                                $messageStates[$message->getMessageId()] = StateFailed::KEY;

                                // log a message that we've a message with an invalid state
                                $this->getApplication()->getInitialContext()->getSystemLogger()->critical(
                                    sprintf('Message %s has an invalid state', $message->getMessageId())
                                );

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
                    sprintf('Processed queue worker with priority %s, size of queue size is: %d', $priorityKey, sizeof($jobsToExecute))
                );
            }

            // we maximal check the storage once a second
            sleep(1);
        }
    }
}
