<?php

/**
 * \AppserverIo\Appserver\Core\AbstractExecutorThread
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

/**
 * An abstraction implementation for daemon threads.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractExecutorThread extends \Thread
{

    /**
     * The name of the daemon's default shutdown method.
     *
     * @var string
     */
    const DEFAULT_SHUTDOWN_METHOD = 'defaultShutdown';

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
     * This method is the threads main method that'll be invoked once and has to
     * provide the threads business logic.
     *
     * @return void
     */
    public function execute()
    {
        // override this to implement threads main functionality that has to be executed.
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

            // invoke the execute method
            try {
                $this->execute();
            } catch (\Exception $e) {
                $this->log(LogLevel::ERROR, $e->__toString());
            }

            // clean up the instances and free memory
            $this->cleanUp();

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
     * Returns the default shutdown method registered with register_shutdown_function().
     *
     * @return callable The daemon's default shutdown method
     */
    public function getDefaultShutdownMethod()
    {
        return array(&$this, self::DEFAULT_SHUTDOWN_METHOD);
    }

    /**
     * Returns the default format for log messages.
     *
     * @return string The default log message format
     */
    public function getDefaultLogFormat()
    {
        return self::LOG_FORMAT;
    }

    /**
     * This is the default shutdown method.
     *
     * @return void
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
