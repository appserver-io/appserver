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
use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\LoggerFactory;
use AppserverIo\Appserver\Core\Api\Node\ParamNode;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Api\ConfigurationService;
use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
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

            // initialize configuration and schema file name
            $configurationFileName = $applicationServer->getConfigurationFilename();

            // initialize the DOMDocument with the configuration file to be validated
            $configurationFile = new \DOMDocument();
            $configurationFile->load($configurationFileName);

            // substitute xincludes
            $configurationFile->xinclude(LIBXML_SCHEMA_CREATE);

            // create a DOMElement with the base.dir configuration
            $paramElement = $configurationFile->createElement('param', APPSERVER_BP);
            $paramElement->setAttribute('name', DirectoryKeys::BASE);
            $paramElement->setAttribute('type', ParamNode::TYPE_STRING);

            // create an XPath instance
            $xpath = new \DOMXpath($configurationFile);
            $xpath->registerNamespace('a', 'http://www.appserver.io/appserver');

            // for node data in a selected id
            $baseDirParam = $xpath->query(sprintf('/a:appserver/a:params/a:param[@name="%s"]', DirectoryKeys::BASE));
            if ($baseDirParam->length === 0) {

                // load the <params> node
                $paramNodes = $xpath->query('/a:appserver/a:params');

                // load the first item => the node itself
                if ($paramsNode = $paramNodes->item(0)) {
                    // append the base.dir DOMElement
                    $paramsNode->appendChild($paramElement);
                } else {
                    // throw an exception, because we can't find a mandatory node
                    throw new \Exception('Can\'t find /appserver/params node');
                }
            }

            // create a new DOMDocument with the merge content => necessary because else, schema validation fails!!
            $mergeDoc = new \DOMDocument();
            $mergeDoc->loadXML($configurationFile->saveXML());

            // get an instance of our configuration tester
            $configurationService = new ConfigurationService(new InitialContext(new AppserverNode()));

            // validate the configuration file with the schema
            if ($configurationService->validateXml($mergeDoc) === false) {
                throw new \Exception('Can\'t parse configuration file');
            }

            // initialize the SimpleXMLElement with the content XML configuration file
            $configuration = new Configuration();
            $configuration->initFromString($mergeDoc->saveXML());

            // initialize the configuration and the base directory
            $systemConfiguration = new AppserverNode();
            $systemConfiguration->initFromConfiguration($configuration);
            $applicationServer->setSystemConfiguration($systemConfiguration);

        } catch (\Exception $e) {
            $applicationServer->getSystemLogger()->error($e->__toString());
        }
    }
}
