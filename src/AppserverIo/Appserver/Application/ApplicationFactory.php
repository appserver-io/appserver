<?php

/**
 * AppserverIo\Appserver\Application\ApplicationFactory
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

namespace AppserverIo\Appserver\Application;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Core\Api\Node\ContextNode;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

/**
 * Application factory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ApplicationFactory
{

    /**
     * Visitor method that registers the application in the container.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The container instance bind the application to
     * @param \AppserverIo\Appserver\Core\Api\Node\ContextNode          $context   The application configuration
     *
     * @return void
     */
    public static function visit(ContainerInterface $container, ContextNode $context)
    {

        // prepare the path to the applications base directory
        $folder = $container->getAppBase() . DIRECTORY_SEPARATOR . $context->getName();

        // declare META-INF and WEB-INF directory
        $webInfDir = $folder . DIRECTORY_SEPARATOR . 'WEB-INF';
        $metaInfDir = $folder . DIRECTORY_SEPARATOR . 'META-INF';

        // check if we've a directory containing a valid application,
        // at least a WEB-INF or META-INF folder has to be available
        if (!is_dir($webInfDir) && !is_dir($metaInfDir)) {
            return;
        }

        // load the naming directory + initial context
        $initialContext = $container->getInitialContext();
        $namingDirectory = $container->getNamingDirectory();

        // load the application service
        $appService = $container->newService('AppserverIo\Appserver\Core\Api\AppService');

        // load the application type
        $contextType = $context->getType();
        $applicationName = $context->getName();

        // create a new application instance
        /** @var \AppserverIo\Appserver\Application\Application $application */
        $application = new $contextType();

        // initialize the storage for managers, virtual hosts an class loaders
        $managers = new GenericStackable();
        $provisioners = new GenericStackable();
        $classLoaders = new GenericStackable();

        // initialize the generic instances and information
        $application->injectManagers($managers);
        $application->injectName($applicationName);
        $application->injectProvisioners($provisioners);
        $application->injectClassLoaders($classLoaders);
        $application->injectInitialContext($initialContext);
        $application->injectNamingDirectory($namingDirectory);

        // prepare the application instance
        $application->prepare($context);

        // create the applications temporary folders and cleans the folders up
        /** @var \AppserverIo\Appserver\Core\Api\AppService $appService */
        $appService->createTmpFolders($application);
        $appService->cleanUpFolders($application);

        // add the configured class loaders
        /** @var \AppserverIo\Appserver\Core\Api\Node\ClassLoaderNode $classLoader */
        foreach ($context->getClassLoaders() as $classLoader) {
            /** @var \AppserverIo\Appserver\Core\Interfaces\ClassLoaderFactoryInterface $classLoaderFactory */
            if ($classLoaderFactory = $classLoader->getFactory()) {
                // use the factory if available
                $classLoaderFactory::visit($application, $classLoader);
            } else {
                // if not, try to instanciate the class loader directly
                $classLoaderType = $classLoader->getType();
                $application->addClassLoader(new $classLoaderType($classLoader), $classLoader);
            }
        }

        // add the configured managers
        /** @var \AppserverIo\Appserver\Core\Api\Node\ManagerNode $manager */
        foreach ($context->getManagers() as $manager) {
            if ($managerFactory = $manager->getFactory()) {
                // use the factory if available
                $managerFactory::visit($application, $manager);
            } else {
                // if not, try to instanciate the manager directly
                $managerType = $manager->getType();
                $application->addManager(new $managerType($manager), $manager);
            }
        }

        // add the configured provisioners
        /** @var \AppserverIo\Appserver\Core\Api\Node\ProvisionerNode $provisioner */
        foreach ($context->getProvisioners() as $provisioner) {
            if ($provisionerFactory = $provisioner->getFactory()) {
                // use the factory if available
                $provisionerFactory::visit($application, $provisioner);
            } else {
                // if not, try to instanciate the provisioner directly
                $provisionerType = $provisioner->getType();
                $application->addProvisioner(new $provisionerType($provisioner), $provisioner);
            }
        }

        // add the application to the container
        $container->addApplication($application);
    }
}
