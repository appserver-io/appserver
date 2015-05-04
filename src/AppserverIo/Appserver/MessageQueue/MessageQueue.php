<?php

/**
 * \AppserverIo\Appserver\MessageQueue\MessageQueue
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
use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Psr\Pms\QueueInterface;
use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Psr\Pms\PriorityKeyInterface;
use AppserverIo\Messaging\Utils\StateActive;
use AppserverIo\Messaging\Utils\PriorityKeys;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A message queue wrapper implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageQueue extends \Thread implements QueueInterface
{

    /**
     * Timeout to sleep while waiting for messages.
     *
     * @var integer
     */
    const TTL = 1000000;

    /**
     * Initializes the message queue with the necessary data.
     */
    public function __construct()
    {

        // initialize the flags for start/stop handling
        $this->run = true;
        $this->running = false;
    }

    /**
     * Initializes the queue with the name to use.
     *
     * @param string $name Holds the queue name to use
     *
     * @return void
     */
    public function injectName($name)
    {
        $this->name = $name;
    }

    /**
     * Initializes the queue with the message bean type that has to handle the messages.
     *
     * @param string $type The message bean type to handle the messages
     *
     * @return void
     */
    public function injectType($type)
    {
        $this->type = $type;
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
     * Injects the storage for the messages.
     *
     * @param \AppserverIo\Storage\GenericStackable $messages An storage for the messages
     *
     * @return void
     */
    public function injectMessages(GenericStackable $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Injects the storage for the workers.
     *
     * @param \AppserverIo\Storage\GenericStackable $workers An storage for the workers
     *
     * @return void
     */
    public function injectWorkers(GenericStackable $workers)
    {
        $this->workers = $workers;
    }

    /**
     * Returns the queue name.
     *
     * @return string The queue name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the message bean type to handle the messages.
     *
     * @return string The message bean type to handle the messages
     */
    public function getType()
    {
        return $this->type;
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
     * Returns the storage for the messages.
     *
     * @return \AppserverIo\Storage\GenericStackable The storage for the messages
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Returns the storage for the workers.
     *
     * @return \AppserverIo\Storage\GenericStackable The storage for the workers
     */
    public function getWorkers()
    {
        return $this->workers;
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
        $this->synchronized(function (MessageQueue $self, MessageInterface $m) {
            // create a unique identifier for the priority
            $priority = $this->uniqueWorkerName($m->getPriority());

            // load the worker for the message's priority
            if (isset($self->workers[$priority])) {
                // attach the message
                $self->messages[$m->getMessageId()] = $m;

                // store the job-ID and the PK of the message => necessary to load the message later
                $jobWrapper = new \stdClass();
                $jobWrapper->jobId = $m->getMessageId();
                $jobWrapper->messageId = $m->getMessageId();

                // attach the job to the worker
                $self->workers[$priority]->attach($jobWrapper);
            }

        }, $this, $message);
    }

    /**
     * Stops the message queues workers and the message queue itself.
     *
     * @return void
     */
    public function stop()
    {

        // stop all workers
        foreach ($this->workers as $worker) {
            $worker->stop();
        }

        // stop the message queue itself
        $this->run = false;
    }

    /**
     * Creates a unique name to register the worker with the passed priority.
     *
     * @param \AppserverIo\Psr\Pms\PriorityKeyInterface $priorityKey The priority key to create a unique name for
     *
     * @return string The unique name
     */
    protected function uniqueWorkerName(PriorityKeyInterface $priorityKey)
    {
        return sprintf('%s-%s', $this->getName(), $priorityKey);
    }

    /**
     * We process the messages/jobs here.
     *
     * @return void
     */
    public function run()
    {

        // create a local instance of application and storage
        $application = $this->application;

        // register the class loader again, because each thread has its own context
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext(sprintf('message-queue-%s', $this->getName()));
        }

        // create a reference to the workers/messages
        $workers = $this->workers;
        $messages = $this->messages;

        // prepare the storages
        $jobsExecuting = array();
        $jobsToExceute = array();
        $messageStates = array();

        // initialize the counter for the storages
        $counter = 0;

        // create a separate queue for each priority
        foreach (PriorityKeys::getAll() as $priorityKey) {
            // create the containers for the worker
            $jobsExecuting[$counter] = new GenericStackable();
            $jobsToExceute[$counter] = new GenericStackable();
            $messageStates[$counter] = new GenericStackable();

            // initialize and start the queue worker
            $queueWorker = new QueueWorker();
            $queueWorker->injectMessages($messages);
            $queueWorker->injectPriorityKey($priorityKey);
            $queueWorker->injectApplication($application);

            // attach the storages
            $queueWorker->injectJobsExecuting($jobsExecuting[$counter]);
            $queueWorker->injectJobsToExecute($jobsToExceute[$counter]);
            $queueWorker->injectMessageStates($messageStates[$counter]);

            // start the worker instance
            $queueWorker->start();

            // add the queue instance to the module
            $workers[$this->uniqueWorkerName($priorityKey)] = $queueWorker;

            // raise the counter
            $counter++;
        }

        // set to TRUE, because message queue is running
        $this->running = true;

        // query whether we keep running
        while ($this->run) {
            // wait for the configured timeout
            $this->wait(MessageQueue::TTL);

            // profile the message queue
            if ($profileLogger) {
                $profileLogger->debug(sprintf('Process message queue %s', $this->getName()));
            }
        }

        // set to FALSE, because message queue has been stopped
        $this->running = false;
    }
}
