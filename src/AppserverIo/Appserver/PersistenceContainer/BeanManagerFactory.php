<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\BeanManagerFactory
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
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

use AppserverIo\Appserver\Naming\InitialContext as NamingContext;

/**
 * The bean manager handles the message and session beans registered for the application.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class BeanManagerFactory
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

        // load the registered loggers
        $loggers = $application->getInitialContext()->getLoggers();

        // initialize the bean locator
        $beanLocator = new BeanLocator();

        // create the initial context instance
        $initialContext = new NamingContext();
        $initialContext->injectApplication($application);

        // initialize the stackable for the data, the stateful + singleton session beans and the naming directory
        $data = new StackableStorage();
        $statefulSessionBeans = new StackableStorage();
        $singletonSessionBeans = new StackableStorage();

        // initialize the default settings for the stateful session beans
        $statefulSessionBeanSettings = new DefaultStatefulSessionBeanSettings();
        $statefulSessionBeanSettings->mergeWithParams($managerConfiguration->getParamsAsArray());

        // we need a factory instance for the stateful session bean instances
        $statefulSessionBeanMapFactory = new StatefulSessionBeanMapFactory($statefulSessionBeans);
        $statefulSessionBeanMapFactory->injectLoggers($loggers);
        $statefulSessionBeanMapFactory->start();

        // initialize the bean manager
        $beanManager = new BeanManager();
        $beanManager->injectData($data);
        $beanManager->injectApplication($application);
        $beanManager->injectResourceLocator($beanLocator);
        $beanManager->injectInitialContext($initialContext);
        $beanManager->injectStatefulSessionBeans($statefulSessionBeans);
        $beanManager->injectSingletonSessionBeans($singletonSessionBeans);
        $beanManager->injectDirectories($managerConfiguration->getDirectories());
        $beanManager->injectStatefulSessionBeanSettings($statefulSessionBeanSettings);
        $beanManager->injectStatefulSessionBeanMapFactory($statefulSessionBeanMapFactory);

        // attach the instance
        $application->addManager($beanManager, $managerConfiguration);
    }
}
