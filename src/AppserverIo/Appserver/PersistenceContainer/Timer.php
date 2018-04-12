<?php
/**
 * \AppserverIo\Appserver\PersistenceContainer\Timer
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

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\IllegalStateException;
use AppserverIo\Psr\EnterpriseBeans\TimerInterface;
use AppserverIo\Psr\EnterpriseBeans\NoMoreTimeoutsException;
use AppserverIo\Psr\EnterpriseBeans\NoSuchObjectLocalException;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;
use AppserverIo\Appserver\PersistenceContainer\Tasks\TimerTask;
use AppserverIo\Appserver\PersistenceContainer\Utils\TimerState;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;

/**
 * A timer implementation for single action and interval timers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property string                                                 $id                The unique identifier for this timer
 * @property boolean                                                $persistent        TRUE if this timer has persistent guarantees
 * @property string                                                 $initialExpiration The first expiry of this timer
 * @property integer                                                $intervalDuration  The duration (in microseconds) between timeouts
 * @property string                                                 $timedObjectId     The ID of the timed object the timer is bound to
 * @property string                                                 $previousRun       The date of the previous run
 * @property integer                                                $timerState        The timer state
 * @property \Serializable                                          $info              The Serializable object that was passed in at timer creation
 * @property \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface $timerService      The timer service instance
 */
class Timer extends GenericStackable implements TimerInterface
{

    /**
     * The date format we use to serialize/unserialize \DateTime properties.
     *
     * @var string
     */
    const DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Initializes the timer with the necessary data.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\TimerBuilder $builder      The builder with the data to create the timer from
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface   $timerService The timer service instance
     */
    public function __construct(TimerBuilder $builder, TimerServiceInterface $timerService)
    {

        // initialize the members
        $this->id = $builder->getId();
        $this->info = $builder->getInfo();
        $this->persistent = $builder->isPersistent();
        $this->timedObjectId = $builder->getTimedObjectId();
        $this->intervalDuration = $builder->getRepeatInterval();

        // if we found a initial date, set it
        if ($initialDate = $builder->getInitialDate()) {
            $this->initialExpiration = $initialDate;
        }

        // check if this is a new timer and the builders next date is NULL
        if ($builder->isNewTimer() && $builder->getNextDate() == null) {
            // if yes, the next expiration date is the initial date
            $this->nextExpiration = $this->initialExpiration;
        } else {
            $this->nextExpiration = $builder->getNextDate();
        }

        // we don't have a previous run
        $this->previousRun = null;

        // set the instances
        $this->timerState = $builder->getTimerState();
        $this->timerService = $timerService;
        $this->timedObjectInvoker = $timerService->getTimedObjectInvoker();
    }

    /**
     * The unique identifier for this timer.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the instance that'll be invoked when the timeout expires.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimedObjectInvokerInterface The instance to be invoked when the timeout expires
     */
    public function getTimedObjectInvoker()
    {
        return $this->timedObjectInvoker;
    }

    /**
     * Returns the instance that'll be invoked when the timeout expires.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface The instance to be invoked when the timeout expires
     */
    public function getTimerService()
    {
        return $this->timerService;
    }

    /**
     * Returns the first expiry of this timer.
     *
     * @return \DateTime The first expiry of this timer
     */
    public function getInitialExpiration()
    {
        return \DateTime::createFromFormat(ScheduleExpression::DATE_FORMAT, $this->initialExpiration);
    }

    /**
     * Returns the duration (in microseconds) between timeouts.
     *
     * @return integer The duration (in microseconds) between timeouts
     */
    public function getIntervalDuration()
    {
        return $this->intervalDuration;
    }

    /**
     * Returns the previous run date.
     *
     * @return \DateTime|null The previous run date
     */
    public function getPreviousRun()
    {
        if ($this->previousRun != null) {
            return \DateTime::createFromFormat(ScheduleExpression::DATE_FORMAT, $this->previousRun);
        }
    }

    /**
     * Sets the information associated with the timer at the time of creation.
     *
     * @param \Serializable $info The Serializable object that was passed in at timer creation.
     *
     * @return void
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     **/
    public function setInfo(\Serializable $info)
    {
        $this->info = $info;
    }

    /**
     * Cause the timer and all its associated expiration notifications to be canceled.
     *
     * @return void
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     **/
    public function cancel()
    {
        $this->setTimerState(TimerState::CANCELED);
    }

    /**
     * This method is similar to Timer::getNextTimeout(), except that this method does not check the timer state and
     * hence does not throw either IllegalStateException or NoSuchObjectLocalException or EnterpriseBeansException.
     *
     * @return \DateTime The date of the next timeout expiration
     */
    public function getNextExpiration()
    {
        if ($this->nextExpiration != null) {
            return \DateTime::createFromFormat(ScheduleExpression::DATE_FORMAT, $this->nextExpiration);
        }
    }

    /**
     * Sets the next timeout of this timer.
     *
     * @param string $next The next scheduled timeout of this timer
     *
     * @return void
     */
    public function setNextTimeout($next = null)
    {
        if ($next != null) {
            $this->nextExpiration = $next;
        } else {
            $this->nextExpiration = null;
        }
    }

