<?php

/**
 * AppserverIo\Appserver\Core\Listeners\ShutdownApplicationsListener
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
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Listener that shutdown the applications for each container bound to the application server.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ShutdownApplicationsListener extends AbstractSystemListener
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
            $applicationServer = $this->getApplicationServer();
            $namingDirectory = $applicationServer->getNamingDirectory();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // deploy the applications for all containers
            /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode */
            foreach ($applicationServer->getSystemConfiguration()->getContainers() as $containerNode) {
                // load the container instance to deploy the applications for
                /** @var \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container */
                $container = $namingDirectory->search(
                    sprintf(
                        'php:services/%s/%s',
                        $applicationServer->runlevelToString(ApplicationServerInterface::NETWORK),
                        $containerNode->getName()
                    )
                );

                // iterate over all applications and shut them down
                /** @var \AppserverIo\Psr\Application\ApplicationInterface $application */
                foreach ($container->getApplications() as $application) {
                    $application->stop();
                }
            }

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
