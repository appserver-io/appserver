<?php

/**
 * AppserverIo\Appserver\Core\Listeners\StartContainersListener
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

namespace AppserverIo\Appserver\Core\Listeners;

use League\Event\EventInterface;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * Listener that initializes and binds the containers found in the system configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StartContainersListener extends AbstractSystemListener
{

    /**
     * Handle an event.
     *
     * @param \League\Event\EventInterface $event    The triggering event
     * @param integer                      $runlevel The actual runlevel
     *
     * @return void
     * @see \League\Event\ListenerInterface::handle()
     */
    public function handle(EventInterface $event, $runlevel = ApplicationServerInterface::NETWORK)
    {

        try {
            // load the application server instance
            /** @var \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // initialize the service to load the container configurations
            /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $deploymentService */
            $deploymentService = $this->getDeploymentService();
            $applicationServer->setSystemConfiguration($systemConfiguration = $deploymentService->loadContainerInstances());

            // we also have to re-attach the system configuration to the initial context, because it's not a \Stackable
            /** @var \AppserverIo\Appserver\Application\Interfaces\ContextInterface */
            $initialContext = $applicationServer->getInitialContext();
            $initialContext->setSystemConfiguration($systemConfiguration);
            $applicationServer->setInitialContext($initialContext);

            // load the naming directory
            /** @var \AppserverIo\Appserver\Naming\NamingDirectory $namingDirectory */
            $namingDirectory = $applicationServer->getNamingDirectory();

            // initialize the environment variables
            $namingDirectory->bind('php:env/baseDirectory', $deploymentService->getBaseDirectory());
            $namingDirectory->bind('php:env/tmpDirectory', $deploymentService->getSystemTmpDir());
            $namingDirectory->bind('php:env/vendorDirectory', $deploymentService->getVendorDir());
            $namingDirectory->bind('php:env/umask', $applicationServer->getSystemConfiguration()->getUmask());
            $namingDirectory->bind('php:env/user', $applicationServer->getSystemConfiguration()->getUser());
            $namingDirectory->bind('php:env/group', $applicationServer->getSystemConfiguration()->getGroup());

            // and initialize a container thread for each container
            /** @var \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode */
            foreach ($applicationServer->getSystemConfiguration()->getContainers() as $containerNode) {
                // load the factory class name
                /** @var \AppserverIo\Appserver\Core\Interfaces\ContainerFactoryInterface $containerFactory */
                $containerFactory = $containerNode->getFactory();
                // use the factory to create a new container instance
                /** @var \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container */
                $container = $containerFactory::factory($applicationServer, $containerNode, $runlevel);
                $container->start();

                // wait until all servers has been bound to their ports and addresses
                while ($container->hasServersStarted() === false) {
                    // sleep to avoid cpu load
                    usleep(10000);
                }

                // register the container as service
                $applicationServer->bindService($runlevel, $container);
            }

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }

    /**
     * Return's the system configuration with the initialized container
     * node instances.
     *
     * @return \AppserverIo\Appserver\Core\Api\DeploymentService The deployment service
     */
    protected function getDeploymentService()
    {
        return $this->getApplicationServer()->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
    }
}
