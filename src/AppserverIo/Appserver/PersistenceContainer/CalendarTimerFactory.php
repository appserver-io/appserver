<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\CalendarTimerFactory
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
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface                $application   The application instance
 * @property \Serializable                                                    $info          The serializable info passed to the timer
 * @property boolean                                                          $persistent    TRUE if we want to create a persistent timer, else FALSE
 * @property $schedule
 * @property \AppserverIo\Appserver\PersistenceContainer\CalendarTimerBuilder $timer         The timer instance to be created
 * @property \AppserverIo\Lang\Reflection\MethodInterface                     $timeoutMethod The timeout method instance
 * @property \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface           $timerService  The timer service
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
     * @throws \Exception
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
     * Invoked when the thread starts.
     *
     * @return void
     * @see Stackable::run()
     */
    public function run()
    {

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // register shutdown handler
        register_shutdown_function(array(&$this, "shutdown"));

        // make the application available and register the class loaders
        $application = $this->getApplication();
        $application->registerClassLoaders();

        // run forever
        while (true) {
            // wait until we've been notified
            $this->synchronized(function (CalendarTimerFactory $self) {
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
                ->setScheduleExprStartDate($this->schedule->getStart())
                ->setScheduleExprEndDate($this->schedule->getEnd())
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

    /**
     * Shutdown function to log unexpected errors.
     *
     * @return void
     * @see http://php.net/register_shutdown_function
     */
    public function shutdown()
    {

        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize error type and message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                $this->getApplication()->getInitialContext()->getSystemLogger()->critical($message);
            }
        }
    }
}
