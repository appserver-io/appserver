<?php

/**
 * \AppserverIo\Appserver\Core\Api\DeploymentService
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

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Configuration\ConfigurationException;
use AppserverIo\Appserver\Core\Api\Node\ContextNode;
use AppserverIo\Appserver\Core\Api\Node\DeploymentNode;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ServerNode;
use AppserverIo\Appserver\Core\Api\Node\ContainersNode;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * A service that handles deployment configuration data.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeploymentService extends AbstractFileOperationService
{

    /**
     * Return's all deployment configurations.
     *
     * @return array An array with all deployment configurations
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $deploymentNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $container) {
            $deploymentNode = $container->getDeployment();
            $deploymentNodes[$deploymentNode->getUuid()] = $deploymentNode;
        }
        return $deploymentNodes;
    }

    /**
     * Returns the deployment with the passed UUID.
     *
     * @param integer $uuid UUID of the deployment to return
     *
     * @return DeploymentNode The deployment with the UUID passed as parameter
     * @see ServiceInterface::load()
     */
    public function load($uuid)
    {
        $deploymentNodes = $this->findAll();
        if (array_key_exists($uuid, $deploymentNodes)) {
            return $deploymentNodes[$uuid];
        }
    }

    /**
     * Initializes the available application contexts and returns them.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ContainerInterface $container The container we want to add the applications to
     *
     * @return array The array with the application contexts
     */
    public function loadContextInstancesByContainer(ContainerInterface $container)
    {

        try {
            // initialize the array for the context instances
            $contextInstances = array();

            // validate the base context file
            /** @var AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
            $configurationService = $this->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
            $configurationService->validateFile($baseContextPath = $this->getConfdDir('context.xml'), null);

            //load it as default if validation succeeds
            $baseContext = new ContextNode();
            $baseContext->initFromFile($baseContextPath);

        } catch (ConfigurationException $ce) {
            // load the logger and log the XML validation errors
            $systemLogger = $this->getInitialContext()->getSystemLogger();
            $systemLogger->error($ce->__toString());

            // additionally log a message that DS will be missing
            $systemLogger->critical(
                sprintf('Problems validating base context file %s, this might affect app configurations badly.', $baseContextPath)
            );
        }

        // iterate over all applications and create the context configuration
        foreach (glob($container->getAppBase() . '/*', GLOB_ONLYDIR) as $webappPath) {
            // prepare the context path
            $contextPath = basename($webappPath);

            // start with a fresh clone of the base context configuration
            $context = clone $baseContext;

            // try to load a context configuration (from appserver.xml) for the context path
            if ($contextToMerge = $container->getContainerNode()->getHost()->getContext($contextPath)) {
                $context->merge($contextToMerge);
            }

            // iterate through all context configurations (context.xml), validate and merge them
            foreach ($this->globDir($webappPath . '/META-INF/context.xml') as $contextFile) {
                try {
                    // validate the application specific context
                    $configurationService->validateFile($contextFile, null);

                    // create a new context node instance
                    $contextInstance = new ContextNode();
                    $contextInstance->initFromFile($contextFile);

                    // merge it into the default configuration
                    $context->merge($contextInstance);

                } catch (ConfigurationException $ce) {
                    // load the logger and log the XML validation errors
                    $systemLogger = $this->getInitialContext()->getSystemLogger();
                    $systemLogger->error($ce->__toString());

                    // additionally log a message that DS will be missing
                    $systemLogger->critical(
                        sprintf('Will skip app specific context file %s, configuration might be faulty.', $contextFile)
                    );
                }
            }

            // set the real context name
            $context->setName($contextPath);

            // attach the context to the context instance
            $contextInstances[$contextPath] = $context;
        }

        // return the array with the context instances
        return $contextInstances;
    }

    /**
     * Loads the containers, defined by the applications, merges them into
     * the system configuration and returns the merged system configuration.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface The merged system configuration
     */
    public function loadContainerInstances()
    {

        // load the system configuration
        /** @var AppserverIo\Appserver\Core\Interfaces\SystemConfigurationInterface $systemConfiguration */
        $systemConfiguration = $this->getSystemConfiguration();

        // load the service to validate the files
        /** @var AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $this->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');

        /** @var AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNodeInstance */
        foreach ($systemConfiguration->getContainers() as $containerName => $containerNodeInstance) {
            // load the containers application base directory
            $containerAppBase = $this->getBaseDirectory($containerNodeInstance->getHost()->getAppBase());

            // iterate over all applications and create the server configuration
            foreach (glob($containerAppBase . '/*', GLOB_ONLYDIR) as $webappPath) {
                // iterate through all server configurations (servers.xml), validate and merge them
                foreach ($this->globDir($webappPath . '/META-INF/containers.xml') as $containersConfigurationFile) {
                    try {
                        // validate the application specific container configurations
                        $configurationService->validateFile($containersConfigurationFile, null);

                        // create a new containers node instance
                        $containersNodeInstance = new ContainersNode();
                        $containersNodeInstance->initFromFile($containersConfigurationFile);

                        /** @var AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNodeInstance */
                        foreach ($containersNodeInstance->getContainers() as $containerNodeInstance) {
                            // query whether we've to merge or append the server node instance
                            if ($container = $systemConfiguration->getContainer($containerNodeInstance->getName())) {
                                $container->merge($containerNodeInstance);
                            } else {
                                $systemConfiguration->attachContainer($containerNodeInstance);
                            }
                        }

                    } catch (ConfigurationException $ce) {
                        // load the logger and log the XML validation errors
                        $systemLogger = $this->getInitialContext()->getSystemLogger();
                        $systemLogger->error($ce->__toString());

                        // additionally log a message that server configuration will be missing
                        $systemLogger->critical(
                            sprintf('Will skip app specific server configuration file %s, configuration might be faulty.', $serverConfigurationFile)
                        );
                    }
                }
            }
        }

        // returns the merged system configuration
        return $systemConfiguration;
    }
}
