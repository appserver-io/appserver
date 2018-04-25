<?php

/**
 * AppserverIo\Appserver\Core\Utilities\Error
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

/**
 * Wrapper for an error triggered by PHP's default error handling.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Error implements ErrorInterface
{

    /**
     * The error type.
     *
     * @var integer
     */
    protected $type;

    /**
     * The error message.
     *
     * @var integer
     */
    protected $message;

    /**
     * The name of the file where the error has been triggered.
     *
     * @var integer
     */
    protected $file;

    /**
     * The line in the file where the error has been triggered.
     *
     * @var integer
     */
    protected $line;

    /**
     * Initializes the error with the passed values.
     *
     * @param integer $type    The error type
     * @param string  $message The error message
     * @param string  $file    The name of the file where the error has been triggered
     * @param integer $line    The line in the file where the error has been triggered
     */
    public function __construct($type, $message, $file, $line)
    {
        $this->type = $type;
        $this->message = $message;
        $this->file = $file;
        $this->line = $line;
    }

    /**
     * Return's the error type.
     *
     * @return integer The error type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return's the error message.
     *
     * @return integer The error message
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Return's the name of the file where the error has been triggered.
     *
     * @return integer The filename
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * Return's the line in the file where the error has been triggered.
     *
     * @return integer The line number
     */
    public function getLine()
    {
        return $this->line;
    }
}
