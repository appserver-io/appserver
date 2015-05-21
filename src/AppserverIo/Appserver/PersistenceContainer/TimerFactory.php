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
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\TimerServiceInterface;
use AppserverIo\Appserver\PersistenceContainer\Utils\TimerState;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;

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
class TimerFactory extends \Thread implements TimerFactoryInterface
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

        do {
            // create a counter
            $counter = 0;

            // if this is the first loop
            if ($counter === 0) {
                // we're not dispatched
                $this->dispatched = false;

                // initialize the data
                $this->info = $info;
                $this->persistent = $persistent;
                $this->timerService = $timerService;
                $this->intervalDuration = $intervalDuration;
                $this->initialExpiration = $initialExpiration->format(ScheduleExpression::DATE_FORMAT);

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
            $this->synchronized(function ($self) {
                $self->wait();
            }, $this);

            // create the timer
            $this->timer = Timer::builder()
                ->setNewTimer(true)
                ->setId(Uuid::uuid4()->__toString())
                ->setInitialDate($this->initialExpiration)
                ->setRepeatInterval($this->intervalDuration)
                ->setInfo($this->info)
                ->setPersistent($this->persistent)
                ->setTimerState(TimerState::CREATED)
                ->setTimedObjectId($this->timerService->getTimedObjectInvoker()->getTimedObjectId())
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
