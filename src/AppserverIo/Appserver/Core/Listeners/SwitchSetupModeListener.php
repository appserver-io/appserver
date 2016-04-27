<?php

/**
 * AppserverIo\Appserver\Core\Listeners\SwitchSetupModeListener
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
use AppserverIo\Psr\Naming\NamingException;

/**
 * Listener that switches the setup mode.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SwitchSetupModeListener extends AbstractSystemListener
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
            /** @var \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();
            /** @var \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory */
            $namingDirectory = $applicationServer->getNamingDirectory();

            // load the configuration filename
            $configurationFilename = $applicationServer->getConfigurationFilename();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // load the setup mode from the naming directory
            $setupMode = $namingDirectory->search('php:env/args/s');
            $currentUser = $namingDirectory->search('php:env/currentUser');

            // load the service instance and switch to the new setup mode
            /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
            $service = $applicationServer->newService('AppserverIo\Appserver\Core\Api\ContainerService');
            $service->switchSetupMode($setupMode, $configurationFilename, $currentUser);

        } catch (NamingException $ne) {
            $applicationServer->getSystemLogger()->error('Please specify a setup mode, e. g. \'./server.php -s prod\'');
        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
