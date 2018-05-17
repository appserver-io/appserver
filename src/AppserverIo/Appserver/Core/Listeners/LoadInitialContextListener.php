<?php

/**
 * AppserverIo\Appserver\Core\Listeners\LoadInitialContextListener
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
 * Listener that loads and initializes the inital context instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoadInitialContextListener extends AbstractSystemListener
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
            // load the application server and system configuration instance
            /** @var \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();
            /** @var \AppserverIo\Psr\ApplicationServer\Configuration\SystemConfigurationInterface $systemConfiguration */
            $systemConfiguration = $applicationServer->getSystemConfiguration();

            // load the initial context configuration
            /** @var \AppserverIo\Psr\ApplicationServer\Configuration\InitialContextConfigurationInterface $initialContextNode */
            $initialContextNode = $systemConfiguration->getInitialContext();
            $reflectionClass = new \ReflectionClass($initialContextNode->getType());
            /** @var \AppserverIo\Psr\ApplicationServer\ContextInterface $initialContext */
            $initialContext = $reflectionClass->newInstanceArgs(array($systemConfiguration));

            // attach the registered loggers to the initial context
            $initialContext->setLoggers($applicationServer->getLoggers());
            $initialContext->setSystemLogger($applicationServer->getSystemLogger());

            // set the initial context and flush it initially
            $applicationServer->setInitialContext($initialContext);

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
