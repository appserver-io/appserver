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

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Naming\InitialContext;
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
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Appserver\Core\Utilities\EnumState;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Appserver\Core\Utilities\LoggerUtils;

/**
 * A message queue worker implementation listening to a queue, defined in the passed application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface                 $application     The application instance with the queue manager/locator
 * @property \AppserverIo\Storage\GenericStackable                             $jobsToExecute   The storage for the jobs to be executed
 * @property \AppserverIo\Storage\GenericStackable                             $messages        The storage for the messages
 * @property \AppserverIo\Psr\Pms\PriorityKeyInterface                         $priorityKey     The priority of this queue worker
 * @property \AppserverIo\Appserver\MessageQueue\QueueManagerSettingsInterface $managerSettings The queue settings
 */
class QueueWorker extends AbstractDaemonThread
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
     * Injects the queue manager settings.
     *
     * @param \AppserverIo\Appserver\MessageQueue\QueueManagerSettingsInterface $managerSettings The queue manager settings
     *
     * @return void
     */
    public function injectManagerSettings(QueueManagerSettingsInterface $managerSettings)
    {
        $this->managerSettings = $managerSettings;
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
     * Return's the queue manager settings.
     *
     * @return \AppserverIo\Appserver\MessageQueue\QueueManagerSettingsInterface The queue manager settings
     */
    public function getManagerSettings()
    {
        return $this->managerSettings;
    }

    /**
     * Returns the default timeout.
     *
     * Reduce CPU load depending on the queues priority, whereas priority
     * can be 1, 2 or 3 actually, so possible values for usleep are:
     *
     * PriorityHigh:         100 === 0.0001 s
     * PriorityMedium:    10.000 === 0.01 s
     * PriorityLow:    1.000.000 === 1 s
     *
     * @return integer The default timeout in microseconds
     * @see \AppserverIo\Appserver\Core\AbstractDaemonThread::getDefaultTimeout()
     */
    public function getQueueTimeout()
    {
        return pow(10, $this->priorityKey->getPriority() * 2);
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
     * This is a very basic method to log some stuff by using the error_log() method of PHP.
     *
     * @param mixed  $level   The log level to use
     * @param string $message The message we want to log
     * @param array  $context The context we of the message
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        LoggerUtils::log($level, $message, $context);
    }

    /**
     * This method will be invoked before the while() loop starts and can be used
     * to implement some bootstrap functionality.
     *
     * @return void
     */
    public function bootstrap()
    {

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // synchronize the application instance and register the class loaders
        $application = $this->application;
        $application->registerClassLoaders();

        // add the application instance to the environment
        Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

        // create s simulated request/session ID whereas session equals request ID
        Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
        Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

        // try to load the profile logger
        if ($this->profileLogger = $application->getInitialContext()->getLogger(\AppserverIo\Logger\LoggerUtils::PROFILE)) {
            $this->profileLogger->appendThreadContext(sprintf('queue-worker-%s', $this->priorityKey));
        }
    }

    /**
     * We process the messages/jobs here.
     *
     * @return void
     */
    public function run()
    {

        try {
            // register shutdown handler
            register_shutdown_function($this->getDefaultShutdownMethod());

            // bootstrap the daemon
            $this->bootstrap();

            // synchronize the application instance and register the class loaders
            $application = $this->application;

            // mark the daemon as successfully shutdown
            $this->synchronized(function ($self) {
                $self->state = EnumState::get(EnumState::RUNNING);
            }, $this);

            // create local instances of the storages
            $messages = $this->messages;
            $priorityKey = $this->priorityKey;
            $jobsToExecute = $this->jobsToExecute;

            // load the maximum number of jobs to process in parallel
            $maximumJobsToProcess = $this->getManagerSettings()->getMaximumJobsToProcess();

            // initialize the arrays for the message states and the jobs executing
            $jobsExecuting = array();
            $messagesFailed = array();
            $retryCounter = array();
            $callbacksToExecute = array();

            // keep the daemon running
            while ($this->keepRunning()) {
                // iterate over all job wrappers
                foreach ($jobsToExecute as $jobWrapper) {
                    try {
                        // load the message
                        $message = $messages[$jobWrapper->jobId];

                        // check if we've a message found
                        if ($message instanceof MessageInterface) {
                            // check the message state
                            switch ($message->getState()->getState()) {

                                // message is active and ready to be processed
                                case StateActive::KEY:

                                    // set the new state now
                                    $message->setState(StateToProcess::get());

                                    break;

                                // message is paused or in progress
                                case StatePaused::KEY:

                                    // invoke the callbacks for the state
                                    if ($message->hasCallbacks($message->getState())) {
                                        $callbacksToExecute[] = new Callback(clone $message, $application);
                                    }

                                    // log a message that we've a message that has been paused
                                    \info(sprintf('Message %s has been paused', $message->getMessageId()));

                                    break;

                                case StateInProgress::KEY:

                                    // query whether or not the job is still available
                                    if (isset($jobsExecuting[$message->getMessageId()])) {
                                        // make sure the job has been finished
                                        if ($jobsExecuting[$message->getMessageId()] instanceof JobInterface && $jobsExecuting[$message->getMessageId()]->isFinished()) {
                                            // log a message that the job is still in progress
                                            \info(sprintf('Job %s has been finished', $message->getMessageId()));

                                            // set the new state now
                                            $message->setState($jobsExecuting[$message->getMessageId()]->getMessage()->getState());

                                        } else {
                                            // log a message that the job is still in progress
                                            \info(sprintf('Job %s is still in progress', $message->getMessageId()));
                                        }

                                    } else {
                                        // log a message that the job is still in progress
                                        \critical(sprintf('Message %s has been deleted, but should still be there', $message->getMessageId()));
                                    }

                                    break;

                                // message failed
                                case StateFailed::KEY:

                                    // remove the old job from the queue
                                    unset($jobsExecuting[$message->getMessageId()]);

                                    // query whether or not the message has to be processed again
                                    if (isset($messagesFailed[$message->getMessageId()]) && $message->getRetryCounter() > 0) {
                                        // query whether or not the message has to be processed now
                                        if ($messagesFailed[$message->getMessageId()] < time() && $retryCounter[$message->getMessageId()] < $message->getRetryCounter()) {
                                            // retry to process the message
                                            $message->setState(StateToProcess::get());

                                            // update the execution time and raise the retry counter
                                            $messagesFailed[$message->getMessageId()] = time() + $message->getRetryTimeout($retryCounter[$message->getMessageId()]);
                                            $retryCounter[$message->getMessageId()]++;

                                        } elseif ($messagesFailed[$message->getMessageId()] < time() && $retryCounter[$message->getMessageId()] === $message->getRetryCounter()) {
                                            // log a message that we've a message with a unknown state
                                            \critical(sprintf('Message %s finally failed after %d retries', $message->getMessageId(), $retryCounter[$message->getMessageId()]));

                                            // stop executing the job because we've reached the maximum number of retries
                                            unset($jobsToExecute[$messageId = $message->getMessageId()]);
                                            unset($messagesFailed[$messageId]);
                                            unset($retryCounter[$messageId]);

                                            // invoke the callbacks for the state
                                            if ($message->hasCallbacks($message->getState())) {
                                                $callbacksToExecute[] = new Callback(clone $message, $application);
                                            }

                                        } else {
                                            // wait for the next try here
                                        }

                                    } elseif (!isset($messagesFailed[$message->getMessageId()]) && $message->getRetryCounter() > 0) {
                                        // first retry, so we've to initialize the next execution time and the retry counter
                                        $retryCounter[$message->getMessageId()] = 0;
                                        $messagesFailed[$message->getMessageId()] = time() + $message->getRetryTimeout($retryCounter[$message->getMessageId()]);

                                    } else {
                                        // log a message that we've a message with a unknown state
                                        \critical(sprintf('Message %s failed with NO retries', $message->getMessageId()));

                                        // stop executing the job because we've reached the maximum number of retries
                                        unset($jobsToExecute[$messageId = $message->getMessageId()]);

                                        // invoke the callbacks for the state
                                        if ($message->hasCallbacks($message->getState())) {
                                            $callbacksToExecute[] = new Callback(clone $message, $application);
                                        }
                                    }

                                    break;

                                case StateToProcess::KEY:

                                    // count messages in queue
                                    $inQueue = sizeof($jobsExecuting);

                                    // we only process 200 jobs in parallel
                                    if ($inQueue < $maximumJobsToProcess) {
                                        // set the new message state now
                                        $message->setState(StateInProgress::get());

                                        // start the job and add it to the internal array
                                        $jobsExecuting[$message->getMessageId()] = new Job(clone $message, $application);

                                    } else {
                                        // log a message that queue is actually full
                                        \info(sprintf('Job queue full - (%d jobs/%d msg wait)', $inQueue, sizeof($messages)));

                                        // if the job queue is full, restart iteration to remove processed jobs from queue first
                                        continue 2;
                                    }

                                    break;

                                // message processing has been successfully processed
                                case StateProcessed::KEY:

                                    // invoke the callbacks for the state
                                    if ($message->hasCallbacks($message->getState())) {
                                        $callbacksToExecute[] = new Callback(clone $message, $application);
                                    }

                                    // remove the job from the queue with jobs that has to be executed
                                    unset($jobsToExecute[$messageId = $message->getMessageId()]);

                                    // also remove the job + the message from the queue
                                    unset($jobsExecuting[$messageId]);
                                    unset($messages[$messageId]);

                                    break;

                                // message is in an unknown state -> this is weired and should never happen!
                                case StateUnknown::KEY:

                                    // log a message that we've a message with a unknown state
                                    \critical(sprintf('Message %s has state %s', $message->getMessageId(), $message->getState()));

                                    // set new state now
                                    $message->setState(StateFailed::get());

                                    break;

                                // we don't know the message state -> this is weired and should never happen!
                                default:

                                    // set the failed message state
                                    $message->setState(StateFailed::get());

                                    // log a message that we've a message with an invalid state
                                    \critical(sprintf('Message %s has an invalid state', $message->getMessageId()));

                                    break;
                            }
                        }

                        // add the message back to the stack (because we've a copy here)
                        if (isset($messages[$message->getMessageId()])) {
                            $messages[$jobWrapper->jobId] = $message;
                        }

                        // catch all exceptions
                    } catch (\Exception $e) {
                        $application->getInitialContext()->getSystemLogger()->critical($e->__toString());
                    }

                    // reduce CPU load depending on queue priority
                    $this->iterate($this->getQueueTimeout());
                }

                // reduce CPU load after each iteration
                $this->iterate($this->getDefaultTimeout());

                // profile the size of the session pool
                if ($this->profileLogger) {
                    $this->profileLogger->debug(
                        sprintf(
                            'Processed queue worker with priority %s, size of queue size is: %d',
                            $priorityKey,
                            sizeof($jobsToExecute)
                        )
                    );
                }
            }

            // clean up the instances and free memory
            $this->cleanUp();

            // mark the daemon as successfully shutdown
            $this->synchronized(function ($self) {
                $self->state = EnumState::get(EnumState::SHUTDOWN);
            }, $this);

        } catch (\Exception $e) {
            \error($e->__toString());
        }
    }
}
