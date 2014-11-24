<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMapFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Collections\Map;
use AppserverIo\Logger\LoggerUtils;

/**
 * A thread thats preinitialized session instances and adds them to the
 * the session pool.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StatefulSessionBeanMapFactory extends \Thread
{

    /**
     * The time we wait after each loop.
     *
     * @var integer
     */
    const TIME_TO_LIVE = 1;

    /**
     * Key for invokation of method 'removeBySessionId()'.
     *
     * @var string
     */
    const ACTION_REMOVE_BY_SESSION_ID = 1;

    /**
     * Key for invokation of method 'newInstance()'.
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
        $this->run = true;

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
     * Stops the session factory.
     *
     * @return void
     */
    public function stop()
    {
        $this->synchronized(function ($self) {
            $self->run = false;
        }, $this);
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
     * @return \TechDivision\Session\ServletSession The session instance
     */
    protected function newInstance($sessionId)
    {

        do {

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
     * This is the main factory method that creates the new
     * session instances and adds them to the session pool.
     *
     * @return void
     */
    public function run()
    {

        // setup autoloader
        require SERVER_AUTOLOADER;

        // try to load the profile logger
        if (isset($this->loggers[LoggerUtils::PROFILE])) {
            $profileLogger = $this->loggers[LoggerUtils::PROFILE];
            $profileLogger->appendThreadContext('stateful-session-bean-map-factory');
        }

        // while we should create threads, to it
        while ($this->run) {

            $this->synchronized(function ($self) { // wait until we receive a notification for a method invokation
                $self->wait(1000000 * StatefulSessionBeanMapFactory::TIME_TO_LIVE);
            }, $this);

            switch ($this->action) { // check the method we want to invoke

                case StatefulSessionBeanMapFactory::ACTION_NEW_INSTANCE: // we want to create a new session instance

                    $this->sessionPool->set($this->sessionId, new StatefulSessionBeanMap());

                    break;

                case StatefulSessionBeanMapFactory::ACTION_REMOVE_BY_SESSION_ID: // we want to remove a session instance from the pool

                    foreach ($this->sessionPool as $sessionId => $session) {
                        if ($session instanceof Map && $sessionId === $this->sessionId) {
                            $this->sessionPool->remove($sessionId);
                        }
                    }

                    break;

                default: // do nothing, because we've an unknown action

                    break;
            }

            // reset the action and session-ID
            $this->action = null;
            $this->sessionId = null;

            if ($profileLogger) { // profile the size of the session pool
                $profileLogger->debug(
                    sprintf('Size of session pool is: %d', sizeof($this->sessionPool))
                );
            }
        }
    }
}
