<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\TimerServiceExecutor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\EnterpriseBeans\TimerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\PersistenceContainerProtocol\ServiceExecutor;

/**
 * The executor thread for the timers.
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
class TimerServiceExecutor extends \Thread implements ServiceExecutor
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
     * @param \AppserverIo\Appserver\Application\ApplicationInterface $application The application instance
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
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
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
        $this->synchronized(function ($self, $t) {

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
     * Only wait for executing timer tasks.
     *
     * @return void
     */
    public function run()
    {

        // register a shutdown function
        register_shutdown_function(array($this, 'shutdown'));

        // the array with the timer tasks that'll be executed actually
        $timerTasksExecuting = array();

        // make the application available and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext('timer-service-executor');
        }

        // handle the timer events
        while (true) {
            // wait 1 second or till we've been notified
            $this->synchronized(function ($self) {
                $self->wait(1000000);
            }, $this);

            try {
                // iterate over the timer tasks that has to be executed
                foreach ($this->tasksToExecute as $taskId => $timerTaskWrapper) {
                    // this should never happpen
                    if (!$timerTaskWrapper instanceof \stdClass) {
                        // log an error message because we task wrapper has wrong type
                        $this->getApplication()->getInitialContext()->getSystemLogger()->error(
                            sprintf('Timer-Task-Wrapper %s has wrong type %s', $taskId, get_class($timerTaskWrapper))
                        );

                        continue;
                    }

                    // query if the task has to be executed now
                    if ($timerTaskWrapper->executeAt < microtime(true)) {
                        // load the timer task wrapper we want to execute
                        if ($pk = $this->scheduledTimers[$timerId = $timerTaskWrapper->timerId]) {
                            // load the timer service registry
                            $timerServiceRegistry = $this->getApplication()->search('TimerServiceContext');

                            // load the timer from the timer service
                            $timer = $timerServiceRegistry->locate($pk)->getTimers()->get($timerId);

                            // create the timer task to be executed
                            $timerTasksExecuting[$taskId] = $timer->getTimerTask($application);

                            // remove the key from the list ot tasks to be executed
                            unset($this->tasksToExecute[$taskId]);

                        } else {
                            // log an error message because we can't find the timer instance
                            $this->getApplication()->getInitialContext()->getSystemLogger()->error(
                                sprintf('Can\'t find timer %s to create timer task %s', $timerTaskWrapper->timerId, $taskId)
                            );
                        }
                    }
                }

                // remove the finished timer tasks
                foreach ($timerTasksExecuting as $taskId => $executingTimerTask) {
                    // query, whether the timer has finished
                    if ($executingTimerTask->isFinished()) {
                        // remove the finished timer task from the list
                        unset($timerTasksExecuting[$taskId]);
                    }
                }

                // profile the size of the timer tasks to be executed
                if ($profileLogger) {
                    $profileLogger->debug(
                        sprintf('Processed timer service executor, executing %d timer tasks', sizeof($timerTasksExecuting))
                    );
                }

            } catch (\Exception $e) {
                // log a critical error message
                $this->getApplication()->getInitialContext()->getSystemLogger()->critical($e->__toString());
            }
        }
    }

    /**
     * Shutdown method that will be invoked when the timer service executor
     * stopped unexpected, by a fatal error or a exception for example.
     *
     * @return void
     */
    public function shutdown()
    {
        $this->getApplication()->getInitialContext()->getSystemLogger()->critical(
            'Timer-Service-Executor stopped unexpected, please contact system administrator immediately!'
        );
    }
}
