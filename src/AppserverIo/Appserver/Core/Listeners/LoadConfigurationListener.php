<?php

/**
 * AppserverIo\Appserver\Core\Listeners\LoadConfigurationListener
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
use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Api\ConfigurationService;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Listener that loads and initializes the system configuration from the XML file.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoadConfigurationListener extends AbstractSystemListener
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

            // initialize configuration and schema file name
            $configurationFileName = $applicationServer->getConfigurationFilename();

            // get an instance of our configuration tester
            $configurationService = new ConfigurationService(new InitialContext(new AppserverNode()));

            // load the parsed system configuration
            /** @var \DOMDocument $doc */
            $doc = $configurationService->loadConfigurationByFilename($configurationFileName);

            // validate the configuration file with the schema
            $configurationService->validateXml($doc, null, true);

            try {
                // query whether we're in configuration test mode or not
                if ($namingDirectory->search('php:env/args/t')) {
                    echo 'Syntax OK' . PHP_EOL;
                    exit(0);
                }

            } catch (NamingException $ne) {
                // do nothing, because we're NOT in configuration test mode
            }

            // initialize the SimpleXMLElement with the content XML configuration file
            $configuration = new Configuration();
            $configuration->initFromString($doc->saveXML());

            // initialize the configuration and the base directory
            $systemConfiguration = new AppserverNode();
            $systemConfiguration->initFromConfiguration($configuration);
            $applicationServer->setSystemConfiguration($systemConfiguration);

        } catch (\Exception $e) {
            // render the validation errors and exit immediately
            echo $e . PHP_EOL;
            exit(0);
        }
    }
}
