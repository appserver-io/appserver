<?php

/**
 * AppserverIo\Appserver\MessageQueue\QueueManagerFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use TechDivision\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

/**
 * A factory for the queue manager instances.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class QueueManagerFactory
{

    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface          $application          The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerConfigurationInterface $managerConfiguration)
    {

        // initialize the stackable for the queues
        $queues = new GenericStackable();

        // initialize the queue locator
        $queueLocator = new QueueLocator();

        // initialize the queue manager
        $queueManager = new QueueManager();
        $queueManager->injectQueues($queues);
        $queueManager->injectWebappPath($application->getWebappPath());
        $queueManager->injectResourceLocator($queueLocator);

        // attach the instance
        $application->addManager($queueManager, $managerConfiguration);
    }
}
