<?php

/**
 * AppserverIo\Appserver\Core\Utilities\ErrorUtil
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

namespace AppserverIo\Appserver\Core\Utilities;

use Psr\Log\LogLevel;

// define a custom user exception constant
define('E_EXCEPTION', 0);

/**
 * Utility class that providing functionality to handle PHP errors.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ErrorUtil
{

    /**
     * The singleton instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Utils\ErrorUtil
     */
    protected static $instance;

    /**
     * Create's and return's the singleton instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Utils\ErrorUtil The singleton instance
     */
    public static function singleton()
    {

        // query whether or not the instance has already been created
        if (ErrorUtil::$instance == null) {
            ErrorUtil::$instance = new static();
        }

        // return the singleton instance
        return ErrorUtil::$instance;
    }

    /**
     * Create's a new error instance with the values from the passed array.
     *
     * @param array $error The array containing the error information
     *
     * @return \AppserverIo\Appserver\Core\Utilities\ErrorInterface The error instance
     */
    public function fromArray(array $error)
    {

        // extract the array with the error information
        list ($type, $message, $file, $line) = array_values($error);

        // initialize and return the error instance
        return new Error($type, $message, $file, $line);
    }

    /**
     * Create's a new error instance from the passed exception.
     *
     * @param \Exception $e The exception to create the error instance from
     *
     * @return \AppserverIo\Appserver\Core\Utilities\ErrorInterface The error instance
     */
    public function fromException(\Exception $e)
    {
        return new Error(E_EXCEPTION, $e->__toString(), $e->getFile(), $e->getLine(), $e->getCode());
    }

    /**
     * Prepare's the error message for logging/rendering purposes.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\ErrorInterface $error The error instance to create the message from
     *
     * @return string The error message
     */
    public function prepareMessage(ErrorInterface $error)
    {
        return sprintf('PHP %s: %s in %s on line %d', $this->mapErrorCode($error), $error->getMessage(), $error->getFile(), $error->getLine());
    }

    /**
     * Return's the log level for the passed error instance.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\ErrorInterface $error The error instance to map the log level for
     *
     * @return string
     */
    public function mapLogLevel(ErrorInterface $error)
    {

        // initialize the log level, default is 'error'
        $logLevel = LogLevel::ERROR;

        // query the error type
        switch ($error->getType()) {
            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $logLevel = LogLevel::WARNING;
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
            case E_STRICT:
            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $logLevel = LogLevel::NOTICE;
                break;

            default:
                break;
        }

        // return the log level
        return $logLevel;
    }

    /**
     * Return's the a human readable error representation for the passed error instance.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\ErrorInterface $error The error instance
     *
     * @return string The human readable error representation
     */
    public function mapErrorCode(ErrorInterface $error)
    {

        // initialize the error representation
        $wrapped = 'Unknown';

        // query the error type
        switch ($error->getType()) {
            case E_EXCEPTION:
                $wrapped = 'Exception';
                break;

            case E_PARSE:
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
                $wrapped = 'Fatal Error';
                break;

            case E_WARNING:
            case E_USER_WARNING:
            case E_COMPILE_WARNING:
            case E_RECOVERABLE_ERROR:
                $wrapped = 'Warning';
                break;

            case E_NOTICE:
            case E_USER_NOTICE:
                $wrapped = 'Notice';
                break;

            case E_STRICT:
                $wrapped = 'Strict';
                break;

            case E_DEPRECATED:
            case E_USER_DEPRECATED:
                $wrapped = 'Deprecated';
                break;

            default:
                break;
        }

        // return the human readable error representation
        return $wrapped;
    }

    /**
     * Query whether or not the passed error type is fatal or not.
     *
     * @param integer $type The type to query for
     *
     * @return boolean TRUE if the passed type is a fatal error, else FALSE
     */
    public function isFatal($type)
    {
        return in_array($type, array(E_EXCEPTION, E_PARSE,  E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR));
    }
}
