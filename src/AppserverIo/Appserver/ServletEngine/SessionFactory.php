<?php

/**
 * \AppserverIo\Appserver\ServletEngine\SessionFactory
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Psr\Servlet\ServletSessionInterface;
use AppserverIo\Appserver\Core\AbstractDaemonThread;

/**
 * A thread which pre-initializes session instances and adds them to the
 * the session pool.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property string|null                           $action           Callback for factory method invocation
 * @property \Psr\Log\LoggerInterface[]            $loggers          Our logger stack
 * @property boolean                               $run              Whether or not the session factory is running
 * @property boolean                               $sessionAvailable Whether or not there is a session available
 * @property string                                $sessionId        ID of the session we want to remove
 * @property \AppserverIo\Storage\GenericStackable $sessionPool      The session pool
 * @property string|null                           $uniqueId         Unique session id
 */
class SessionFactory extends AbstractDaemonThread
{

    /**
     * Key for invocation of method 'removeBySessionId()'.
     *
     * @var string
     */
    const ACTION_REMOVE_BY_SESSION_ID = 1;

    /**
     * Key for invocation of method 'nextFromPool()'.
     *
     * @var string
     */
    const ACTION_NEXT_FROM_POOL = 2;

    /**
     * Initializes the session factory instance.
     *
     * @param \AppserverIo\Storage\GenericStackable $sessionPool The session pool
     */
    public function __construct($sessionPool)
    {

        // initialize the members
        $this->action = null;
        $this->uniqueId = null;
        $this->sessionAvailable = false;

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
        return $this->synchronized(function (SessionFactory $self) {
            return $self->sessionPool;
        }, $this);
    }

    /**
     * Load the next initialized session instance from the session pool.
     *
     * @return \AppserverIo\Psr\Servlet\ServletSessionInterface The session instance
     */
    protected function nextFromPool()
    {
        return $this->synchronized(function (SessionFactory $self) {

            // set the action and the flag we want to wait for
            $self->action = SessionFactory::ACTION_NEXT_FROM_POOL;
            $self->sessionAvailable = false;

            // send the notification that we're ready
            $self->notify();

            // wait for notification
            if ($self->sessionAvailable === false) {
                $self->wait();
            }

            // return the new session instance
            return $self->sessionPool->get($self->uniqueId);

        }, $this);
    }

    /**
     * Removes the session with the passed ID from the session pool.
     *
     * @param string $sessionId ID of the session we want to remove
     *
     * @return void
     */
    public function removeBySessionId($sessionId)
    {
        $this->synchronized(function (SessionFactory $self, $id) {

            // set the action and the session-ID
            $self->action = SessionFactory::ACTION_REMOVE_BY_SESSION_ID;
            $self->sessionId = $id;

            // send a notification
            $self->notify();

        }, $this, $sessionId);
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
            $this->profileLogger->appendThreadContext('session-factory');
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

        // create sessions and add them to the pool
        $this->synchronized(function ($self) {
            // check the method we want to invoke
            switch ($self->action) {

                // we want to create a new session instance
                case SessionFactory::ACTION_NEXT_FROM_POOL:

                    $self->uniqueId = uniqid();
                    $self->sessionPool->set($self->uniqueId, Session::emptyInstance());
                    $self->sessionAvailable = true;

                    // send a notification that method invocation has been processed
                    $self->notify();

                    break;

                // we want to remove a session instance from the pool
                case SessionFactory::ACTION_REMOVE_BY_SESSION_ID:

                    foreach ($self->sessionPool as $uniqueId => $session) {
                        if ($session instanceof ServletSessionInterface && $session->getId() === $self->sessionId) {
                            $self->sessionPool->remove($uniqueId);
                        }
                    }

                    // send a notification that method invocation has been processed
                    $self->notify();

                    break;

                // do nothing, because we've an unknown action
                default:

                    // send a notification that method invocation has been processed
                    $self->notify();

                    break;
            }

            // reset the action
            $self->action = null;

        }, $this);

        // profile the size of the sessions
        if ($this->profileLogger) {
            $this->profileLogger->debug(
                sprintf('Size of session pool is: %d', sizeof($this->sessionPool))
            );
        }
    }
}
