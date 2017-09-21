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

use AppserverIo\Appserver\Core\Api\Node\WebAppNode;
use AppserverIo\Psr\Servlet\ServletContextInterface;

/**
 * Parser implementation to parse a web application deployment descriptor (WEB-INF/web.xml).
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
     * The servlet context we want to parse the deployment descriptor for.
     *
     * @var \AppserverIo\Psr\Servlet\ServletContextInterface
     */
    protected $servletContext;

    /**
     * Inject the servlet context instance.
     *
     * @param \AppserverIo\Psr\Servlet\ServletContextInterface $servletContext The servlet context instance
     *
     * @return void
     */
    public function injectServletContext(ServletContextInterface $servletContext)
    {
        $this->servletContext = $servletContext;
    }

    /**
     * Returns the servlet context instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContextInterface The servlet context instance
     */
    public function getServletContext()
    {
        return $this->servletContext;
    }

    /**
     * Returns the application context instance the servlet context is bound to.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application context instance
     */
    public function getApplication()
    {
        return $this->getServletContext()->getApplication();
    }

    /**
     * Parses the servlet context's deployment descriptor file for servlets
     * that has to be registered in the object manager.
     *
     * @return void
     */
    public function parse()
    {

        // load the web application base directory
        $webappPath = $this->getServletContext()->getWebappPath();

        // prepare the deployment descriptor
        $deploymentDescriptor = $webappPath . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR . 'web.xml';

        // query whether we found epb.xml deployment descriptor file
        if (file_exists($deploymentDescriptor) === false) {
            return;
        }

        // validate the passed configuration file
        /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $this->getApplication()->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
        $configurationService->validateFile($deploymentDescriptor, null, true);

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search('ObjectManagerInterface');

        // prepare and initialize the configuration node
        $webAppNode = new WebAppNode();
        $webAppNode->initFromFile($deploymentDescriptor);

        /** @var \AppserverIo\Appserver\Core\Api\Node\ServletNode $servletNode */
        foreach ($webAppNode->getServlets() as $servletNode) {
            // iterate over all configured descriptors and try to load object description
            /** \AppserverIo\Appserver\Core\Api\Node\DescriptorNode $descriptor */
            foreach ($objectManager->getConfiguredDescriptors() as $descriptor) {
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
                $this->getServletContext()->addSessionParameter($key, $value);
            }
        }

        // initialize the error page configuration if available
        /** @var \AppserverIo\Appserver\Core\Api\Node\ErrorPageNode $errorPageNode */
        foreach ($webAppNode->getErrorPages() as $errorPageNode) {
            $this->getServletContext()->addErrorPage((string) $errorPageNode->getErrorCodePattern(), (string) $errorPageNode->getErrorLocation());
        }

        // initialize the context with the context parameters
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContextParamNode $contextParamNode */
        foreach ($webAppNode->getContextParams() as $contextParamNode) {
            $this->getServletContext()->addInitParameter((string) $contextParamNode->getParamName(), (string) $contextParamNode->getParamValue());
        }
    }
}
