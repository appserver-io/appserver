<?php

/**
 * AppserverIo\Appserver\ServletEngine\SessionFactory
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
namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Storage\StorageInterface;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Servlet\ServletSession;
use AppserverIo\Psr\Servlet\Http\HttpSession;

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
class SessionFactory extends \Thread
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
     * Key for invokation of method 'nextFromPool()'.
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
        $this->run = true;
        $this->sessionAvailable = false;

        $this->uniqueId = null;
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
     * Load the next initialized session instance from the session pool.
     *
     * @return \AppserverIo\Psr\Servlet\ServletSession The session instance
     */
    protected function nextFromPool()
    {
        return $this->synchronized(function ($self) {

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
    protected function removeBySessionId($sessionId)
    {
        $this->synchronized(function ($self, $id) {

            // set the action and the session-ID
            $self->action = SessionFactory::ACTION_REMOVE_BY_SESSION_ID;
            $self->sessionId = $id;

            // send a notification
            $self->notify();

        }, $this, $sessionId);
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
            $profileLogger->appendThreadContext('session-factory');
        }

        // while we should create threads, to it
        while ($this->run) {
            $this->synchronized(function ($self) {

                // wait until we receive a notification for a method invokation
                $self->wait(1000000 * SessionFactory::TIME_TO_LIVE);

                switch ($self->action) { // check the method we want to invoke

                    case SessionFactory::ACTION_NEXT_FROM_POOL: // we want to create a new session instance

                        $self->uniqueId = uniqid();
                        $self->sessionPool->set($self->uniqueId, Session::emptyInstance());
                        $self->sessionAvailable = true;

                        // send a notification that method invokation has been processed
                        $self->notify();

                        break;

                    case SessionFactory::ACTION_REMOVE_BY_SESSION_ID: // we want to remove a session instance from the pool

                        foreach ($self->sessionPool as $uniqueId => $session) {
                            if ($session instanceof ServletSession && $session->getId() === $self->sessionId) {
                                $self->sessionPool->remove($uniqueId);
                            }
                        }

                        break;

                    default: // do nothing, because we've an unknown action

                        break;
                }

                // reset the action
                $self->action = null;

            }, $this);

            if ($profileLogger) {
                // profile the size of the session pool
                $profileLogger->debug(sprintf('Size of session pool is: %d', sizeof($this->sessionPool)));
            }
        }
    }
}
