<?php

/**
 * AppserverIo\Appserver\Core\GenericDeployment
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
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Appserver\Application\Application;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Appserver\Core\AbstractDeployment;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Api\Node\ContextNode;
use AppserverIo\Appserver\Naming\NamingDirectory;

/**
 * Generic deployment implementation for web applications.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The container we want to add the applications to
     *
     * @return void
     */
    public function deploy(ContainerInterface $container)
    {

        // load the mutex to lock/unlock resources during application deployment
        $mutex = $container->getMutex();

        // load the context instances for this container
        $contextInstances = $this->getDeploymentService()->loadContextInstancesByContainer($container);

        // gather all the deployed web applications
        foreach (glob($container->getAppBase() . '/*', GLOB_ONLYDIR) as $folder) {

            // declare META-INF and WEB-INF directory
            $webInfDir = $folder . DIRECTORY_SEPARATOR . 'WEB-INF';
            $metaInfDir = $folder . DIRECTORY_SEPARATOR . 'META-INF';

            // check if we've a directory containing a valid application,
            // at least a WEB-INF or META-INF folder has to be available
            if (is_dir($webInfDir) || is_dir($metaInfDir)) {

                // this IS the unique application name
                $applicationName = basename($folder);

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
                $application->injectMutex($mutex);
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
                $application->addClassLoader(
                    $this->getInitialContext()->getClassLoader(),
                    $this->getInitialContext()->getSystemConfiguration()->getInitialContext()->getClassLoader()
                );

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

            } else { // if we can't find WEB-INF or META-INF directory

                // write a log message, that the folder doesn't contain a valid application
                $this->getInitialContext()->getSystemLogger()->info(
                    sprintf('Directory %s doesn\'t contain a valid application, so we ignore it!', $folder)
                );
            }
        }
    }
}
