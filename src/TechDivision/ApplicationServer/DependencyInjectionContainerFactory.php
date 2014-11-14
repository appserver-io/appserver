<?php

/**
 * TechDivision\ApplicationServer\DependencyInjectionContainer
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Storage\GenericStackable;
use TechDivision\Application\Interfaces\ApplicationInterface;
use TechDivision\Application\Interfaces\ManagerConfigurationInterface;
use TechDivision\Naming\InitialContext as NamingContext; // ATTENTION: this is necessary for Windows

/**
 * The factory for the dependency injection container.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class DependencyInjectionContainerFactory
{

    /**
     * The main method that creates new instances in a separate context.
     *
     * @param \TechDivision\Application\Interfaces\ApplicationInterface          $application          The application instance to register the class loader with
     * @param \TechDivision\Application\Interfaces\ManagerConfigurationInterface $managerConfiguration The manager configuration
     *
     * @return void
     */
    public static function visit(ApplicationInterface $application, ManagerConfigurationInterface $managerConfiguration)
    {

        // bind the application specific temporary directory to the naming directory
        $namingDirectory = $application->getNamingDirectory();

        // create the initial context instance
        $initialContext = new NamingContext();
        $initialContext->injectApplication($application);

        // create the application specific aliases
        $namingDirectoryAliases = new GenericStackable();

        // create and initialize the DI container instance
        $dependencyInjectionContainer = new DependencyInjectionContainer();
        $dependencyInjectionContainer->injectInitialContext($initialContext);
        $dependencyInjectionContainer->injectNamingDirectory($namingDirectory);
        $dependencyInjectionContainer->injectNamingDirectoryAliases($namingDirectoryAliases);

        // attach the instance
        $application->addManager($dependencyInjectionContainer);
    }
}
