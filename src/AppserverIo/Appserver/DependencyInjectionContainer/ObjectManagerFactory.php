<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\ObjectManagerFactory
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;

/**
 * The factory for the object manager.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ObjectManagerFactory
{

    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface         $application          The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerNodeInterface $managerConfiguration)
    {

        // load the configured descriptors from the configuration
        $configuredDescriptors = $managerConfiguration->getDescriptors();

        // create the storage for the data and the bean descriptors
        $data = new StackableStorage();
        $objectDescriptors = new StackableStorage();

        // create and initialize the object manager instance
        $objectManager = new ObjectManager();
        $objectManager->injectData($data);
        $objectManager->injectApplication($application);
        $objectManager->injectObjectDescriptors($objectDescriptors);
        $objectManager->injectConfiguredDescriptors($configuredDescriptors);

        // attach the instance
        $application->addManager($objectManager, $managerConfiguration);
    }
}
