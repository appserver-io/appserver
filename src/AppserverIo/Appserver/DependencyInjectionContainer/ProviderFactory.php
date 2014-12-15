<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\ProviderFactory
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

// ATTENTION: this is necessary for Windows
use AppserverIo\Appserver\Naming\InitialContext as NamingContext;

/**
 * The factory for the dependency injection provider.
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
class ProviderFactory
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

        // create the initial context instance
        $initialContext = new NamingContext();
        $initialContext->injectApplication($application);

        // create the storage for the reflection classes and the application specific aliases
        $reflectionClasses = new GenericStackable();
        $namingDirectoryAliases = new GenericStackable();

        // create and initialize the DI provider instance
        $provider = new Provider();
        $provider->injectNamingDirectory($application);
        $provider->injectInitialContext($initialContext);
        $provider->injectReflectionClasses($reflectionClasses);
        $provider->injectNamingDirectoryAliases($namingDirectoryAliases);

        // attach the instance
        $application->addManager($provider, $managerConfiguration);
    }
}
