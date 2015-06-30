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
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

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
     * @param \League\Event\EventInterface $event The triggering event
     *
     * @return void
     * @see \League\Event\ListenerInterface::handle()
     */
    public function handle(EventInterface $event)
    {

        try {
            // load the application server instance
            $applicationServer = $this->getApplicationServer();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // and initialize a container thread for each container
            /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode */
            foreach ($applicationServer->getSystemConfiguration()->getContainers() as $containerNode) {
                /** @var \AppserverIo\Appserver\Core\Interfaces\ContainerFactoryInterface $containerFactory */
                $containerFactory = $containerNode->getFactory();

                // use the factory if available
                /** @var \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container */
                $container = $containerFactory::factory($applicationServer, $containerNode);
                $container->start();

                // register the container as service
                $applicationServer->bindService(ApplicationServerInterface::NETWORK, $container);
            }

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
