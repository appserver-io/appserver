<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\Tasks\TimerTask
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

namespace AppserverIo\Appserver\PersistenceContainer\Tasks;

use AppserverIo\Psr\EnterpriseBeans\TimerInterface;
use AppserverIo\Appserver\PersistenceContainer\Utils\TimerState;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;

/**
 * The timer task.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TimerTask extends \Thread
{

    /**
     * The timer we have to handle.
     *
     * @var \AppserverIo\Psr\EnterpriseBeans\TimerInterface
     */
    protected $timer;

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the queue worker with the application and the storage it should work on.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface   $timer       The timer we have to handle
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    public function __construct(TimerInterface $timer, ApplicationInterface $application)
    {

        // initialize timer and application instance
        $this->timer = $timer;
        $this->application = $application;

        // start the timer task
        $this->start(PTHREADS_INHERIT_NONE|PTHREADS_INHERIT_CONSTANTS);
    }

    /**
     * Returns the timer instance the task is bound to.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\TimerInterface The timer instance
     */
    public function getTimer()
    {
        return $this->timer;
    }

    /**
     * We process the timer here.
     *
     * @return void
     */
    public function run()
    {

        // register the default autoloader
        require SERVER_AUTOLOADER;

        // synchronize the application instance and register the class loaders
        $application = $this->application;
        $application->registerClassLoaders();

        // sychronize timer instance
        $timer = $this->timer;

        // we lock the timer for this check, because if a cancel is in progress then we do not want to
        // do the isActive check, but wait for the cancelling transaction to finish one way or another
        $timer->lock();

        try {
            // check if the timer is active
            if ($timer->isActive() === false) {
                // create the actual date
                $now = new \DateTime('now');
                // log an info that the timer is NOT active
                $application->getInitialContext()->getSystemLogger()->info(
                    sprintf(
                        'Timer is not active, skipping this scheduled execution at: %s for %s',
                        $now->format('Y-m-d'),
                        $timer->getId()
                    )
                );

                return; // return without do anything
            }

            // set the current date as the "previous run" of the timer
            $timer->setPreviousRun(new \DateTime());

            // set the next timeout, if one is available
            if ($nextTimeout = $this->calculateNextTimeout($timer)) {
                $timer->setNextTimeout($nextTimeout->format(ScheduleExpression::DATE_FORMAT));
            } else {
                $timer->setNextTimeout(null);
            }

            // change the state to mark it as in timeout method
            $timer->setTimerState(TimerState::IN_TIMEOUT);

            // persist changes
            $timer->getTimerService()->persistTimer($timer, false);

        } catch (\Exception $e) {
            $application->getInitialContext()->getSystemLogger()->error($e->__toString());
        }

        // unlock after timeout recalculation
        $timer->unlock();

        // call timeout method
        $this->callTimeout($timer);
    }

    /**
     * Invokes the timeout on the passed timer.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface $timer The timer we want to invoke the timeout for
     *
     * @return void
     */
    protected function callTimeout(TimerInterface $timer)
    {

        // if we have any more schedules remaining, then schedule a new task
        if ($timer->getNextExpiration() != null && !$timer->isInRetry()) {
            $timer->scheduleTimeout(false);
        }

        // invoke the timeout on the timed object
        $timer->getTimerService()->getTimedObjectInvoker()->callTimeout($timer);
    }

    /**
     * Calculates and returns the next timeout for the passed timer.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\TimerInterface $timer The timer we want to calculate the next timeout for
     *
     * @return \DateTime|null The next expiration timeout
     */
    protected function calculateNextTimeout(TimerInterface $timer)
    {

        // try to load the interval
        $intervalDuration = $timer->getIntervalDuration();

        // check if we've a interval
        if ($intervalDuration > 0) {
            // load the next expiration date
            $nextExpiration = $timer->getNextExpiration();

            // compute and return the next expiration date
            return $nextExpiration->add(new \DateInterval(sprintf('PT%sS', $intervalDuration / 1000000)));
        }

        // return nothing
        return null;
    }
}
