<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\StandardScannerFactory
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

use AppserverIo\Appserver\Core\Interfaces\ServerInterface;
use AppserverIo\Appserver\Core\Api\Node\ScannerNodeInterface;

/**
 * Standard scanner factory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardScannerFactory implements ScannerFactoryInterface
{

    /**
     * Creates a new scanner instance and attaches it to the passed server instance.
     *
     * @param \AppserverIo\Appserver\Core\ServerInterface               $server      The server instance to add the scanner to
     * @param \AppserverIo\Appserver\Core\Api\Node\ScannerNodeInterface $scannerNode The scanner configuration
     *
     * @return object The scanner instance
     */
    public static function visit(ServerInterface $server, ScannerNodeInterface $scannerNode)
    {

        // load the initial context instance
        /** @var \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext */
        $initialContext = $server->getInitialContext();

        // load the reflection class for the scanner type
        $reflectionClass = new \ReflectionClass($scannerNode->getType());

        // prepare the scanner params
        $scannerParams = array($initialContext);
        $scannerParams = array_merge($scannerParams, $scannerNode->getParamsAsArray());

        error_log(print_r($scannerParams, true));

        // create and return a new instance
        $server->addScanner($reflectionClass->newInstanceArgs($scannerParams));
    }
}
