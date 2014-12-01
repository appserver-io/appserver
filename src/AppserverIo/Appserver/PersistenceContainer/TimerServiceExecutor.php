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
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerInterface;

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
     * Contains the scheduled timer tasks.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $scheduledTimerTasks;

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
     * Injects the storage for the scheduled timer tasks.
     *
     * @param \AppserverIo\Storage\StorageInterface $scheduledTimerTasks The storage for the scheduled timer tasks
     *
     * @return void
     */
    public function injectScheduledTimerTasks(StorageInterface $scheduledTimerTasks)
    {
        $this->scheduledTimerTasks = $scheduledTimerTasks;
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
     * Returns the scheduled timer tasks.
     *
     * @return \AppserverIo\Storage\StorageInterface A collection of scheduled timer tasks
     **/
    public function getScheduledTimerTasks()
    {
        return $this->scheduledTimerTasks;
    }

    /**
     * Adds the passed timer task to the schedule.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface $timer The timer we want to schedule
     *
     * @return void
     */
    protected function schedule(TimerInterface $timer)
    {

        // create a wrapper instance for the timer task that we want to schedule
        $timerTaskWrapper = new \stdClass();
        $timerTaskWrapper->executeAt = microtime(true) + ($timer->getTimeRemaining() / 1000000);
        $timerTaskWrapper->timer = $timer;

        // schedule the timer tasks as wrapper
        $this->scheduledTimerTasks[] = $timerTaskWrapper;

        // force handling the timer tasks now
        $this->synchronized(function ($self) {
            $self->notify();
        }, $this);
    }

    /**
     * Only wait for executing timer tasks.
     *
     * @return void
     */
    public function run()
    {

        // array with the timer tasks that are actually running
        $timerTasksExecuting = array();

        // make the list with the scheduled timer task wrappers available
        $scheduledTimerTasks = $this->getScheduledTimerTasks();

        // make the application available and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // try to load the profile logger
        if ($profileLogger = $application->getInitialContext()->getLogger(LoggerUtils::PROFILE)) {
            $profileLogger->appendThreadContext('timer-service-executor');
        }

        while (true) { // handle the timer events

            // wait 1 second or till we've been notified
            $this->synchronized(function ($self) {
                $self->wait(1000000);
            }, $this);

            // iterate over the scheduled timer tasks
            foreach ($scheduledTimerTasks as $key => $timerTaskWrapper) {

                if ($timerTaskWrapper instanceof \stdClass) { // make sure we've a wrapper found

                    // check if the task has to be executed now
                    if ($timerTaskWrapper->executeAt < microtime(true)) { // if yes, create the timer task and execute it
                        $timerTasksExecuting[] = $timerTaskWrapper->timer->getTimerTask($application);
                    }
                }
            }

            // remove the finished timer tasks
            foreach ($timerTasksExecuting as $key => $executingTimerTask) {
                if ($executingTimerTask->isFinished()) { // remove the task and wrapper from the list
                    unset ($timerTasksExecuting[$key]);
                    unset ($scheduledTimerTasks[$key]);
                    error_log("SUCCESSFULLY REMOVED scheduled timer task $key");
                }
            }

            if ($profileLogger) { // profile the size of the timer tasks to be executed
                $profileLogger->debug(
                    sprintf('Processed timer service executor, executing %d timer tasks', sizeof($timerTasksExecuting))
                );
            }
        }
    }
}
