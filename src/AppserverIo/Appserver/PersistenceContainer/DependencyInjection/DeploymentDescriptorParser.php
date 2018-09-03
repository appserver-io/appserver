<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\DependencyInjection\DeploymentDescriptorParser
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

namespace AppserverIo\Appserver\PersistenceContainer\DependencyInjection;

use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Appserver\Core\Api\Node\EpbNode;
use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Appserver\DependencyInjectionContainer\AbstractDeploymentDescriptorParser;

/**
 * Parser to parse a deployment descriptor for beans.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeploymentDescriptorParser extends AbstractDeploymentDescriptorParser
{

    /**
     * Parses the bean context's deployment descriptor file for beans
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse()
    {

        // load the deployment descriptors that has to be parsed
        $deploymentDescriptors = $this->loadDeploymentDescriptors();

        // parse the deployment descriptors from the conf.d and the application's META-INF directory
        foreach ($deploymentDescriptors as $deploymentDescriptor) {
            // query whether we found epb.xml deployment descriptor file
            if (file_exists($deploymentDescriptor) === false) {
                continue;
            }

            // validate the passed configuration file
            /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
            $configurationService = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
            $configurationService->validateFile($deploymentDescriptor, null, true);

            // prepare and initialize the configuration node
            $epbNode = new EpbNode();
            $epbNode->initFromFile($deploymentDescriptor);

            // query whether or not the deployment descriptor contains any beans
            /** @var \AppserverIo\Appserver\Core\Api\Node\EnterpriseBeansNode $enterpriseBeans */
            if ($enterpriseBeans = $epbNode->getEnterpriseBeans()) {
                // parse the session beans of the deployment descriptor
                /** @var \AppserverIo\Appserver\Core\Api\Node\SessionNode $sessionNode */
                foreach ($enterpriseBeans->getSessions() as $sessionNode) {
                    $this->processConfigurationNode($sessionNode);
                }
                // parse the message driven beans from the deployment descriptor
                /** @var \AppserverIo\Appserver\Core\Api\Node\MessageDrivenNode $messageDrivenNode */
                foreach ($enterpriseBeans->getMessageDrivens() as $messageDrivenNode) {
                    $this->processConfigurationNode($messageDrivenNode);
                }
            }
        }
    }

    /**
     * Creates a new descriptor instance from the data of the passed configuration node
     * and add's it to the object manager.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $node The node to process
     *
     * @return void
     */
    protected function processConfigurationNode(NodeInterface $node)
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // iterate over all configured descriptors and try to load object description
        /** \AppserverIo\Appserver\Core\Api\Node\DescriptorNode $descriptor */
        foreach ($this->getDescriptors() as $descriptor) {
            try {
                // load the descriptor class
                $descriptorClass = $descriptor->getNodeValue()->getValue();

                // load the object descriptor, initialize the servlet mappings and add it to the object manager
                /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
                if ($objectDescriptor = $descriptorClass::newDescriptorInstance()->fromConfiguration($node)) {
                    $objectManager->addObjectDescriptor($objectDescriptor, true);
                }

                // proceed with the next descriptor
                continue;

                // if class can not be reflected continue with next class
            } catch (\Exception $e) {
                // log an error message
                $this->getApplication()->getInitialContext()->getSystemLogger()->error($e->__toString());

                // proceed with the next descriptor
                continue;
            }
        }
    }
}
