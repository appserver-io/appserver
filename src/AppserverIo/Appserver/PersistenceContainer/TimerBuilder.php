<?php
/**
 * AppserverIo\Appserver\PersistenceContainer\TimerBuilder
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

use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;

/**
 * Implementation of a timer builder.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TimerBuilder
{

    /**
     * The unique identifier of timer we want to create.
     *
     * @var string
     */
    protected $id;

    /**
     * The unique identifier of the timed object instance.
     *
     * @var string
     */
    protected $timedObjectId;

    /**
     * The date time fo the first timeout expiration.
     *
     * @var \DateTime
     */
    protected $initialDate;

    /**
     * The repeat interval between the expiration dates.
     *
     * @var integer
     */
    protected $repeatInterval;

    /**
     * The date time for the next expiration.
     *
     * @var \DateTime
     */
    protected $nextDate;

    /**
     * The date time of the previous run.
     *
     * @var \DateTime
     */
    protected $previousRun;

    /**
     * The serializable info passed to the timer.
     *
     * @var \Serializable
     */
    protected $info;

    /**
     * The state of the timer we want to create.
     *
     * @var integer
     */
    protected $timerState;

    /**
     * Whether we want to create a persistent timer.
     *
     * @var boolean
     */
    protected $persistent;

    /**
     * Whether we want to create a new timer or not.
     *
     * @var boolean
     */
    protected $newTimer;

    /**
     * Sets the unique identifier of timer we want to create.
     *
     * @param string $id The unique identifier of the timer to be created
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Sets the unique identifier of the timed object instance.
     *
     * @param string $timedObjectId The unique identifier of the timed object instance
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setTimedObjectId($timedObjectId)
    {
        $this->timedObjectId = $timedObjectId;
        return $this;
    }

    /**
     * The date time fo the first timeout expiration.
     *
     * @param \DateTime $initialDate The date time for the first timeout expiration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setInitialDate(\DateTime $initialDate)
    {
        $this->initialDate = $initialDate;
        return $this;
    }

    /**
     * Sets the repeat interval between the expiration dates.
     *
     * @param integer $repeatInterval The repeat interval
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setRepeatInterval($repeatInterval)
    {
        $this->repeatInterval = $repeatInterval;
        return $this;
    }

    /**
     * Sets the date time for the next expiration.
     *
     * @param \DateTime $nextDate The date time for the next expiration
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setNextDate(\DateTime $nextDate)
    {
        $this->nextDate = $nextDate;
        return $this;
    }

    /**
     * Sets the date time of the previous run.
     *
     * @param \DateTime $previousRun The date time of the previous run
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setPreviousRun(\DateTime $previousRun)
    {
        $this->previousRun = $previousRun;
        return $this;
    }

    /**
     * Sets the serializable info passed to the timer.
     *
     * @param \Serializable $info The serializable info passed to the timer
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setInfo(\Serializable $info = null)
    {
        $this->info = $info;
        return $this;
    }

    /**
     * Sets the state of the timer we want to create.
     *
     * @param integer $timerState The timer state
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setTimerState($timerState)
    {
        $this->timerState = $timerState;
        return $this;
    }

    /**
     * Whether we want to create a persistent timer.
     *
     * @param boolean $persistent TRUE if we want to create a persistent timer, else FALSE
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setPersistent($persistent)
    {
        $this->persistent = $persistent;
        return $this;
    }

    /**
     * Whether we want to create a new timer or not.
     *
     * @param boolean $newTimer TRUE if we want to create a new timer, else NOT
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\TimerBuilder The instance itself
     */
    public function setNewTimer($newTimer)
    {
        $this->newTimer = $newTimer;
        return $this;
    }

    /**
     * Returns the unique identifier of timer we want to create.
     *
     * @return string The unique identifier of the timer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the unique identifier of the timed object instance.
     *
     * @return string The unique identifier of the time object instance
     */
    public function getTimedObjectId()
    {
        return $this->timedObjectId;
    }

    /**
     * Returns the date time fo the first timeout expiration.
     *
     * @return \DateTime The first timeout expiration
     */
    public function getInitialDate()
    {
        return $this->initialDate;
    }

    /**
     * Returns the repeat interval between the expiration dates.
     *
     * @return integer The repeat interval
     */
    public function getRepeatInterval()
    {
        return $this->repeatInterval;
    }

    /**
     * Returns the date time for the next expiration.
     *
     * @return \DateTime The date time for the next expiration
     */
    public function getNextDate()
    {
        return $this->nextDate;
    }

    /**
     * Returns the date time of the previous run.
     *
     * @return \DateTime The date time of the previous run
     */
    public function getPreviousRun()
    {
        return $this->previousRun;
    }

    /**
     * Returns the serializable info passed to the timer.
     *
     * @return \Serializable The serializable info
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * Returns the state of the timer we want to create.
     *
     * @return integer The timer state
     */
    public function getTimerState()
    {
        return $this->timerState;
    }

    /**
     * Queries whether we want to create a persistent timer.
     *
     * @return boolean TRUE if we want to create a persistent timer, else FALSE
     */
    public function isPersistent()
    {
        return $this->persistent;
    }

    /**
     * Queries whether we want to create a new timer.
     *
     * @return boolean TRUE if this is a new timer
     */
    public function isNewTimer()
    {
        return $this->newTimer;
    }

    /**
     * Creates a new timer instance with the builders data.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface $timerService The timer service
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\Timer The initialized timer instance
     */
    public function build(TimerServiceInterface $timerService)
    {
        return new Timer($this, $timerService);
    }
}
