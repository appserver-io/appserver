<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\ScannerFactoryInterface
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

namespace AppserverIo\Appserver\Core\Scanner;

use AppserverIo\Appserver\Core\Api\Node\ScannerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Interface for a scanner factory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ScannerFactoryInterface
{

    /**
     * Creates a new scanner instance and attaches it to the passed server instance.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $server      The server instance to add the scanner to
     * @param \AppserverIo\Appserver\Core\Api\Node\ScannerNodeInterface         $scannerNode The scanner configuration
     *
     * @return object The scanner instance
     */
    public static function visit(ApplicationServerInterface $server, ScannerNodeInterface $scannerNode);
}
