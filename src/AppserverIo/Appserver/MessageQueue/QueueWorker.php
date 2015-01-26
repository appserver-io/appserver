<?php

/**
 * AppserverIo\Appserver\MessageQueue\QueueWorker
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
use AppserverIo\Psr\Pms\Message;
use AppserverIo\Psr\Pms\PriorityKey;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Messaging\Utils\PriorityMedium;
use AppserverIo\Messaging\Utils\StateActive;
use AppserverIo\Messaging\Utils\StateFailed;
use AppserverIo\Messaging\Utils\StateInProgress;
use AppserverIo\Messaging\Utils\StatePaused;
use AppserverIo\Messaging\Utils\StateProcessed;
use AppserverIo\Messaging\Utils\StateToProcess;
use AppserverIo\Messaging\Utils\StateUnknown;
use AppserverIo\Appserver\Naming\InitialContext;

/**
 * A message queue worker implementation listening to a queue, defined in the passed application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class QueueWorker extends \Thread
{

    /**
     * Injects the priority of the queue worker.
     *
     * @param \AppserverIo\Psr\Pms\PriorityKey $priorityKey The priority of this queue worker
     *
     * @return void
     */
    public function injectPriorityKey(PriorityKey $priorityKey)
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
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance with the queue manager/locator
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Attach a new message to the queue.
     *
     * @param \AppserverIo\Psr\Pms\Message $message The messsage to be attached to the queue
     *
     * @return void
     */
    public function attach(Message $message)
    {

        // force handling the timer tasks now
        $this->synchronized(function ($self, $m) {

            // store the job-ID and the PK of the message => necessary to load the message later
            $jobWrapper = new \stdClass();
            $jobWrapper->jobId = uniqid();
            $jobWrapper->messageId = $m->getMessageId();

            // attach the job wrapper
            $self->jobsToExecute[$jobWrapper->jobId] = $jobWrapper;

        }, $this, $message);
    }

    /**
     * Removes the message from the queue.
     *
     * @param \AppserverIo\Psr\Pms\Message $message The message to be removed from the queue
     *
     * @return void
     */
    public function remove(Message $message)
    {
        unset($this->messages[$message->getMessageId()]);
    }

    /**
     * We process the messages/jobs here.
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

        // initialize an array with jobs that are executed actually
        $jobsExecuting = array();

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
            foreach ($this->jobsToExecute as $jobId => $jobWrapper) {
                // load the message
                $message = $this->messages[$jobWrapper->messageId];

                // check the message state
                switch ($message->getState()) {

                    case StateActive::get(): // message is active and ready to be processed

                        // message is ready to be processed
                        $message->setState(StateToProcess::get());
                        break;

                    case StatePaused::get(): // message is paused
                    case StateInProgress::get(): // message is in progress

                        // do nothing here because everything is OK!
                        break;

                    case StateFailed::get(): // message processing has been failure
                    case StateProcessed::get(): // message processing has been successfully processed

                        // make sure the job has been finished
                        if ($jobsExecuting[$jobId]->isFinished()) {
                            // we remove the message to free the memory
                            $this->remove($message);

                            // we also remove the job
                            unset($jobsExecuting[$jobId]);
                        }

                        break;

                    case StateToProcess::get(): // message has to be processed now

                        // start the job and add it to the internal array
                        $jobsExecuting[$jobId] = $message->getJob($application);

                        // remove the job from the list of jobs to be executed
                        unset($this->jobsToExecute[$jobId]);
                        break;

                    case StateUnknown::get(): // message is in an unknown state -> this is weired and should never happen!

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

            if ($profileLogger) {
                // profile the size of the session pool
                $profileLogger->debug(
                    sprintf('Processed queue worker with priority %s, size of queue size is: %d', $this->priorityKey, sizeof($this->storage))
                );
            }

            // we maximal check the storage once a second
            sleep(1);
        }
    }
}
