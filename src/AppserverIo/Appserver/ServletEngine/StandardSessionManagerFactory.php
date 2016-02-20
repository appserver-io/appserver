<?php

/**
 * \AppserverIo\Appserver\ServletEngine\StandardSessionManagerFactory
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Collections\HashMap;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface;
use AppserverIo\Appserver\ServletEngine\Session\FilesystemSessionHandler;

/**
 * A factory for the standard session manager instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardSessionManagerFactory implements ManagerFactoryInterface
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

        // initialize the sessions and the session settings
        $sessionHandlers = new HashMap();
        $sessionSettings = new DefaultSessionSettings();
        $sessionMarshaller = new StandardSessionMarshaller();

        // add the configured session handlers
        /** @var \AppserverIo\Appserver\Core\Api\Node\SessionHandlerNode $sessionHandlerNode */
        foreach ($managerConfiguration->getSessionHandlers() as $sessionHandlerNode) {
            if ($factory = $sessionHandlerNode->getFactory()) {
                $sessionHandlers->add(
                    $sessionHandlerNode->getName(),
                    $factory::create($sessionHandlerNode, $sessionSettings, $sessionMarshaller)
                );
            }
        }

        // we need a garbage collector
        $garbageCollector = new StandardGarbageCollector();
        $garbageCollector->injectApplication($application);
        $garbageCollector->injectSessionSettings($sessionSettings);
        $garbageCollector->start();

        // and finally we need the session manager instance
        $sessionManager = new StandardSessionManager();
        $sessionManager->injectApplication($application);
        $sessionManager->injectSessionSettings($sessionSettings);
        $sessionManager->injectSessionHandlers($sessionHandlers);
        $sessionManager->injectGarbageCollector($garbageCollector);
        $sessionManager->injectSessionMarshaller($sessionMarshaller);
        $sessionManager->injectManagerConfiguration($managerConfiguration);

        // attach the instance
        $application->addManager($sessionManager, $managerConfiguration);
    }
}
