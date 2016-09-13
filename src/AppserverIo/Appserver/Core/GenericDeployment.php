<?php

/**
 * \AppserverIo\Appserver\Core\GenericDeployment
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Hans Höchtl <hhoechtl@1drop.de>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Api\Node\DatasourcesNode;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;

/**
 * Generic deployment implementation for web applications.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Hans Höchtl <hhoechtl@1drop.de>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

    /**
     * Returns all datasource files we potentially use
     *
     * @return array
     */
    protected function getDatasourceFiles()
    {
        // if we have a valid app base we will collect all datasources
        $datasourceFiles = array();
        if (is_dir($appBase = $this->getAppBase())) {
            // get all the global datasource files first
            $datasourceFiles = $this->getDeploymentService()->globDir($appBase . DIRECTORY_SEPARATOR . '*-ds.xml', 0, false);

            // iterate over all applications and collect the environment specific datasources
            foreach (glob($appBase . '/*', GLOB_ONLYDIR) as $webappPath) {
                $datasourceFiles = array_merge($datasourceFiles, $this->getDeploymentService()->globDir(AppEnvironmentHelper::getEnvironmentAwareGlobPattern($webappPath, '*-ds')));
            }
        }
        return $datasourceFiles;
    }

    /**
     * Initializes the available applications and adds them to the container.
     *
     * @return void
     * @see \AppserverIo\Psr\Deployment\DeploymentInterface::deploy()
     */
    public function deploy()
    {
        $this->deployDatasources();
        $this->deployApplications();
    }

    /**
     * Returns the container's directory with applications to be deployed.
     *
     * @return string The container's application base directory
     */
    protected function getAppBase()
    {
        return $this->getContainer()->getAppBase();
    }

    /**
     * Loads and return's the context instances for the container.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContextNode[] The array with the container's context instances
     */
    protected function loadContextInstances()
    {
        return $this->getDeploymentService()->loadContextInstancesByContainer($this->getContainer());
    }

    /**
     * Deploys the available root directory datasources.
     *
     * @return void
     */
    protected function deployDatasources()
    {

        // load the container
        $container = $this->getContainer();

        // load the container and check if we actually have datasource files to work with
        if ($datasourceFiles = $this->getDatasourceFiles()) {
            // load the naming directory instance
            $namingDirectory = $container->getNamingDirectory();

            // create a subdirectory for the container's datasoruces
            $namingDirectory->createSubdirectory(sprintf('php:env/%s/ds', $this->getContainer()->getName()));

            // iterate through all provisioning files (*-ds.xml), validate them and attach them to the configuration
            /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
            $configurationService = $this->getConfigurationService();
            foreach ($datasourceFiles as $datasourceFile) {
                try {
                    // validate the file, but skip it if validation fails
                    $configurationService->validateFile($datasourceFile);

                    // load the system properties
                    $systemProperties = $this->getDatasourceService()->getSystemProperties($container->getContainerNode());

                    // load the datasources from the file and replace the properties
                    $datasourcesNode = new DatasourcesNode();
                    $datasourcesNode->initFromFile($datasourceFile);
                    $datasourcesNode->replaceProperties($systemProperties);

                    // store the datasource in the system configuration
                    /** @var \AppserverIo\Appserver\Core\Api\Node\DatasourceNode $datasourceNode */
                    foreach ($datasourcesNode->getDatasources() as $datasourceNode) {
                        // add the datasource to the system configuration
                        $this->getDatasourceService()->persist($datasourceNode);

                        // bind the datasource to the naming directory
                        $namingDirectory->bind(sprintf('php:env/%s/ds/%s', $container->getName(), $datasourceNode->getName()), $datasourceNode);

                        // log a message that the datasource has been deployed
                        $this->getInitialContext()->getSystemLogger()->info(
                            sprintf('Successfully deployed datasource %s', $datasourceNode->getName())
                        );
                    }

                // log a message and continue with the next datasource node
                } catch (\Exception $e) {
                    // load the logger and log the XML validation errors
                    $systemLogger = $this->getInitialContext()->getSystemLogger();
                    $systemLogger->error($e->__toString());

                    // additionally log a message that DS will be missing
                    $systemLogger->critical(
                        sprintf('Will skip reading configuration in %s, datasources might be missing.', $datasourceFile)
                    );
                }
            }
        }
    }

    /**
     * Deploys the available applications.
     *
     * @return void
     */
    protected function deployApplications()
    {

        // load the container and initial context instance
        $container = $this->getContainer();

        // load the context instances for this container
        $contextInstances = $this->loadContextInstances();

        // gather all the deployed web applications
        foreach ($contextInstances as $context) {
            // try to load the application factory
            if ($applicationFactory = $context->getFactory()) {
                // use the factory if available
                $applicationFactory::visit($container, $context);
            } else {
                // if not, try to instantiate the application directly
                $applicationType = $context->getType();
                $container->addApplication(new $applicationType($context));
            }

            // log a message that the application has been initialized and started
            $this->getInitialContext()->getSystemLogger()->info(
                sprintf('Successfully initialized and started application %s', $context->getName())
            );
        }
    }
}
