<?php

/**
 * AppserverIo\Appserver\Core\Api\Mock\MockInitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Mock;

use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Mocked initial context.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MockInitialContext extends \AppserverIo\Appserver\Core\Mock\MockInitialContext
{

    /**
     * Constructor without handling of classloaders.
     * This allows for testing without pthreads extension
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $systemConfiguration The system configuration
     */
    public function __construct(NodeInterface $systemConfiguration)
    {
        // initialize the storage
        $initialContextNode = $systemConfiguration->getInitialContext();
        $storageNode = $initialContextNode->getStorage();
        $reflectionClass = $this->newReflectionClass($storageNode->getType());

        // create the storage instance
        $storage = $reflectionClass->newInstance();

        // append the storage servers registered in system configuration
        foreach ($storageNode->getStorageServers() as $storageServer) {
            $storage->addServer($storageServer->getAddress(), $storageServer->getPort(), $storageServer->getWeight());
        }

        // add the storage to the initial context
        $this->setStorage($storage);

        // attach the system configuration to the initial context
        $this->setSystemConfiguration($systemConfiguration);
    }
}
