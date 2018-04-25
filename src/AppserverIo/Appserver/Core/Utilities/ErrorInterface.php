<?php

/**
 * AppserverIo\Appserver\Core\Utilities\ErrorInterface
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
 * Interface for wrapper implementations of errors triggered by PHP's default error handling.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ErrorInterface
{

    /**
     * Return's the error type.
     *
     * @return integer The error type
     */
    public function getType();

    /**
     * Return's the error message.
     *
     * @return integer The error message
     */
    public function getMessage();

    /**
     * Return's the name of the file where the error has been triggered.
     *
     * @return integer The filename
     */
    public function getFile();

    /**
     * Return's the line in the file where the error has been triggered.
     *
     * @return integer The line number
     */
    public function getLine();
}
