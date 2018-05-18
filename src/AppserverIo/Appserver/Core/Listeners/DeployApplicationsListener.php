<?php

/**
 * AppserverIo\Appserver\Core\Listeners\DeployApplicationsListener
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
 * Listener that deploys the applications for each container bound to the application server.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeployApplicationsListener extends AbstractSystemListener
{

    /**
     * Handle an event.
     *
     * @param \League\Event\EventInterface $event The triggering event
     *
     * @return void
     * @see \League\Event\ListenerInterface::handle()
     */
    public function handle(EventInterface $event)
    {

        try {
            // load the application server and the naming directory instance
            /** @var \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();
            /** @var \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory */
            $namingDirectory = $applicationServer->getNamingDirectory();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // deploy the applications for all containers
            /** @var \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode */
            foreach ($applicationServer->getSystemConfiguration()->getContainers() as $containerNode) {
                // load the container instance to deploy the applications for
                /** @var \AppserverIo\Psr\ApplicationServer\ContainerInterface $container */
                $container = $namingDirectory->search(
                    sprintf(
                        'php:services/%s/%s',
                        $applicationServer->runlevelToString(ApplicationServerInterface::NETWORK),
                        $containerNode->getName()
                    )
                );

                // load the containers deployment
                /** @var \AppserverIo\Psr\Deployment\DeploymentInterface $deployment */
                $deployment = $container->getDeployment();
                $deployment->injectContainer($container);

                // deploy and initialize the container's applications
                $deployment->deploy();
            }

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
