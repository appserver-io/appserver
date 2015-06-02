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

use Psr\Log\LogLevel;
use AppserverIo\Appserver\Core\Utilities\EnumState;

/**
 * An abstraction implementation for daemon threads.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractDaemonThread extends \Thread
{

    /**
     * The name of the daemon's default shutdown method.
     *
     * @var string
     */
    const DEFAULT_SHUTDOWN_METHOD = 'defaultShutdown';

    /**
     * The default timeout to wait inside the daemon's while() loop.
     *
     * @var integer
     */
    const DEFAULT_TIMEOUT = 1000000;

    /**
     * The default timeout to re-check state during shutdown process.
     *
     * @var integer
     */
    const DEFAULT_SHUTDOWN_TIMEOUT = 500000;

    /**
     * The default format for log messages.
     *
     * @var string
     */
    const LOG_FORMAT = '[%s] - %s (%s): %s [%s]';

    /**
     * This method will be invoked before the while() loop starts and can be used
     * to implement some bootstrap functionality.
     *
     * @return void
     */
    public function bootstrap()
    {
        // override this to implement functionality that has to be executed before the while() loop starts.
    }

    /**
     * This method will be invoked, after the while() loop has been finished and
     * can be used to implement clean up functionality.
     *
     * @return void
     */
    public function cleanUp()
    {
        // override this to implement functionality that has to be executed after the while() loop finished.
    }

    /**
     * Stops the daemon and finally invokes the cleanup() method.
     *
     * @return void
     */
    public function stop()
    {

        // log a message that we're waiting for shutdown
        $this->log(LogLevel::INFO, sprintf('Now start to shutdown daemon %s', get_class($this)));

        // load the default timeout to wait for daemon shutdown
        $shutdownTimeout = $this->getDefaultShutdownTimeout();

        // start shutdown process
        $this->synchronized(function ($self) {
            $self->state = EnumState::get(EnumState::HALT);
        }, $this);

        do {

            // log a message that we're waiting for shutdown
            $this->log(LogLevel::INFO, sprintf('Wait for shutdown daemon %s', get_class($this)));

            // query whether state key is SHUTDOWN or not
            $waitForShutdown = $this->state->notEquals(EnumState::get(EnumState::SHUTDOWN));

            // sleep and wait for successfull daemon shutdown
            $this->sleep($shutdownTimeout);

        } while ($waitForShutdown);

        // log a message that we're waiting for shutdown
        $this->log(LogLevel::INFO, sprintf('Successfully shutdown daemon %s', get_class($this)));
    }

    /**
     * This methods has to return FALSE to stop the daemon keep running.
     *
     * @return boolean TRUE to keep the daemon running, else FALSE
     */
    public function keepRunning()
    {
        return $this->state->equals(EnumState::get(EnumState::RUNNING));
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
        $this->sleep($timeout);
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
        usleep($timeout);
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

            // bootstrap the daemon
            $this->bootstrap();

            // mark the daemon as successfully shutdown
            $this->synchronized(function ($self) {
                $self->state = EnumState::get(EnumState::RUNNING);
            }, $this);

            // keep the daemon running
            while ($this->keepRunning()) {
                $this->iterate($this->getDefaultTimeout());
            }

            // clean up the instances and free memory
            $this->cleanUp();

            // mark the daemon as successfully shutdown
            $this->synchronized(function ($self) {
                $self->state = EnumState::get(EnumState::SHUTDOWN);
            }, $this);

        } catch (\Exception $e) {
            $this->log(LogLevel::ERROR, $e->__toString());
        }
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
        error_log(sprintf($this->getDefaultLogFormat(), date('Y-m-d H:i:s'), gethostname(), $level, $message, json_encode($context)));
    }

    /**
     * Returns the default timeout.
     *
     * @return integer The default timeout in microseconds
     */
    public function getDefaultTimeout()
    {
        return AbstractDaemonThread::DEFAULT_TIMEOUT;
    }

    /**
     * Returns the default shutdown method registered with register_shutdown_function().
     *
     * @return callable The daemon's default shutdown method
     */
    public function getDefaultShutdownMethod()
    {
        return array(&$this, AbstractDaemonThread::DEFAULT_SHUTDOWN_METHOD);
    }

    /**
     * Returns the default shutdown timeout.
     *
     * @return integer The default shutdown timeout in microseconds
     */
    public function getDefaultShutdownTimeout()
    {
        return AbstractDaemonThread::DEFAULT_SHUTDOWN_TIMEOUT;
    }

    /**
     * Returns the default format for log messages.
     *
     * @return string The default log message format
     */
    public function getDefaultLogFormat()
    {
        return AbstractDaemonThread::LOG_FORMAT;
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
                $this->log(LogLevel::ERROR, $message);
            }
        }
    }
}
