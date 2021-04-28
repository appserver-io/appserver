<?php

/**
 * \AppserverIo\Appserver\ServletEngine\DependencyInjection\DeploymentDescriptorParser
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

namespace AppserverIo\Appserver\ServletEngine\DependencyInjection;

use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Appserver\Core\Api\Node\WebAppNode;
use AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys;
use AppserverIo\Appserver\DependencyInjectionContainer\AbstractDeploymentDescriptorParser;

/**
 * Parser implementation to parse a web application deployment descriptor (WEB-INF/web.xml).
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
     * Parses the servlet context's deployment descriptor file for servlets
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse()
    {

        // load the application instance
        /** @var \AppserverIo\Psr\Application\ApplicationInterface $application */
        $application = $this->getApplication();

        // load the container node to initialize the system properties
        /** @var \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode */
        $containerNode = $application->getContainer()->getContainerNode();

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

            // load the system properties
            $properties = $configurationService->getSystemProperties($containerNode);

            // append the application specific properties
            $properties->add(SystemPropertyKeys::WEBAPP, $webappPath = $application->getWebappPath());
            $properties->add(SystemPropertyKeys::WEBAPP_NAME, basename($webappPath));
            $properties->add(SystemPropertyKeys::WEBAPP_DATA, $application->getDataDir());
            $properties->add(SystemPropertyKeys::WEBAPP_CACHE, $application->getCacheDir());
            $properties->add(SystemPropertyKeys::WEBAPP_SESSION, $application->getSessionDir());

            // prepare and initialize the configuration node
            $webAppNode = new WebAppNode();
            $webAppNode->initFromFile($deploymentDescriptor);
            $webAppNode->replaceProperties($properties);

            // load the object manager instance
            /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
            $objectManager = $application->search(ObjectManagerInterface::IDENTIFIER);

            /** @var \AppserverIo\Appserver\Core\Api\Node\ServletNode $servletNode */
            foreach ($webAppNode->getServlets() as $servletNode) {
                // iterate over all configured descriptors and try to load object description
                /** \AppserverIo\Appserver\Core\Api\Node\DescriptorNode $descriptor */
                foreach ($this->getDescriptors() as $descriptor) {
                    try {
                        // load the descriptor class
                        $descriptorClass = $descriptor->getNodeValue()->getValue();

                        // load the object descriptor, initialize the servlet mappings and add it to the object manager
                        /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
                        if ($objectDescriptor = $descriptorClass::newDescriptorInstance()->fromConfiguration($servletNode)) {
                            /** @var \AppserverIo\Appserver\Core\Api\Node\ServletMappingNode $servletMappingNode */
                            foreach ($webAppNode->getServletMappings() as $servletMappingNode) {
                                // query whether or not we've to add the URL pattern for the servlet
                                if ((string) $servletNode->getServletName() === (string) $servletMappingNode->getServletName()) {
                                    $objectDescriptor->addUrlPattern((string) $servletMappingNode->getUrlPattern());
                                }
                            }

                            // add the object descriptor for the servlet
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

            // initialize the session configuration if available
            /** @var \AppserverIo\Appserver\Core\Api\Node\SessionConfigNode $sessionConfig */
            if ($sessionConfig = $webAppNode->getSessionConfig()) {
                foreach ($sessionConfig->toArray() as $key => $value) {
                    $this->getManager()->addSessionParameter($key, $value);
                }
            }

            // initialize the error page configuration if available
            /** @var \AppserverIo\Appserver\Core\Api\Node\ErrorPageNode $errorPageNode */
            foreach ($webAppNode->getErrorPages() as $errorPageNode) {
                $this->getManager()->addErrorPage((string) $errorPageNode->getErrorCodePattern(), (string) $errorPageNode->getErrorLocation());
            }

            // initialize the context with the context parameters
            /** @var \AppserverIo\Appserver\Core\Api\Node\ContextParamNode $contextParamNode */
            foreach ($webAppNode->getContextParams() as $contextParamNode) {
                $this->getManager()->addInitParameter((string) $contextParamNode->getParamName(), (string) $contextParamNode->getParamValue());
            }
        }
    }
}
