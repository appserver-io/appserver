<?php

/**
 * \AppserverIo\Appserver\MessageQueue\QueueManagerFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface;

/**
 * A factory for the queue manager instances.
 *
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class QueueManagerFactory implements ManagerFactoryInterface
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

        // initialize the stackable containers
        $queues = new GenericStackable();
        $workers = new GenericStackable();
        $messages = new GenericStackable();

        // initialize the queue locator
        $queueLocator = new QueueLocator();

        // initialize the default settings for message queue
        $queueManagerSettings = new QueueManagerSettings();
        $queueManagerSettings->mergeWithParams($managerConfiguration->getParamsAsArray());

        // initialize the queue manager
        $queueManager = new QueueManager();
        $queueManager->injectQueues($queues);
        $queueManager->injectWorkers($workers);
        $queueManager->injectMessages($messages);
        $queueManager->injectApplication($application);
        $queueManager->injectResourceLocator($queueLocator);
        $queueManager->injectManagerSettings($queueManagerSettings);
        $queueManager->injectManagerConfiguration($managerConfiguration);

        // attach the instance
        $application->addManager($queueManager, $managerConfiguration);
    }
}
