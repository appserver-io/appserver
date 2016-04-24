<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\PersistenceManager
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Core\Api\Node\PersistenceNode;
use AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface;
use AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\PersistenceContextInterface;
use AppserverIo\Psr\EnterpriseBeans\EntityManagerLookupException;

/**
 * The persistence manager handles the entity managers registered for the application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\GenericStackable $entityManagers The the storage for the entity manager instances
 */
class PersistenceManager extends AbstractManager implements PersistenceContextInterface
{

    /**
     * Injects the storage for the entity manager configurations.
     *
     * @param \AppserverIo\Storage\GenericStackable $entityManagers The storage for the entity manager configurations
     *
     * @return void
     */
    public function injectEntityManagers(GenericStackable $entityManagers)
    {
        $this->entityManagers = $entityManagers;
    }

    /**
     * Returns the storage with the registered entity manager configurations.
     *
     * @return \AppserverIo\Storage\GenericStackable The storage with the entity manager configurations
     */
    public function getEntityManagers()
    {
        return $this->entityManagers;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {
        $this->registerEntityManagers($application);
    }

    /**
     * Registers the entity managers at startup.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    protected function registerEntityManagers(ApplicationInterface $application)
    {

        // build up META-INF directory var
        $metaInfDir = $this->getWebappPath() . DIRECTORY_SEPARATOR .'META-INF';

        // check if we've found a valid directory
        if (is_dir($metaInfDir) === false) {
            return;
        }

        // check META-INF + subdirectories for XML files with MQ definitions
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
        $service = $application->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $xmlFiles = $service->globDir(AppEnvironmentHelper::getEnvironmentAwareFilePath($this->getWebappPath(), 'META_INF' . DIRECTORY_SEPARATOR . 'persistence'));

        // load the configuration service instance
        /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $application->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');

        // load the container node to initialize the system properties
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode */
        $containerNode = $application->getContainer()->getContainerNode();

        // gather all the deployed web applications
        foreach ($xmlFiles as $file) {
            try {
                // validate the file here, but skip if the validation fails
                $configurationService->validateFile($file, null, true);

                // load the system properties
                $properties = $service->getSystemProperties($containerNode);

                // append the application specific properties
                $properties->add(SystemPropertyKeys::WEBAPP, $webappPath = $application->getWebappPath());
                $properties->add(SystemPropertyKeys::WEBAPP_NAME, basename($webappPath));

                // create a new persistence manager node instance and replace the properties
                $persistenceNode = new PersistenceNode();
                $persistenceNode->initFromFile($file);
                $persistenceNode->replaceProperties($properties);

                // register the entity managers found in the configuration
                foreach ($persistenceNode->getPersistenceUnits() as $persistenceUnitNode) {
                    $this->registerEntityManager($application, $persistenceUnitNode);
                }

            } catch (InvalidConfigurationException $e) {
                // try to load the system logger instance
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger()) {
                    $systemLogger->error($e->getMessage());
                    $systemLogger->critical(sprintf('Persistence configuration file %s is invalid, needed entity managers might be missing.', $file));
                }

            } catch (\Exception $e) {
                // try to load the system logger instance
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger()) {
                    $systemLogger->error($e->__toString());
                }
            }
        }
    }

    /**
     * Deploys the entity manager described by the passed XML node.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface            $application         The application instance
     * @param \AppserverIo\Appserver\Core\Api\PersistenceUnitNodeInterface $persistenceUnitNode The XML node that describes the entity manager
     *
     * @return void
     */
    protected function registerEntityManager(ApplicationInterface $application, PersistenceUnitNodeInterface $persistenceUnitNode)
    {

        // initialize the the entity manager instance
        $this->entityManagers[$lookupName = $persistenceUnitNode->getName()] = $persistenceUnitNode;

        // bind the callback for the entity manager instance to the naming directory => necessary for DI provider
        $application->getNamingDirectory()->bindCallback(sprintf('php:global/%s/%s', $application->getUniqueName(), $lookupName), array(&$this, 'lookup'), array($lookupName));
    }

    /**
     * Runs a lookup for the entity manager with the passed class name.
     *
     * If the passed lookup name is an entity manager the instance
     * will be returned.
     *
     * @param string $lookupName The name of the requested entity manager
     *
     * @return object The requested entity manager instance
     * @throws \AppserverIo\Psr\EnterpriseBeans\EntityManagerLookupException Is thrown if requested entity manager instance has not been registered
     */
    public function lookup($lookupName)
    {

        // query whether the entity manager has been registered or not
        if (isset($this->entityManagers[$lookupName])) {
            // load the entity manager configuration
            $persistenceUnitNode = $this->entityManagers[$lookupName];

            // load the factory class from the configuration
            $factoryClass = $persistenceUnitNode->getFactory();

            // create a new entity manager instance from the configuration
            return $factoryClass::factory($this->getApplication(), $persistenceUnitNode);
        }

        // throw an exception if the requested entity manager has not been registered
        throw new EntityManagerLookupException(
            sprintf('Entity Manager with lookup name %s has not been registered in application %s', $lookupName, $this->getApplication()->getName())
        );
    }

    /**
     * Returns the identifier for the entity manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return PersistenceContextInterface::IDENTIFIER;
    }
}
