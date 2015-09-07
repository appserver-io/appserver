<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory
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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;
use AppserverIo\Psr\Naming\InitialContext as NamingContext;

/**
 * The bean manager handles the message and session beans registered for the application.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class BeanManagerFactory implements ManagerFactoryInterface
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

        // load the registered loggers
        $loggers = $application->getInitialContext()->getLoggers();

        // initialize the bean locator
        $beanLocator = new BeanLocator();

        // create the initial context instance
        $initialContext = new NamingContext();
        $initialContext->injectApplication($application);

        // initialize the stackable for the data, the stateful + singleton session beans and the naming directory
        $data = new StackableStorage();
        $instances = new GenericStackable();
        $singletonSessionBeans = new StackableStorage();
        $statefulSessionBeans = new StatefulSessionBeanMap();

        // initialize the default settings for the stateful session beans
        $statefulSessionBeanSettings = new DefaultStatefulSessionBeanSettings();
        $statefulSessionBeanSettings->mergeWithParams($managerConfiguration->getParamsAsArray());

        // create an instance of the object factory
        $objectFactory = new GenericObjectFactory();
        $objectFactory->injectInstances($instances);
        $objectFactory->injectApplication($application);
        $objectFactory->start();

        // add a garbage collector and timer service workers for each application
        $garbageCollector = new StandardGarbageCollector();
        $garbageCollector->injectApplication($application);
        $garbageCollector->start();

        // initialize the bean manager
        $beanManager = new BeanManager();
        $beanManager->injectData($data);
        $beanManager->injectApplication($application);
        $beanManager->injectResourceLocator($beanLocator);
        $beanManager->injectObjectFactory($objectFactory);
        $beanManager->injectInitialContext($initialContext);
        $beanManager->injectGarbageCollector($garbageCollector);
        $beanManager->injectStatefulSessionBeans($statefulSessionBeans);
        $beanManager->injectSingletonSessionBeans($singletonSessionBeans);
        $beanManager->injectDirectories($managerConfiguration->getDirectories());
        $beanManager->injectStatefulSessionBeanSettings($statefulSessionBeanSettings);

        // attach the instance
        $application->addManager($beanManager, $managerConfiguration);
    }
}
