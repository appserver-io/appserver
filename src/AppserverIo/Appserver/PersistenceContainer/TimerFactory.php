<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\TimerFactory
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
use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;
use AppserverIo\Appserver\Core\AbstractDaemonThread;
use AppserverIo\Appserver\PersistenceContainer\Utils\TimerState;

/**
 * A thread which creates timer instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface      $application       The application instance
 * @property \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface $timerService      The timer service to create the service for
 * @property \DateTime                                              $initialExpiration The date at which the first timeout should occur.
 * @property integer                                                $intervalDuration  The interval (in milli seconds) between consecutive timeouts for the newly created timer.
 * @property \Serializable                                          $info              Serializable info that will be made available through the newly created timers Timer::getInfo() method
 * @property boolean                                                $persistent        TRUE if the newly created timer has to be persistent
 */
class TimerFactory extends AbstractDaemonThread implements TimerFactoryInterface
{

    /**
     * Initializes and the timer factory.
     */
    public function __construct()
    {
        $this->dispatched = true;
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
     * Create a new timer instance with the passed data.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface $timerService      The timer service to create the service for
     * @param \DateTime                                              $initialExpiration The date at which the first timeout should occur.
     *                                                                                  If the date is in the past, then the timeout is triggered immediately
     *                                                                                  when the timer moves to TimerState::ACTIVE
     * @param integer                                                $intervalDuration  The interval (in milli seconds) between consecutive timeouts for the newly created timer.
     *                                                                                  Cannot be a negative value. A value of 0 indicates a single timeout action
     * @param \Serializable                                          $info              Serializable info that will be made available through the newly created timers Timer::getInfo() method
     * @param boolean                                                $persistent        TRUE if the newly created timer has to be persistent
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimerInterface Returns the newly created timer
     *
     * @throws \Exception
     */
    public function createTimer(TimerServiceInterface $timerService, \DateTime $initialExpiration, $intervalDuration = 0, \Serializable $info = null, $persistent = true)
    {

        // lock the method
        \Mutex::lock($this->mutex);

        // we're not dispatched
        $this->dispatched = false;

        // initialize the data
        $this->info = $info;
        $this->persistent = $persistent;
        $this->timerService = $timerService;
        $this->intervalDuration = $intervalDuration;
        $this->initialExpiration = $initialExpiration->format(ScheduleExpression::DATE_FORMAT);

        // notify the thread
        $this->notify();

        // wait till we've dispatched the request
        while ($this->dispatched === false) {
            usleep(100);
        }

        // unlock the method
        \Mutex::unlock($this->mutex);

        // return the created timer
        return $this->timer;
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
            $this->profileLogger->appendThreadContext('timer-factory');
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

        // create the requested timer instance
        $this->synchronized(function ($self) {

            // create the timer, only if we're NOT dispatched
            if ($self->dispatched === false) {
                $self->timer = Timer::builder()
                    ->setNewTimer(true)
                    ->setId(Uuid::uuid4()->__toString())
                    ->setInitialDate($self->initialExpiration)
                    ->setRepeatInterval($self->intervalDuration)
                    ->setInfo($self->info)
                    ->setPersistent($self->persistent)
                    ->setTimerState(TimerState::CREATED)
                    ->setTimedObjectId($self->timerService->getTimedObjectInvoker()->getTimedObjectId())
                    ->build($self->timerService);

                // we're dispatched now
                $self->dispatched = true;
            }

        }, $this);

        // profile the size of the sessions
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Size of session pool is: %d', sizeof($this->sessionPool))
            );
        }
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
}
