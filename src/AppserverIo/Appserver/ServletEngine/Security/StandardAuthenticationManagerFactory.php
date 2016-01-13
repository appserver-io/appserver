<?php

/**
 * \AppserverIo\Appserver\ServletEngine\Security\StandardAuthenticationManagerFactory
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;
use AppserverIo\Appserver\Core\Interfaces\ManagerFactoryInterface;

/**
 * A factory for the standard session authentication manager instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardAuthenticationManagerFactory implements ManagerFactoryInterface
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

        // initialize the storage instances
        $authenticationMethods = new StackableStorage();
        $urlPatternToAuthenticationMethodMappings = new StackableStorage();

        // initialize the authentication manager
        $authenticationManager = new StandardAuthenticationManager();
        $authenticationManager->injectApplication($application);
        $authenticationManager->injectManagerConfiguration($managerConfiguration);
        $authenticationManager->injectAuthenticationMethods($authenticationMethods);
        $authenticationManager->injectUrlPatternToAuthenticationMethodMappings($urlPatternToAuthenticationMethodMappings);

        // attach the instance
        $application->addManager($authenticationManager, $managerConfiguration);
    }
}
