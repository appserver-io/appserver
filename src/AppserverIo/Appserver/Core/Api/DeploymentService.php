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

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ContextNode;
use AppserverIo\Appserver\Core\Api\Node\DeploymentNode;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

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

        // initialize the array for the context instances
        $contextInstances = array();

        // we will need to test our configuration files
        $configurationTester = new ConfigurationService($this->getInitialContext());
        $baseContextPath = $this->getConfdDir('context.xml');

        // validate the base context file and load it as default if validation succeeds
        $baseContext = new ContextNode();
        if (!$configurationTester->validateFile($baseContextPath, null)) {
            $errorMessages = $configurationTester->getErrorMessages();
            $systemLogger = $this->getInitialContext()->getSystemLogger();
            $systemLogger->error(reset($errorMessages));
            $systemLogger->critical(sprintf('Problems validating base context file %s, this might affect app configurations badly.', $baseContextPath));

        } else {
            $baseContext->initFromFile($baseContextPath);
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
                // validate the file, but skip it if validation fails
                if (!$configurationTester->validateFile($contextFile, null)) {
                    $errorMessages = $configurationTester->getErrorMessages();
                    $systemLogger = $this->getInitialContext()->getSystemLogger();
                    $systemLogger->error(reset($errorMessages));
                    $systemLogger->alert(sprintf('Will skip app specific context file %s, configuration might be faulty.', $contextFile));
                    continue;
                }

                // create a new context node instance
                $contextInstance = new ContextNode();
                $contextInstance->initFromFile($contextFile);

                // merge it into the default configuration
                $context->merge($contextInstance);
            }

            // set the real context name
            $context->setName($contextPath);

            // attach the context to the context instance
            $contextInstances[$contextPath] = $context;
        }

        // return the array with the context instances
        return $contextInstances;
    }
}
