<?php

/**
 * AppserverIo\Appserver\WebSocketServer\HandlerManagerFactory
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

namespace AppserverIo\Appserver\WebSocketServer;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

/**
 * The handler manager handles the handlers registered for the application.
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
class HandlerManagerFactory
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

        // initialize the stackabls
        $handlers = new GenericStackable();
        $handlerMappings = new GenericStackable();
        $initParameters = new GenericStackable();

        // initialize the handler locator
        $handlerLocator = new HandlerLocator();

        // initialize the handler manager
        $handlerManager = new HandlerManager();
        $handlerManager->injectHandlers($handlers);
        $handlerManager->injectHandlerMappings($handlerMappings);
        $handlerManager->injectInitParameters($initParameters);
        $handlerManager->injectWebappPath($application->getWebappPath());
        $handlerManager->injectHandlerLocator($handlerLocator);

        // attach the instance
        $application->addManager($handlerManager, $managerConfiguration);
    }
}
