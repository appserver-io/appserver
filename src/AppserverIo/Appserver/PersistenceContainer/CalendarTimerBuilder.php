<?php
/**
 * \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder
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

use AppserverIo\Lang\Reflection\MethodInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;

/**
 * A build class that creates a calendar timer instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CalendarTimerBuilder extends TimerBuilder
{

    /**
     * The schedule expression for the second.
     *
     * @var string
     */
    protected $scheduleExprSecond;

    /**
     * The schedule expression for the minute.
     *
     * @var string
     */
    protected $scheduleExprMinute;

    /**
     * The schedule expression for the hour.
     *
     * @var string
     */
    protected $scheduleExprHour;

    /**
     * The schedule expression for the day of week.
     *
     * @var string
     */
    protected $scheduleExprDayOfWeek;

    /**
     * The schedule expression for the day of month.
     *
     * @var string
     */
    protected $scheduleExprDayOfMonth;

    /**
     * The schedule expression for the month.
     *
     * @var string
     */
    protected $scheduleExprMonth;

    /**
     * The schedule expression for the year.
     *
     * @var string
     */
    protected $scheduleExprYear;

    /**
     * The date time for the calendar timer start date.
     *
     * @var string
     */
    protected $scheduleExprStartDate;

    /**
     * The date time for the calendar timer end date.
     *
     * @var string
     */
    protected $scheduleExprEndDate;

    /**
     * The timezone for the calendar timer.
     *
     * @var string
     */
    protected $scheduleExprTimezone;

    /**
     * Whether the calendar timer will be a auto timer.
     *
     * @var boolean
     */
    protected $autoTimer;

    /**
     * The timeout method instance.
     *
     * @var \AppserverIo\Lang\Reflection\MethodInterface
     */
    protected $timeoutMethod;

    /**
     * Sets the second expression for the calendar timer.
     *
     * @param string $scheduleExprSecond The second expression
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprSecond($scheduleExprSecond)
    {
        $this->scheduleExprSecond = $scheduleExprSecond;
        return $this;
    }

    /**
     * Sets the minute expression for the calendar timer.
     *
     * @param string $scheduleExprMinute The minute expression
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprMinute($scheduleExprMinute)
    {
        $this->scheduleExprMinute = $scheduleExprMinute;
        return $this;
    }

    /**
     * Sets the hour expression for the calendar timer.
     *
     * @param string $scheduleExprHour The hour expression
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprHour($scheduleExprHour)
    {
        $this->scheduleExprHour = $scheduleExprHour;
        return $this;
    }

    /**
     * Returns the day of week expression for the calendar timer.
     *
     * @param string $scheduleExprDayOfWeek The day of week expression
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprDayOfWeek($scheduleExprDayOfWeek)
    {
        $this->scheduleExprDayOfWeek = $scheduleExprDayOfWeek;
        return $this;
    }

    /**
     * Sets the day of month expression for the calendar timer.
     *
     * @param string $scheduleExprDayOfMonth The day of month expression
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprDayOfMonth($scheduleExprDayOfMonth)
    {
        $this->scheduleExprDayOfMonth = $scheduleExprDayOfMonth;
        return $this;
    }

    /**
     * Sets the month expression for the calendar timer.
     *
     * @param string $scheduleExprMonth The month expression
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprMonth($scheduleExprMonth)
    {
        $this->scheduleExprMonth = $scheduleExprMonth;
        return $this;
    }

    /**
     * Sets the year expression for the calendar timer.
     *
     * @param string $scheduleExprYear The year exrpession
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprYear($scheduleExprYear)
    {
        $this->scheduleExprYear = $scheduleExprYear;
        return $this;
    }

    /**
     * Sets the date time for the calendar end date.
     *
     * @param string $scheduleExprStartDate The start date
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprStartDate($scheduleExprStartDate)
    {
        $this->scheduleExprStartDate = $scheduleExprStartDate;
        return $this;
    }

    /**
     * Sets the date time for the calendar end date.
     *
     * @param string $scheduleExprEndDate The end date
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprEndDate($scheduleExprEndDate)
    {
        $this->scheduleExprEndDate = $scheduleExprEndDate;
        return $this;
    }

    /**
     * Sets the timezone for the calendar timer.
     *
     * @param string $scheduleExprTimezone The timezone for the calendar timer
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setScheduleExprTimezone($scheduleExprTimezone)
    {
        $this->scheduleExprTimezone = $scheduleExprTimezone;
        return $this;
    }

    /**
     * Whether the calendar timer will be an auto timer or not.
     *
     * @param boolean $autoTimer TRUE if the calendar timer will be an auto timer
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setAutoTimer($autoTimer)
    {
        $this->autoTimer = $autoTimer;
        return $this;
    }

    /**
     * Sets the timeout method instance.
     *
     * @param \AppserverIo\Lang\Reflection\MethodInterface $timeoutMethod The timeout method instance
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder The instance itself
     */
    public function setTimeoutMethod(MethodInterface $timeoutMethod)
    {
        $this->timeoutMethod = $timeoutMethod;
        return $this;
    }

    /**
     * Returns the second expression for the calendar timer.
     *
     * @return string The second expression
     */
    public function getScheduleExprSecond()
    {
        return $this->scheduleExprSecond;
    }

    /**
     * Returns the minute expression for the calendar timer.
     *
     * @return string The minute expression
     */
    public function getScheduleExprMinute()
    {
        return $this->scheduleExprMinute;
    }

    /**
     * Returns the hour expression for the calendar timer.
     *
     * @return string The hour expression
     */
    public function getScheduleExprHour()
    {
        return $this->scheduleExprHour;
    }

    /**
     * Returns the day of week expression for the calendar timer.
     *
     * @return string The day of week expression
     */
    public function getScheduleExprDayOfWeek()
    {
        return $this->scheduleExprDayOfWeek;
    }

    /**
     * Returns the day of month expression for the calendar timer.
     *
     * @return string The day of month expression
     */
    public function getScheduleExprDayOfMonth()
    {
        return $this->scheduleExprDayOfMonth;
    }

    /**
     * Returns the month expression for the calendar timer.
     *
     * @return string The month expression
     */
    public function getScheduleExprMonth()
    {
        return $this->scheduleExprMonth;
    }

    /**
     * Returns the year expression for the calendar timer.
     *
     * @return string The year expression
     */
    public function getScheduleExprYear()
    {
        return $this->scheduleExprYear;
    }

    /**
     * The date time for the calendar end date.
     *
     * @return string The end date
     */
    public function getScheduleExprStartDate()
    {
        return $this->scheduleExprStartDate;
    }

    /**
     * The date time for the calendar timer start date.
     *
     * @return string The start date
     */
    public function getScheduleExprEndDate()
    {
        return $this->scheduleExprEndDate;
    }

    /**
     * Returns the timezone for the calendar timer.
     *
     * @return string The timezone for the calendar timer
     */
    public function getScheduleExprTimezone()
    {
        return $this->scheduleExprTimezone;
    }

    /**
     * Queries if the calendar timer will be a auto timer.
     *
     * @return boolean TRUE if calendar timer is an auto timer
     */
    public function isAutoTimer()
    {
        return $this->autoTimer;
    }

    /**
     * Returns the timeout method instance.
     *
     * @return \AppserverIo\Lang\Reflection\MethodInterface The timeout method instance
     */
    public function getTimeoutMethod()
    {
        return $this->timeoutMethod;
    }

    /**
     * Creates a new timer instance with the builders data.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface $timerService The timer service
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarTimer The initialized timer instance
     */
    public function build(TimerServiceInterface $timerService)
    {
        return new CalendarTimer($this, $timerService);
    }
}
