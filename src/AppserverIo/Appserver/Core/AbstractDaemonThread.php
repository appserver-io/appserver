<?php

/**
 * \AppserverIo\Appserver\Core\AbstractDaemonThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

/**
 * An abstraction layer for Threads
 *
 * The major change vs. a normal Thread is that you have to use a main() method instead of a run() method.
 * You can use init() method to get and process args passed in constructor.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractDaemonThread extends \Thread
{

    /**
     * The default timeout to wait inside the daemon's while() loop.
     *
     * @var integer
     */
    const TIME_TO_LIVE = 1000000;

    /**
     * The name of the daemon's default shutdown method.
     *
     * @var string
     */
    const DEFAULT_SHUTDOWN_METHOD = 'defaultShutdown';

    /**
     * The thread implementation main method which will be called from run in abstractness
     *
     * @return void
     */
    abstract public function main();

    /**
     * The thread implementation main method which will be called from run in abstractness
     *
     * @return void
     */
    public function cleanup()
    {
        // do nothing, we've nothing to cleanup
    }

    /**
     * Stops the daemon and finally invokes the cleanup() method.
     *
     * @return void
     */
    public function stop()
    {

        // start daemon shutdown
        $this->synchronized(function ($self) {
            $self->applicationState = ApplicationStateKeys::get(ApplicationStateKeys::HALT);
        }, $this);

        do {
            // log a message that we'll wait till application has been shutdown
            $this->getInitialContext()->getSystemLogger()->info(
                sprintf('Wait for application %s to be shutdown', $this->getName())
            );

            // query whether application state key is SHUTDOWN or not
            $waitForShutdown = $this->synchronized(function ($self) {
                return $self->applicationState->notEquals(ApplicationStateKeys::get(ApplicationStateKeys::SHUTDOWN));
            }, $this);

            // wait one second more
            sleep(1);

        } while ($waitForShutdown);
    }

    /**
     * This methods has to return FALSE to stop the daemon keep running.
     *
     * @return boolean TRUE to keep the daemon running, else FALSE
     */
    protected function keepRunning()
    {
        return true;
    }

    /**
     * This is invoked on every iteration of the daemons while() loop.
     *
     * @param integer $timeout The default timeout is 1 second
     *
     * @return void
     */
    protected function iterate($timeout = AbstractDaemonThread::TIME_TO_LIVE)
    {
        $this->sleep($timeout);
    }

    /**
     * The daemon's main run method. It should not be necessary to override,
     * instead use the main(), iterate() and cleanup() methods to implement
     * the daemons custom functionality.
     *
     * @return void
     * @see \Thread::run()
     */
    public function run()
    {

        try {

            // register shutdown handler
            register_shutdown_function($this->getDefaultShutdownMethod());

            $this->main();

            while ($this->keepRunning()) {
                $this->iterate();
            }

            $this->cleanup();

        } catch (\Exception $e) {
            $this->logError($e->__toString());
        }
    }

    /**
     * Default method to log errors.
     *
     * @param string $message The message to log
     *
     * @return void
     */
    protected function logError($message)
    {
        error_log($message);
    }

    /**
     * Returns the default shutdown method registered with register_shutdown_function().
     *
     * @return callable The daemon's default shutdown method
     */
    protected function getDefaultShutdownMethod()
    {
        return array(&$this, AbstractDaemonThread::DEFAULT_SHUTDOWN_METHOD);
    }

    /**
     * This is the default shutdown
     */
    public function defaultShutdown()
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
                $this->logError($message);
            }
        }
    }
}
