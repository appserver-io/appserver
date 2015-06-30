<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMapFactory
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Appserver\Core\AbstractDaemonThread;

/**
 * A thread that pre-initializes session instances and adds them to the session pool.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property array $loggers The logger instances
 */
class StatefulSessionBeanMapFactory extends AbstractDaemonThread
{

    /**
     * Key for invocation of method 'removeBySessionId()'.
     *
     * @var string
     */
    const ACTION_REMOVE_BY_SESSION_ID = 1;

    /**
     * Key for invocation of method 'newInstance()'.
     *
     * @var string
     */
    const ACTION_NEW_INSTANCE = 2;

    /**
     * Initializes the session factory instance.
     *
     * @param \AppserverIo\Storage\GenericStackable $sessionPool The session pool
     */
    public function __construct($sessionPool)
    {

        // initialize the members
        $this->createSession = false;
        $this->sessionId = null;
        $this->action = null;

        // set the session pool storage
        $this->sessionPool = $sessionPool;
    }

    /**
     * Injects the available logger instances.
     *
     * @param array $loggers The logger instances
     *
     * @return void
     */
    public function injectLoggers(array $loggers)
    {
        $this->loggers = $loggers;
    }

    /**
     * public function return the session pool.
     *
     * @return \AppserverIo\Storage\StackableStorage The session pool instance
     */
    public function getSessionPool()
    {
        return $this->synchronized(function ($self) {
            return $self->sessionPool;
        }, $this);
    }

    /**
     * Removes the session with the passed ID from the session pool.
     *
     * @param string $sessionId ID of the session we want to remove
     *
     * @return void
     */
    protected function removeBySessionId($sessionId)
    {

        // remove all SFSBs for the session with the passed ID
        $this->synchronized(function ($self, $id) {

            // set the action and the session-ID
            $self->action = StatefulSessionBeanMapFactory::ACTION_REMOVE_BY_SESSION_ID;
            $self->sessionId = $id;

            // send a notification
            $self->notify();

        }, $this, $sessionId);
    }

    /**
     * Load the next initialized session instance from the session pool.
     *
     * @param string $sessionId The session-ID we want to create a new map for
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMap The SFB map instance
     */
    protected function newInstance($sessionId)
    {

        do {
            // create a new SFSB instance and return it
            $this->synchronized(function ($self, $id) {

                // set action and session-ID
                $self->action = StatefulSessionBeanMapFactory::ACTION_NEW_INSTANCE;
                $self->sessionId = $id;

                // send the notification that we're ready
                $self->notify();

            }, $this, $sessionId);

        } while ($this->sessionPool->has($sessionId) === false);

        // return the new session instance
        return $this->sessionPool->get($sessionId);
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

        // try to load the profile logger
        if (isset($this->loggers[LoggerUtils::PROFILE])) {
            $this->profileLogger = $this->loggers[LoggerUtils::PROFILE];
            $this->profileLogger->appendThreadContext('stateful-session-bean-map-factory');
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

        // check the method we want to invoke
        switch ($this->action) {

            // we want to create a new session instance
            case StatefulSessionBeanMapFactory::ACTION_NEW_INSTANCE:

                $this->sessionPool->set($this->sessionId, new StatefulSessionBeanMap());

                break;

                // we want to remove a session instance from the pool
            case StatefulSessionBeanMapFactory::ACTION_REMOVE_BY_SESSION_ID:

                foreach ($this->sessionPool as $sessionId => $session) {
                    if ($session instanceof MapInterface && $sessionId === $this->sessionId) {
                        $this->sessionPool->remove($sessionId);
                    }
                }

                break;

                // do nothing, because we've an unknown action
            default:

                break;
        }

        // reset the action and session-ID
        $this->action = null;
        $this->sessionId = null;

        // profile the size of the session pool
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
}
