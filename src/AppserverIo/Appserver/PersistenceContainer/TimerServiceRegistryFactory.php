<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\TimerServiceRegistryFactory
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
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;
use AppserverIo\Storage\GenericStackable;

/**
 * A factory for the timer service registry instances.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class TimerServiceRegistryFactory
{

    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface                           $application          The application instance to register the class loader with
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerConfigurationInterface $managerConfiguration)
    {

        // initialize the service locator
        $serviceLocator = new ServiceLocator();

        // initialize the stackable for the data, the services and the scheduled timer tasks
        $data = new StackableStorage();
        $services = new StackableStorage();
        $tasksToExecute = new GenericStackable();
        $scheduledTimers = new GenericStackable();

        // initialize the executor for the scheduled timer tasks
        $timerServiceExecutor = new TimerServiceExecutor();
        $timerServiceExecutor->injectApplication($application);
        $timerServiceExecutor->injectTasksToExecute($tasksToExecute);
        $timerServiceExecutor->injectScheduledTimers($scheduledTimers);
        $timerServiceExecutor->start();

        // initialize the service registry
        $serviceRegistry = new TimerServiceRegistry();
        $serviceRegistry->injectData($data);
        $serviceRegistry->injectServices($services);
        $serviceRegistry->injectServiceLocator($serviceLocator);
        $serviceRegistry->injectWebappPath($application->getWebappPath());
        $serviceRegistry->injectTimerServiceExecutor($timerServiceExecutor);

        // attach the instance
        $application->addManager($serviceRegistry, $managerConfiguration);
    }
}
