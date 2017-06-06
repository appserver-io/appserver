<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\TimerServiceExecutor
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerInterface;
use AppserverIo\Psr\EnterpriseBeans\ServiceExecutorInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceContextInterface;

/**
 * The executor thread for the timers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TimerServiceExecutor extends AbstractDaemonThread implements ServiceExecutorInterface
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Contains the scheduled timers.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $scheduledTimers;

    /**
     * Contains the ID's of the tasks to be executed.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $tasksToExecute;

    /**
     * Injects the application instance.
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
     * Injects the storage for the scheduled timers.
     *
     * @param \AppserverIo\Storage\GenericStackable $scheduledTimers The storage for the scheduled timers
     *
     * @return void
     */
    public function injectScheduledTimers(GenericStackable $scheduledTimers)
    {
        $this->scheduledTimers = $scheduledTimers;
    }

    /**
     * Injects the storage for the ID's of the tasks to be executed.
     *
     * @param \AppserverIo\Storage\GenericStackable $tasksToExecute The storage for the ID's of the tasks to be executed
     *
     * @return void
     */
    public function injectTasksToExecute(GenericStackable $tasksToExecute)
    {
        $this->tasksToExecute = $tasksToExecute;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the scheduled timers.
     *
     * @return \AppserverIo\Storage\GenericStackable A collection of scheduled timers
     **/
    public function getScheduledTimers()
    {
        return $this->scheduledTimers;
    }

    /**
     * Returns the storage of the ID's of the tasks to be executed.
     *
     * @return \AppserverIo\Storage\GenericStackable The storage for the ID's of the tasks to be executed
     **/
    public function getTasksToExecute()
    {
        return $this->tasksToExecute;
    }

    /**
     * Adds the passed timer task to the schedule.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface $timer The timer we want to schedule
     *
     * @return void
     */
    public function schedule(TimerInterface $timer)
    {

        // force handling the timer tasks now
        $this->synchronized(function (TimerServiceExecutor $self, TimerInterface $t) {

            // store the timer-ID and the PK of the timer service => necessary to load the timer later
            $self->scheduledTimers[$timerId = $t->getId()] = $t->getTimerService()->getPrimaryKey();

            // create a wrapper instance for the timer task that we want to schedule
            $timerTaskWrapper = new \stdClass();
            $timerTaskWrapper->executeAt = microtime(true) + ($t->getTimeRemaining() / 1000000);
            $timerTaskWrapper->taskId = uniqid();
            $timerTaskWrapper->timerId = $timerId;

            // schedule the timer tasks as wrapper
            $self->tasksToExecute[$timerTaskWrapper->taskId] = $timerTaskWrapper;

            // notify the thread to execute the timers
            $self->notify();

        }, $this, $timer);
    }

    /**
     * This method will be invoked before the while() loop starts and can be used
     * to implement some bootstrap functionality.
     *
     * @return void
     */
    public function bootstrap()
    {

        // setup autoloader
        require SERVER_AUTOLOADER;

        // make the application available and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if (isset($this->loggers[LoggerUtils::PROFILE])) {
            $this->profileLogger = $this->loggers[LoggerUtils::PROFILE];
            $this->profileLogger->appendThreadContext('timer-service-executor');
        }
    }

    /**
     * This is invoked on every iteration of the daemons while() loop.
     *
     * @param integer $timeout The timeout before the daemon wakes up
     *
     * @return void
     */
    public function iterate($timeout)
    {

        // call parent method and sleep for the default timeout
        parent::iterate($timeout);

        // iterate over the timer tasks that has to be executed
        foreach ($this->tasksToExecute as $taskId => $timerTaskWrapper) {
            // this should never happen
            if (!$timerTaskWrapper instanceof \stdClass) {
                // log an error message because we task wrapper has wrong type
                $this->getApplication()->getInitialContext()->getSystemLogger()->error(
                    sprintf('Timer-Task-Wrapper %s has wrong type %s', $taskId, get_class($timerTaskWrapper))
                );
                // we didn't foud a timer task ignore this
                continue;
            }

            // query if the task has to be executed now
            if ($timerTaskWrapper->executeAt < microtime(true)) {
                // load the timer task wrapper we want to execute
                if ($pk = $this->scheduledTimers[$timerId = $timerTaskWrapper->timerId]) {
                    // load the timer service registry
                    $timerServiceRegistry = $this->getApplication()->search(TimerServiceContextInterface::IDENTIFIER);

                    // lookup the timer from the timer service
                    $timer = $timerServiceRegistry->lookup($pk)->getTimers()->get($timerId);

                    // create the timer task to be executed
                    $timer->getTimerTask($this->getApplication());

                    // remove the key from the list of tasks to be executed
                    unset($this->tasksToExecute[$taskId]);

                } else {
                    // log an error message because we can't find the timer instance
                    $this->getApplication()->getInitialContext()->getSystemLogger()->error(
                        sprintf('Can\'t find timer %s to create timer task %s', $timerTaskWrapper->timerId, $taskId)
                    );
                }
            }
        }

        // profile the size of the timer tasks to be executed
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Processed timer service executor, executing %d timer tasks', sizeof($this->tasksToExecute))
            );
        }
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
     * Let the daemon sleep for the passed value of miroseconds.
     *
     * @param integer $timeout The number of microseconds to sleep
     *
     * @return void
     */
    public function sleep($timeout)
    {
        $this->synchronized(function ($self) use ($timeout) {
            $self->wait($timeout);
        }, $this);
    }
}
