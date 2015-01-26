<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\ScannerFactory
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Scanner;

use AppserverIo\Appserver\Core\Api\Node\ScannerNodeInterface;
use AppserverIo\Appserver\Core\Api\Node\DirectoryNodeInterface;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;

/**
 * Scanner factory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ScannerFactory
{

    /**
     * Creates a new scanner instance based on the passed configuration.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext The initial context instance
     * @param \AppserverIo\Appserver\Core\Api\Node\DirectoryNodeInterface    $directoryNode  The directory node configuration
     * @param \AppserverIo\Appserver\Core\Api\Node\ScannerNodeInterface      $scannerNode    The scanner configuration
     *
     * @return object The scanner instance
     */
    public static function factory(ContextInterface $initialContext, DirectoryNodeInterface $directoryNode, ScannerNodeInterface $scannerNode)
    {

        // load the reflection class for the scanner type
        $reflectionClass = new \ReflectionClass($scannerNode->getType());

        // prepare the scanner params
        $scannerParams = array($initialContext, $directoryNode->getNodeValue()->__toString());
        $scannerParams = array_merge($scannerParams, $scannerNode->getParamsAsArray());

        // create and return a new instance
        return $reflectionClass->newInstanceArgs($scannerParams);
    }
}
