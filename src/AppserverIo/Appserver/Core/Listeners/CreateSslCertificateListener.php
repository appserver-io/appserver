<?php

/**
 * AppserverIo\Appserver\Core\Listeners\CreateSslCertificateListener
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
 * Listener that creates a SSL certificate if not already exists.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CreateSslCertificateListener extends AbstractSystemListener
{

    /**
     * The default certificate name.
     *
     * @var string
     */
    const DEFAULT_CERTIFICATE_NAME = 'server.pem';

    /**
     * Handle an event.
     *
     * @param EventInterface $event
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
            $namingDirectory->search('php:global/log/System')->info($event->getName());

            // load the service instance and create the SSL file if not available
            /** @var \AppserverIo\Appserver\Core\Api\ContainerService $service */
            $service = $applicationServer->newService('AppserverIo\Appserver\Core\Api\ContainerService');
            $service->createSslCertificate(new \SplFileInfo($service->getConfDir(CreateSslCertificateListener::DEFAULT_CERTIFICATE_NAME)));

        } catch (\Exception $e) {
            $namingDirectory->search('php:global/log/System')->error($e->__toString());
        }
    }
}
