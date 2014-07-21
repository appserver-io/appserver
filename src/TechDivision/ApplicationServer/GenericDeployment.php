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

use TechDivision\PBC\Config;
use TechDivision\PBC\AutoLoader;
use TechDivision\Storage\StackableStorage;
use TechDivision\Application\Application;
use TechDivision\Application\Interfaces\ContextInterface;
use TechDivision\ServletEngine\StandardSessionManager;
use TechDivision\ServletEngine\Authentication\StandardAuthenticationManager;
use TechDivision\ServletEngine\ServletManager;
use TechDivision\MessageQueue\QueueManager;
use TechDivision\PersistenceContainer\BeanManager;
use TechDivision\WebSocketServer\HandlerManager;
use TechDivision\ApplicationServer\AbstractDeployment;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\ApplicationServer\Api\Node\ContextNode;

/**
 * Specific deployment implementation for web applications.
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
     * Path the to default provisioning configuration.
     *
     * @var string
     */
    const DEFAULT_CONFIGURATION = '/etc/appserver.d/context.xml';

    /**
     * The default context configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContextNode
     */
    protected $context;

    /**
     * Initialize.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContextInterface $initialContext The initial context instance
     */
    public function __construct(ContextInterface $initialContext)
    {

        // invoke the parent constructor
        parent::__construct($initialContext);

        // load the default context configuration
        $this->context = new ContextNode();
        $this->context->initFromFile($this->getDeploymentService()->realpath(GenericDeployment::DEFAULT_CONFIGURATION));
    }

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container we want to add the applications to
     *
     * @return void
     */
    public function deploy(ContainerInterface $container)
    {

        // initialize the iterator for the web applications
        $iterator = new \FilesystemIterator($container->getAppBase());

        // gather all the deployed web applications
        foreach ($iterator as $folder) {

            if ($folder->isDir()) { // check if we've a directory (possible application)

                // load the specific application context or the default one
                $context = clone $this->context;

                // prepare the context path
                $contextPath = '/'. $folder->getBasename();

                // try to load a context configuration for the context path
                if ($contextToMerge = $container->getContainerNode()->getHost()->getContext($contextPath)) {
                    $context->merge($contextToMerge);
                }

                // create a new application instance
                $application = $this->newInstance($context->getType());

                // initialize the generic instances and information
                $application->injectName($folder->getBasename());
                $application->injectInitialContext($this->getInitialContext());
                $application->injectBaseDirectory($container->getBaseDirectory());
                $application->injectTmpDir($container->getTmpDir($contextPath));
                $application->injectAppBase($container->getAppBase());

                // create the applications temporary folders
                $this->createTmpFolders($application);

                // add the default class loaders
                $application->addClassLoader($this->getInitialContext()->getClassLoader());

                // add the configured class loaders
                foreach ($context->getClassLoaders() as $classLoader) {
                    $classLoaderType = $classLoader->getType();
                    $classLoaderType::get($application, $classLoader);
                }

                // add the configured managers
                foreach ($context->getManagers() as $manager) {
                    $managerType = $manager->getType();
                    $managerType::get($application);
                }

                // add the application to the container
                $container->addApplication($application);
            }
        }
    }
}
