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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

/**
 * Generic deployment implementation for web applications.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Array containing the application specific 'env' naming directories.
     *
     * @var array
     */
    protected $envAppDirs = array();

    /**
     * Initializes the available applications and adds them to the container.
     *
     * @return void
     * @see \AppserverIo\Psr\Deployment\DeploymentInterface::deploy()
     */
    public function deploy()
    {

        // load the container and initial context instance
        $container = $this->getContainer();
        $initialContext = $container->getInitialContext();

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
                $classLoaders = new GenericStackable();

                // bind the application specific temporary directory to the naming directory
                $namingDirectory = $container->getNamingDirectory();

                // initialize the generic instances and information
                $application->injectData($data);
                $application->injectManagers($managers);
                $application->injectName($applicationName);
                $application->injectClassLoaders($classLoaders);
                $application->injectInitialContext($initialContext);
                $application->injectNamingDirectory($namingDirectory);

                // bind the application (which is also a naming directory)
                $globalDir = $namingDirectory->search('php:global');
                $globalDir->bind($applicationName, $application);

                // prepare the application specific directories
                $webappPath = sprintf('%s/%s', $namingDirectory->search('env/appBase'), $applicationName);
                $tmpDirectory = sprintf('%s/%s', $namingDirectory->search('env/tmpDirectory'), $applicationName);
                $cacheDirectory = sprintf('%s/%s', $tmpDirectory, ltrim($context->getParam(DirectoryKeys::CACHE, '/')));
                $sessionDirectory = sprintf('%s/%s', $tmpDirectory, ltrim($context->getParam(DirectoryKeys::SESSION, '/')));

                // register the applications temporary directory in the naming directory
                list ($envDir, ) = $namingDirectory->getAttribute('env');
                $this->envAppDirs[$applicationName] = $envDir->createSubdirectory($applicationName);
                $this->envAppDirs[$applicationName]->bind('webappPath', $webappPath);
                $this->envAppDirs[$applicationName]->bind('tmpDirectory', $tmpDirectory);
                $this->envAppDirs[$applicationName]->bind('cacheDirectory', $cacheDirectory);
                $this->envAppDirs[$applicationName]->bind('sessionDirectory', $sessionDirectory);

                // create the applications temporary folders and cleans the folders up
                $this->getDeploymentService()->createTmpFolders($application);
                $this->getDeploymentService()->cleanUpFolders($application);

                // add the default class loader
                $application->addClassLoader(
                    $initialContext->getClassLoader(),
                    $initialContext->getSystemConfiguration()->getInitialContext()->getClassLoader()
                );

                // add the configured class loaders
                foreach ($context->getClassLoaders() as $classLoader) {
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

                // add the application to the container
                $container->addApplication($application);

            // if we can't find WEB-INF or META-INF directory
            } else {
                // write a log message, that the folder doesn't contain a valid application
                $initialContext->getSystemLogger()->info(
                    sprintf('Directory %s doesn\'t contain a webapp, will assume a need for legacy support.', $folder)
                );
            }
        }
    }
}
