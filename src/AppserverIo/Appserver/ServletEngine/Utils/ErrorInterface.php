<?php

/**
 * AppserverIo\Appserver\ServletEngine\Utils\ErrorInterface
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
 * Interface for wrapper implementations of errors triggered by PHP's default error handling.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ErrorInterface extends \AppserverIo\Appserver\Core\Utilities\ErrorInterface
{

    /**
     * Return's the HTTP status code that has to be send back with the response.
     *
     * @return integer The HTTP status code
     */
    public function getStatusCode();

    /**
     * Returns the exception instance.
     *
     * @return \Exception|null The exception instance
     */
    public function getException();
}
