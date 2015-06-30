<?php

/**
 * AppserverIo\Appserver\Core\Listeners\ExtractArchivesListener
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
 * Listener that extracts all application archives found in the deployment directory.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ExtractArchivesListener extends AbstractSystemListener
{

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
            // load the application server instance
            $applicationServer = $this->getApplicationServer();

            // write a log message that the event has been invoked
            $applicationServer->getSystemLogger()->info($event->getName());

            // add the configured extractors to the internal array
            /** @var \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface $extractorNode */
            foreach ($applicationServer->getSystemConfiguration()->getExtractors() as $extractorNode) {

                /** @var \AppserverIo\Appserver\Core\Extractors\ExtractorFactoryInterface $extractorFactory */
                $extractorFactory = $extractorNode->getFactory();

                // use the factory if available
                /** @var \AppserverIo\Appserver\Core\Interfaces\ConsoleInterface $console */
                $extractor = $extractorFactory::factory($applicationServer, $extractorNode);

                // deploy the found application archives
                $extractor->deployWebapps();

                // log that the extractor has successfully been initialized and executed
                $applicationServer->getSystemLogger()->debug(
                    sprintf('Extractor %s successfully initialized and executed', $extractorNode->getName())
                );
            }

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
