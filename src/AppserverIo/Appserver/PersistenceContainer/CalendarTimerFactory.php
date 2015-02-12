<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\CalendarTimerFactory
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

use Rhumsaa\Uuid\Uuid;
use AppserverIo\Lang\Reflection\MethodInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;
use AppserverIo\Appserver\PersistenceContainer\Utils\TimerState;

/**
 * A thread which creates calendar timer instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CalendarTimerFactory extends \Thread implements CalendarTimerFactoryInterface
{

    /**
     * Initializes and the timer factory.
     */
    public function __construct()
    {

        // initialize the member variables
        $this->dispatched = false;
        $this->mutex = \Mutex::create();
    }

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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Create a calendar-based timer based on the input schedule expression.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface $timerService  The timer service to create the service for
     * @param \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression    $schedule      A schedule expression describing the timeouts for this timer
     * @param \Serializable                                          $info          Serializable info that will be made available through the newly created timers Timer::getInfo() method
     * @param boolean                                                $persistent    TRUE if the newly created timer has to be persistent
     * @param \AppserverIo\Lang\Reflection\MethodInterface           $timeoutMethod The timeout method to be invoked
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimerInterface The newly created Timer.
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     */
    public function createTimer(TimerServiceInterface $timerService, ScheduleExpression $schedule, \Serializable $info = null, $persistent = true, MethodInterface $timeoutMethod = null)
    {

        // lock the method
        \Mutex::lock($this->mutex);

        do {
            // create a counter
            $counter = 0;

            // if this is the first loop
            if ($counter === 0) {
                // we're not dispatched
                $this->dispatched = false;

                // initialize the data
                $this->info = $info;
                $this->schedule = $schedule;
                $this->persistent = $persistent;
                $this->timerService = $timerService;
                $this->timeoutMethod = $timeoutMethod;

                // notify the thread
                $this->synchronized(function ($self) {
                    $self->notify();
                }, $this);

            }

            // raise the counter
            $counter++;

            // we wait for 100 iterations
            if ($counter > 100) {
                throw new \Exception('Can\'t create timer');
            }

            // lower system load a bit
            usleep(100);

        } while ($this->dispatched === false);

        // unlock the method
        \Mutex::unlock($this->mutex);

        // return the created timer
        return $this->timer;
    }

    /**
     * (non-PHPdoc)
     * @see Stackable::run()
     */
    public function run()
    {

        // make the application available and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        while (true) {

            // wait until we've been notified
            $this->synchronized(function ($self) {
                $self->wait();
            }, $this);

            // create the timer
            $this->timer = CalendarTimer::builder()
                ->setAutoTimer($this->timeoutMethod != null)
                ->setScheduleExprSecond($this->schedule->getSecond())
                ->setScheduleExprMinute($this->schedule->getMinute())
                ->setScheduleExprHour($this->schedule->getHour())
                ->setScheduleExprDayOfWeek($this->schedule->getDayOfWeek())
                ->setScheduleExprDayOfMonth($this->schedule->getDayOfMonth())
                ->setScheduleExprMonth($this->schedule->getMonth())
                ->setScheduleExprYear($this->schedule->getYear())
                ->setScheduleExprStartDate(\DateTime::createFromFormat(ScheduleExpression::DATE_FORMAT, $this->schedule->getStart()))
                ->setScheduleExprEndDate(\DateTime::createFromFormat(ScheduleExpression::DATE_FORMAT, $this->schedule->getEnd()))
                ->setScheduleExprTimezone($this->schedule->getTimezone())
                ->setTimeoutMethod($this->timeoutMethod)
                ->setTimerState(TimerState::CREATED)
                ->setId(Uuid::uuid4()->__toString())
                ->setPersistent($this->persistent)
                ->setTimedObjectId($this->timerService->getTimedObjectInvoker()->getTimedObjectId())
                ->setInfo($this->info)
                ->setNewTimer(true)
                ->build($this->timerService);

            // we're dispatched now
            $this->dispatched = true;
        }
    }
}