    /**
     * Get the point in time at which the next timer expiration is scheduled to occur.
     *
     * @return \DateTime Get the point in time at which the next timer expiration is scheduled to occur
     * @throws \AppserverIo\Psr\EnterpriseBeans\NoSuchObjectLocalException If invoked on a timer that has expired or has been cancelled
     * @throws \AppserverIo\Psr\EnterpriseBeans\NoMoreTimeoutsException Indicates that the timer has no future timeouts
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure
     **/
    public function getNextTimeout()
    {

        // throw an exception if timer has been canceled
        if ($this->isCanceled()) {
            throw new NoSuchObjectLocalException('Timer has been cancelled');
        }

        // return the timeout of the next expiration
        $nextExpiration = $this->getNextExpiration();

        // check if we've a next expiration timeout
        if ($nextExpiration == null) {
            throw new NoMoreTimeoutsException(sprintf('Timer %s has no more future timeouts', $this->getId()));
        }

        // return the next expiration date
        return $nextExpiration;
    }

    /**
     * Get the number of microseconds that will elapse before the next scheduled timer expiration.
     *
     * @return int Number of microseconds that will elapse before the next scheduled timer expiration
     * @throws \AppserverIo\Psr\EnterpriseBeans\NoSuchObjectLocalException If invoked on a timer that has expired or has been cancelled
     * @throws \AppserverIo\Psr\EnterpriseBeans\NoMoreTimeoutsException Indicates that the timer has no future timeouts
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure
     **/
    public function getTimeRemaining()
    {

        // load next timeout and current time in seconds
        $nextTimeoutInSeconds = $this->getNextTimeout()->getTimestamp();
        $currentTimeInSeconds = time();

        // return the time remaining in MICROSECONDS
        return ($nextTimeoutInSeconds - $currentTimeInSeconds)  * 1000000;
    }

    /**
     * Get the information associated with the timer at the time of creation.
     *
     * @return \Serializable The Serializable object that was passed in at timer creation, or null if the
     *         info argument passed in at timer creation was null.
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     **/
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Get a serializable handle to the timer. This handle can be used at a later time to
     * re-obtain the timer reference.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimerHandleInterface Handle of the Timer
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     **/
    public function getHandle()
    {
        // TODO: Implement getHandle() method.
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
        throw new IllegalStateException('This is not an calendar based timer');
    }

    /**
     * Query whether this timer is a calendar-based timer.
     *
     * @return boolean True if this timer is a calendar-based timer.
     * @throws \AppserverIo\Psr\EnterpriseBeans\EnterpriseBeansException If this method could not complete due to a system-level failure.
     */
    public function isCalendarTimer()
    {
        return false;
    }

    /**
     * Query whether this is an auto-timer or a normal programmatically created timer.
     *
     * @return boolean TRUE if this timer is a auto-timer, else FALSE
     */
    public function isAutoTimer()
    {
        return false;
    }

    /**
     * Query whether this timer has persistent semantics.
     *
     * @return boolean True if this timer has persistent guarantees.
     */
    public function isPersistent()
    {
        return $this->persistent;
    }

    /**
     * Creates and schedules a TimerTask for the next timeout of this timer.
     *
     * @param boolean $newTimer TRUE if this is a new timer being scheduled, and not a re-schedule due to a timeout
     *
     * @return void
     */
    public function scheduleTimeout($newTimer)
    {
        $this->getTimerService()->scheduleTimeout($this, $newTimer);
    }

    /**
     * Returns the task which handles the timeouts of this timer.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Tasks\TimerTask The task
     */
    public function getTimerTask(ApplicationInterface $application)
    {
        return new TimerTask($this, $application);
    }

    /**
     * Instanciates a new builder that creates a timer instance.
     *
     *  @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The builder instance
     */
    public static function builder()
    {
        return new TimerBuilder();
    }

    /**
     * Sets the (new) timer state.
     *
     * @param integer $timerState The timer state
     *
     * @return void
     */
    public function setTimerState($timerState)
    {
        $this->timerState = $timerState;
    }

    /**
     * Sets the previous run date.
     *
     * @param \DateTime $previousRun The previous run date
     *
     * @return void
     */
    public function setPreviousRun(\DateTime $previousRun)
    {
        $this->previousRun = $previousRun->format(ScheduleExpression::DATE_FORMAT);
    }

    /**
     * Returns TRUE if this timer is active, else FALSE.
     *
     * A timer is considered to be "active", if its timer state is neither of the following:
     *
     * - TimerState::CANCELED
     * - TimerState::EXPIRED
     * - has not been suspended
     *
     * And if the corresponding timer service is still up
     *
     * @return boolean TRUE if the timer is active
     */
    public function isActive()
    {
        return $this->timerService->isStarted() && !$this->isCanceled() && !$this->isExpired() &&
            ($this->timerService->isScheduled($this->getId()) || $this->timerState == TimerState::CREATED);
    }

    /**
     * Returns TRUE if this timer is in TimerState::CANCELED state, else FALSE.
     *
     * @return boolean TRUE if this timer has been canceled
     */
    public function isCanceled()
    {
        return $this->timerState == TimerState::CANCELED;
    }

    /**
     * Returns TRUE if this timer is in TimerState::EXPIRED state, else FALSE.
     *
     * @return boolean TRUE if this timer has been expired
     */
    public function isExpired()
    {
        return $this->timerState == TimerState::EXPIRED;
    }

    /**
     * Returns TRUE if this timer is in TimerState::RETRY_TIMEOUT, else returns FALSE.
     *
     * @return boolean TRUE if this timer will be retried
     */
    public function isInRetry()
    {
        return $this->timerState == TimerState::RETRY_TIMEOUT;
    }
}
