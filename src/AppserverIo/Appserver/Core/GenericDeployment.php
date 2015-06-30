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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Configuration\ConfigurationException;

/**
 * Generic deployment implementation for web applications.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GenericDeployment extends AbstractDeployment
{

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
     * Deploys the available datasources.
     *
     * @return void
     */
    protected function deployDatasources()
    {

        // check if deploy dir exists
        if (is_dir($directory = $this->getDeploymentService()->getWebappsDir())) {
            // load the datasource files
            $datasourceFiles = $this->getDeploymentService()->globDir($directory . DIRECTORY_SEPARATOR . '*-ds.xml');

            // iterate through all provisioning files (*-ds.xml), validate them and attach them to the configuration
            /** @var AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
            $configurationService = $this->getConfigurationService();
            foreach ($datasourceFiles as $datasourceFile) {
                try {
                    // validate the file, but skip it if validation fails
                    $configurationService->validateFile($datasourceFile);

                    // load the database configuration
                    $datasourceNodes = $this->getDatasourceService()->initFromFile($datasourceFile);

                    // store the datasource in the system configuration
                    foreach ($datasourceNodes as $datasourceNode) {
                        $this->getDatasourceService()->persist($datasourceNode);

                        // log a message that the datasource has been deployed
                        $this->getInitialContext()->getSystemLogger()->info(
                            sprintf('Successfully deployed datasource %s', $datasourceNode->getName())
                        );
                    }

                // log a message and continue with the next datasource node
                } catch (ConfigurationException $ce) {
                    // load the logger and log the XML validation errors
                    $systemLogger = $this->getInitialContext()->getSystemLogger();
                    $systemLogger->error($ce->__toString());

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
        $contextInstances = $this->getDeploymentService()->loadContextInstancesByContainer($container);

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
