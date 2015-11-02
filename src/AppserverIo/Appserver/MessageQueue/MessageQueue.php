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
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Messaging\Utils\StateActive;
use AppserverIo\Messaging\Utils\PriorityKeys;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use Psr\Log\LogLevel;

/**
 * A message queue wrapper implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property string                                                     $name            The queue name to use
 * @property string                                                     $type            The message bean type that has to handle the messages
 * @property \AppserverIo\Psr\Application\ApplicationInterface          $application     The application to manage queues for
 * @property \AppserverIo\Storage\GenericStackable                      $workers         The storage for the workers
 * @property \AppserverIo\Storage\GenericStackable                      $messages        The storage for the messages
 * @property \AppserverIo\Appserver\MessageQueue\QueueSettingsInterface $queueSettings   The queue settings
 */
class MessageQueue extends AbstractDaemonThread implements QueueInterface
{

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
     * Injects the queue settings.
     *
     * @param \AppserverIo\Appserver\MessageQueue\QueueSettingsInterface $queueSettings The queue settings
     *
     * @return void
     */
    public function injectQueueSettings(QueueSettingsInterface $queueSettings)
    {
        $this->queueSettings = $queueSettings;
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
     * Return's the queue settings.
     *
     * @return \AppserverIo\Appserver\MessageQueue\QueueSettingsInterface The queue settings
     */
    public function getQueueSettings()
    {
        return $this->queueSettings;
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
        $this->getApplication()->getInitialContext()->getSystemLogger()->log($level, $message, $context);
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

        // create a reference to the workers/messages
        $workers = $this->workers;
        $messages = $this->messages;
        $queueSettings = $this->queueSettings;

        // try to load the profile logger
        if ($this->profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $this->profileLogger->appendThreadContext(sprintf('message-queue-%s', $this->getName()));
        }

        // prepare the storages
        $jobsToExceute = array();

        // initialize the counter for the storages
        $counter = 0;

        // create a separate queue for each priority
        foreach (PriorityKeys::getAll() as $priorityKey) {
            // create the containers for the worker
            $jobsToExceute[$counter] = new GenericStackable();

            // initialize and start the queue worker
            $queueWorker = new QueueWorker();
            $queueWorker->injectMessages($messages);
            $queueWorker->injectApplication($application);
            $queueWorker->injectPriorityKey($priorityKey);
            $queueWorker->injectQueueSettings($queueSettings);

            // attach the storages
            $queueWorker->injectJobsToExecute($jobsToExceute[$counter]);

            // start the worker instance
            $queueWorker->start();

            // add the queue instance to the module
            $workers[$this->uniqueWorkerName($priorityKey)] = $queueWorker;

            // raise the counter
            $counter++;
        }
    }

    /**
     * This method will be invoked, after the while() loop has been finished and
     * can be used to implement clean up functionality.
     *
     * @return void
     */
    public function cleanUp()
    {
        // create a separate queue for each priority
        foreach (PriorityKeys::getAll() as $priorityKey) {
            $this->workers[$this->uniqueWorkerName($priorityKey)]->stop();
        }
    }
}
