<?php
/**
 * AppserverIo\Appserver\PersistenceContainer\CalendarTimer
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

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;
use AppserverIo\Appserver\PersistenceContainer\Tasks\CalendarTimerTask;

/**
 * Represents a timer which is created out a calendar expression.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CalendarTimer extends Timer
{

    /**
     * The schedule expression corresponding to this timer.
     *
     * @var \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression
     */
    protected $calendarTimeout;

    /**
     * Represents whether this is an auto-timer or a normal programmatically created timer.
     *
     * @var boolean
     */
    protected $autoTimer = false;

    /**
     * The timeout method called if we've an auto-timer.
     *
     * \AppserverIo\Lang\Reflection\MethodInterface
     */
    protected $timeoutMethod;

    /**
     * Initializes the timer with the necessary data.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder $builder      The builder with the data to create the timer from
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface           $timerService The timer service instance
     */
    public function __construct(CalendarTimerBuilder $builder, TimerServiceInterface $timerService)
    {

        // call parent constructor
        parent::__construct($builder, $timerService);

        // load the auto timer flag
        $this->autoTimer = $builder->isAutoTimer();

        // check if we've an auto timer
        if ($this->autoTimer) {
            // check if the timeout method is available
            if ($builder->getTimeoutMethod() == null) {
                // TODO: Throw an exception here, because we NEED a timeout method
            }

            // if yes, set it
            $this->timeoutMethod = $builder->getTimeoutMethod();

        } else {
            // we don't expect an timeout method here
            if ($builder->getTimeoutMethod() != null) {
                // TODO: Throw an exception here, because we DON'T expect a timeout method
            }

            // reset the timeout method
            $this->timeoutMethod = null;
        }

        // create the schedule for this timer
        $scheduledExpression = new ScheduleExpression();
        $scheduledExpression->second($builder->getScheduleExprSecond());
        $scheduledExpression->minute($builder->getScheduleExprMinute());
        $scheduledExpression->hour($builder->getScheduleExprHour());
        $scheduledExpression->dayOfWeek($builder->getScheduleExprDayOfWeek());
        $scheduledExpression->dayOfMonth($builder->getScheduleExprDayOfMonth());
        $scheduledExpression->month($builder->getScheduleExprMonth());
        $scheduledExpression->year($builder->getScheduleExprYear());
        $scheduledExpression->start($builder->getScheduleExprStartDate());
        $scheduledExpression->end($builder->getScheduleExprEndDate());
        $scheduledExpression->timezone($builder->getScheduleExprTimezone());

        // create the calender timeout from the schedule
        $this->calendarTimeout = CalendarBasedTimeout::factoryFromScheduleExpression($scheduledExpression);

        // if the timer has a next date and is new
        if ($builder->getNextDate() == null && $builder->isNewTimer()) {
            // compute the next timeout (from "now")
            $nextTimeout = $this->calendarTimeout->getNextRunDate();
            if ($nextTimeout != null) {
                $this->nextExpiration = $nextTimeout->format($scheduledExpression::DATE_FORMAT);
            }
        }
    }

    /**
     * Returns the calendar base timeout instance.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarBasedTimeout The calendar timeout instance
     */
    public function getCalendarTimeout()
    {
        return $this->calendarTimeout;
    }

    /**
     * Get the schedule expression corresponding to this timer.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression
     * @throws \AppserverIo\Lang\IllegalStateException If this method is invoked while the instance is in a state that does not allow access to this method
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     */
    public function getSchedule()
    {
        return $this->getCalendarTimeout()->getScheduleExpression();
    }

    /**
     * Query whether this timer is a calendar-based timer.
     *
     * @return boolean True if this timer is a calendar-based timer.
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     */
    public function isCalendarTimer()
    {
        return true;
    }

    /**
     * Query whether this is an auto-timer or a normal programmatically created timer.
     *
     * @return boolean TRUE if this timer is a auto-timer, else FALSE
     */
    public function isAutoTimer()
    {
        return $this->autoTimer;
    }

    /**
     * Returns the thimeout method called if we've an auto-timer.
     *
     * @return \AppserverIo\Lang\Reflection\MethodInterface|null The timeout method
     */
    public function getTimeoutMethod()
    {
        return $this->timeoutMethod;
    }

    /**
     * Returns the task which handles the timeouts of this timer.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Tasks\CalendarTimerTask The task
     */
    public function getTimerTask(ApplicationInterface $application)
    {
        return new CalendarTimerTask($this, $application);
    }

    /**
     * Instanciates a new builder that creates a timer instance.
     *
     *  @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The builder instance
     */
    public static function builder()
    {
        return new CalendarTimerBuilder();
    }
}
