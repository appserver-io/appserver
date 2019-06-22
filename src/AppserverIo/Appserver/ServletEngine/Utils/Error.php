<?php

/**
 * AppserverIo\Appserver\ServletEngine\Utils\Error
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

namespace AppserverIo\Appserver\ServletEngine\Utils;

/**
 * Wrapper for an error triggered by PHP's default error handling.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Error extends \AppserverIo\Appserver\Core\Utilities\Error implements ErrorInterface
{

    /**
     * The HTTP status code that has to be send back with the response.
     *
     * @var integer
     */
    protected $statusCode;

    /**
     * The original exception instance.
     *
     * @var \Exception|null
     */
    protected $exception;

    /**
     * Initializes the error with the passed values.
     *
     * @param integer         $type       The error type
     * @param string          $message    The error message
     * @param string          $file       The name of the file where the error has been triggered
     * @param integer         $line       The line in the file where the error has been triggered
     * @param integer         $statusCode The HTTP status code that has to be send back with the response
     * @param \Exception|null $exception  The original exception instance
     */
    public function __construct($type, $message, $file, $line, $statusCode = 0, $exception = null)
    {

        // invoke the parent method
        parent::__construct($type, $message, $file, $line);

        // initialize the status code
        $this->statusCode = $statusCode;

        // initialize the exception
        $this->exception = $exception;
    }

    /**
     * Return's the HTTP status code that has to be send back with the response.
     *
     * @return integer The HTTP status code
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Returns the exception instance.
     *
     * @return \Exception|null The exception instance
     */
    public function getException()
    {
        return $this->exception;
    }
}
