<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\TimerFactoryInterface
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
 * Interface for timer factory implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface TimerFactoryInterface
{

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
     */
    public function createTimer(TimerServiceInterface $timerService, \DateTime $initialExpiration, $intervalDuration = 0, \Serializable $info = null, $persistent = true);
}
