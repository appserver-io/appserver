<?php

/**
 * AppserverIo\Appserver\Core\Listeners\LoadLoggersListener
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
use AppserverIo\Appserver\Core\LoggerFactory;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Listener that loads and initializes the system logger instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoadLoggersListener extends AbstractSystemListener
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
            // load the application server, naming directory and system configuration instance
            /** @var \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer */
            $applicationServer = $this->getApplicationServer();
            /** @var \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory */
            $namingDirectory = $applicationServer->getNamingDirectory();
            /** @var \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration */
            $systemConfiguration = $applicationServer->getSystemConfiguration();

            // initialize the loggers
            $loggers = array();
            foreach ($systemConfiguration->getLoggers() as $loggerNode) {
                $loggers[$loggerNode->getName()] = LoggerFactory::factory($loggerNode);
            }

            // register the logger callbacks in the naming directory
            foreach ($loggers as $name => $logger) {
                $namingDirectory->bind(sprintf('php:global/log/%s', $name), $logger);
            }

            // set the initialized loggers finally
            $applicationServer->setLoggers($loggers);

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
