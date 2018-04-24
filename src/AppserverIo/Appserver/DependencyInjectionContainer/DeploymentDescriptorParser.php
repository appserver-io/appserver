<?php

/**
 * \AppserverIo\Appserver\DependencyInjectionContainer\DeploymentDescriptorParser
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

namespace AppserverIo\Appserver\DependencyInjectionContainer;

use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Appserver\Core\Api\Node\DiNode;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Parser to parse a deployment descriptor for beans.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeploymentDescriptorParser
{

    /**
     * The object manager we want to parse the deployment descriptor for.
     *
     * @var \AppserverIo\Psr\Di\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * Inject the object manager instance.
     *
     * @param \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager The object manager instance
     *
     * @return void
     */
    public function injectObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns the object manager instance.
     *
     * @return \AppserverIo\Psr\Di\ObjectManagerInterface The object manager instance
     */
    public function getObjectManager()
    {
        return $this->objectManager;
    }

    /**
     * Returns the application context instance the bean context is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application context instance
     */
    public function getApplication()
    {
        return $this->getObjectManager()->getApplication();
    }

    /**
     * Parses the bean context's deployment descriptor file for beans
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse()
    {

        // load the web application base directory
        $webappPath = $this->getApplication()->getWebappPath();

        // load the deployment service
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $deploymentService */
        $deploymentService = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\DeploymentService');

        // prepare the array with the deployment descriptor with the fallback deployment descriptor
        $deploymentDescriptors = array($deploymentService->getConfdDir('di.xml'));

        // try to locate deployment descriptors in the configured directories
        foreach ($this->getObjectManager()->getDirectories() as $directory) {
            array_push($deploymentDescriptors, sprintf('%s/di.xml', $directory));
        }

        // finally append the deployment descriptor of the application
        // (this allows to overwrite all other deployment descriptors)
        array_push(
            $deploymentDescriptors,
            AppEnvironmentHelper::getEnvironmentAwareGlobPattern($webappPath, 'META-INF' . DIRECTORY_SEPARATOR . 'di')
        );

        // parse the deployment descriptors from the conf.d and the application's META-INF directory
        foreach ($deploymentDescriptors as $deploymentDescriptor) {
            // query whether we found epb.xml deployment descriptor file
            if (file_exists($deploymentDescriptor) === false) {
                return;
            }

            // validate the passed configuration file
            /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
            $configurationService = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
            $configurationService->validateFile($deploymentDescriptor, null, true);

            // prepare and initialize the configuration node
            $diNode = new DiNode();
            $diNode->initFromFile($deploymentDescriptor);

            // query whether or not the deployment descriptor contains any preferences
            if ($preferences = $diNode->getPreferences()) {
                // parse the preferences of the deployment descriptor
                /** @var \AppserverIo\Description\Api\Node\PreferenceNode $preferenceNode */
                foreach ($preferences as $preferenceNode) {
                    $this->processPreferenceNode($preferenceNode);
                }
            }

            // query whether or not the deployment descriptor contains any beans
            if ($beans = $diNode->getBeans()) {
                // parse the beans from the deployment descriptor
                /** @var \AppserverIo\Description\Api\Node\BeanNode $beanNode */
                foreach ($beans as $beanNode) {
                    $this->processBeanNode($beanNode);
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
    protected function processPreferenceNode(NodeInterface $node)
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // iterate over all configured descriptors and try to load object description
        /** \AppserverIo\Appserver\Core\Api\Node\DescriptorNode $descriptor */
        foreach ($objectManager->getConfiguredDescriptors() as $descriptor) {
            try {
                // load the descriptor class
                $descriptorClass = $descriptor->getNodeValue()->getValue();

                // load the object descriptor, initialize the servlet mappings and add it to the object manager
                /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
                if ($objectDescriptor = $descriptorClass::newDescriptorInstance()->fromConfiguration($node)) {
                    $objectManager->addPreference($objectDescriptor, true);
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

    /**
     * Creates a new descriptor instance from the data of the passed configuration node
     * and add's it to the object manager.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $node The node to process
     *
     * @return void
     */
    protected function processBeanNode(NodeInterface $node)
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // iterate over all configured descriptors and try to load object description
        /** \AppserverIo\Appserver\Core\Api\Node\DescriptorNode $descriptor */
        foreach ($objectManager->getConfiguredDescriptors() as $descriptor) {
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
