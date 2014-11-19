<?php

/**
 * TechDivision\ApplicationServer\GenericDeployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer;

use Rhumsaa\Uuid\Uuid;
use TechDivision\PBC\Config;
use TechDivision\Storage\GenericStackable;
use TechDivision\Storage\StackableStorage;
use TechDivision\Application\Application;
use TechDivision\Application\Interfaces\ContextInterface;
use TechDivision\ApplicationServer\AbstractDeployment;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\ApplicationServer\Api\Node\ContextNode;
use TechDivision\Naming\NamingDirectory;

/**
 * Generic deployment implementation for web applications.
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2014 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container we want to add the applications to
     *
     * @return void
     */
    public function deploy(ContainerInterface $container)
    {

        // load the context instances for this container
        $contextInstances = $this->getDeploymentService()->loadContextInstancesByContainer($container);

        // gather all the deployed web applications
        foreach (new \FilesystemIterator($container->getAppBase()) as $folder) {

            if ($folder->isDir()) { // check if we've a directory (possible application)

                // this IS the unique application name
                $applicationName = $folder->getBasename();

                // try to load a context configuration for the context path
                $context = $contextInstances['/'. $applicationName];

                // create a new application instance
                $application = $this->newInstance($context->getType());

                // initialize the storage for managers, virtual hosts an class loaders
                $data = new StackableStorage();
                $managers = new GenericStackable();
                $virtualHosts = new GenericStackable();
                $classLoaders = new GenericStackable();

                // bind the application specific temporary directory to the naming directory
                $namingDirectory = $container->getNamingDirectory();

                // initialize the generic instances and information
                $application->injectData($data);
                $application->injectManagers($managers);
                $application->injectName($applicationName);
                $application->injectVirtualHosts($virtualHosts);
                $application->injectClassLoaders($classLoaders);
                $application->injectNamingDirectory($namingDirectory);
                $application->injectInitialContext($this->getInitialContext());

                // bind the application (which is also a naming directory)
                $globalDir = $namingDirectory->search('php:global');
                $globalDir->bind($applicationName, $application);

                // register the applications temporary directory in the naming directory
                $tmpDirectory = sprintf('%s/%s', $namingDirectory->search('env/tmpDirectory'), $applicationName);
                list ($envDir, ) = $namingDirectory->getAttribute('env');
                $envAppDir = $envDir->createSubdirectory($applicationName);
                $envAppDir->bind('tmpDirectory', $tmpDirectory);

                // create the applications temporary folders and cleans the folders up
                $this->getDeploymentService()->createTmpFolders($application);
                $this->getDeploymentService()->cleanUpFolders($application);

                // add the default class loader
                $application->addClassLoader($this->getInitialContext()->getClassLoader());

                // add the configured class loaders
                foreach ($context->getClassLoaders() as $classLoader) {
                    if ($classLoaderFactory = $classLoader->getFactory()) { // use the factory if available
                        $classLoaderFactory::visit($application, $classLoader);
                    } else { // if not, try to instanciate the class loader directly
                        $classLoaderType = $classLoader->getType();
                        $application->addClassLoader(new $classLoaderType($classLoader), $classLoader);
                    }
                }

                // add the configured managers
                foreach ($context->getManagers() as $manager) {
                    if ($managerFactory = $manager->getFactory()) { // use the factory if available
                        $managerFactory::visit($application, $manager);
                    } else { // if not, try to instanciate the manager directly
                        $managerType = $manager->getType();
                        $application->addManager(new $managerType($manager), $manager);
                    }
                }

                // add the application to the container
                $container->addApplication($application);
            }
        }
    }
}
