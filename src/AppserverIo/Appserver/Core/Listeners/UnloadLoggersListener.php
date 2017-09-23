<?php

/**
 * AppserverIo\Appserver\Core\Listeners\UnloadLoggersListener
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

/**
 * Listener that unloads the system logger instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class UnloadLoggersListener extends AbstractSystemListener
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
            /** @var \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();

            // unbind the loggers from the naming directory
            foreach (array_keys($applicationServer->getLoggers()) as $name) {
                $applicationServer->getNamingDirectory()->unbind(sprintf('php:global/log/%s', $name));
            }

            // set the initialized loggers finally
            $applicationServer->setLoggers(array());

        } catch (\Exception $e) {
            error_log($e->__toString());
        }
    }
}
