<?php

/**
 * AppserverIo\Appserver\MessageQueue\Job
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

use AppserverIo\Psr\Pms\JobInterface;
use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Messaging\Utils\StateProcessed;
use AppserverIo\Messaging\Utils\StateInProgress;
use AppserverIo\Appserver\Naming\InitialContext;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A simple job implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Job extends \Thread implements JobInterface
{

    /**
     * The message we have to handle.
     *
     * @var \AppserverIo\Psr\Pms\MessageInterface
     */
    protected $message;

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Whether the job has been finished or not.
     *
     * @var boolean
     */
    protected $finished;

    /**
     * Initializes the job with the application and the storage it should work on.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface             $message     The message we have to handle
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(MessageInterface $message, ApplicationInterface $application)
    {

        // we want to start working
        $this->finished = false;

        // initialize message and application instance
        $this->message = $message;
        $this->application = $application;

        // start the job
        $this->start();
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the message instance the job is bound to.
     *
     * @return \AppserverIo\Psr\Pms\MessageInterface The message instance
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Queries whether the timer task has been finished or not.
     *
     * @return boolean TRUE if the timer task has been finished, else FALSE
     */
    public function isFinished()
    {
        return $this->finished;
    }

    /**
     * We process the timer here.
     *
     * @return void
     */
    public function run()
    {

        try {
            // register shutdown handler
            register_shutdown_function(array(&$this, "shutdown"));

            // load application and message instance
            $application = $this->application;
            $message = $this->message;

            // we need to register the class loaders again
            $application->registerClassLoaders();

            // load class name and session ID from remote method
            $queueProxy = $message->getDestination();
            $sessionId = $message->getSessionId();

            // lookup the queue and process the message
            if ($queue = $application->search('QueueContextInterface')->locate($queueProxy)) {
                // the queues receiver type
                $queueType = $queue->getType();

                // create an intial context instance
                $initialContext = new InitialContext();
                $initialContext->injectApplication($application);

                // lookup the bean instance
                $instance = $initialContext->lookup($queueType);

                // inject the application to the receiver and process the message
                $instance->onMessage($message, $sessionId);
            }

            // mark the job finished
            $this->finished = true;

        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->error($e->__toString());
        }
    }

    /**
     * Does shutdown logic for worker if something breaks in process and
     * marks the job as finished.
     *
     * @return void
     */
    public function shutdown()
    {
        $this->finished = true;
    }
}
