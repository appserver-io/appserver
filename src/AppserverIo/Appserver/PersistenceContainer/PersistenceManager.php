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

use AppserverIo\Storage\StorageInterface;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\CollectionInterface;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\PersistenceContextInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Core\Api\Node\PersistenceNode;
use AppserverIo\Appserver\Core\Api\InvalidConfigurationException;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\DoctrineLocalContextConnection;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodInterface;
use AppserverIo\Description\PersistenceUnitDescriptor;
use AppserverIo\Description\PersistenceUnitFactoryDescriptor;
use AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface;

/**
 * The persistence manager handles the entity managers registered for the application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface $managerSettings    Settings for the bean manager
 * @property \AppserverIo\Storage\StorageInterface                                    $entityManagerNames The the storage for the entity manage names
 */
class PersistenceManager extends AbstractManager implements PersistenceContextInterface, ManagerSettingsAwareInterface
{

    /**
     * Injects the storage for the entity manager names.
     *
     * @param \AppserverIo\Storage\StorageInterface $entityManagerNames The storage for the entity manager names
     *
     * @return void
     */
    public function injectEntityManagerNames(StorageInterface $entityManagerNames)
    {
        $this->entityManagerNames = $entityManagerNames;
    }

    /**
     * Return's the storage for the entity manager names.
     *
     * @return \AppserverIo\Storage\StorageInterface The storage for the entity manager names
     */
    public function getEntityManagerNames()
    {
        return $this->entityManagerNames;
    }

    /**
     * Add the passed entity manager name to the persistenc manager.
     *
     * @param string $entityManagerName The entity manager name to add
     *
     * @return void
     */
    public function addEntityManagerName($entityManagerName)
    {
        $this->getEntityManagerNames()->set(sizeof($this->getEntityManagerNames()->getAllKeys()), $entityManagerName);
    }

    /**
     * Injects the bean manager settings.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface $managerSettings The bean manager settings
     *
     * @return void
     */
    public function injectManagerSettings(ManagerSettingsInterface $managerSettings)
    {
        $this->managerSettings = $managerSettings;
    }

    /**
     * Return's the bean manager settings.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface The bean manager settings
     */
    public function getManagerSettings()
    {
        return $this->managerSettings;
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
    public function registerEntityManagers(ApplicationInterface $application)
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
        $xmlFiles = $service->globDir(AppEnvironmentHelper::getEnvironmentAwareGlobPattern($this->getWebappPath(), 'META-INF' . DIRECTORY_SEPARATOR . 'persistence'));

        // load the configuration service instance
        /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $application->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');

        // load the container node to initialize the system properties
        /** @var \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode */
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
                $properties->add(SystemPropertyKeys::WEBAPP_DATA, $application->getDataDir());
                $properties->add(SystemPropertyKeys::WEBAPP_CACHE, $application->getCacheDir());
                $properties->add(SystemPropertyKeys::WEBAPP_SESSION, $application->getSessionDir());

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
     * @param \AppserverIo\Psr\Application\ApplicationInterface                            $application         The application instance
     * @param \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface $persistenceUnitNode The XML node that describes the entity manager
     *
     * @return void
     */
    public function registerEntityManager(ApplicationInterface $application, PersistenceUnitConfigurationInterface $persistenceUnitNode)
    {

        // bind the callback for the Entity Manager instance to the naming directory
        $application->getNamingDirectory()
                    ->bind(
                        sprintf('php:global/%s/%s', $application->getUniqueName(), $lookupName = $persistenceUnitNode->getName()),
                        array(&$this, 'lookup'),
                        array($lookupName)
                    );

        // bind the Entity Manager's configuration to the naming directory
        $application->getNamingDirectory()
                    ->bind(
                        sprintf('php:global/%s/%sConfiguration', $application->getUniqueName(), $lookupName),
                        $persistenceUnitNode
                    );

        // register the entity manager's configuration in the persistence manager
        $this->addEntityManagerName($lookupName);

        // load the object manager instance
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // add the descriptors, necesseary to create the Entity Manaager instance, to the object manager
        $objectManager->addObjectDescriptor(PersistenceUnitDescriptor::newDescriptorInstance()->fromConfiguration($persistenceUnitNode));
        $objectManager->addObjectDescriptor(PersistenceUnitFactoryDescriptor::newDescriptorInstance()->fromConfiguration($persistenceUnitNode));
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
        return $this->get($lookupName);
    }

    /**
     * This returns a proxy to the requested session bean.
     *
     * @param string $lookupName The lookup name for the requested session bean
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The proxy instance
     */
    public function lookupProxy($lookupName)
    {

        // initialize the remote method call parser and the session storage
        $sessions = new ArrayList();

        // initialize the local context connection
        $connection = new DoctrineLocalContextConnection();
        $connection->injectSessions($sessions);
        $connection->injectApplication($this->getApplication());

        // initialize the context session
        $session = $connection->createContextSession();

        // set the session ID from the environment
        $session->setSessionId(Environment::singleton()->getAttribute(EnvironmentKeys::SESSION_ID));

        // lookup and return the requested remote bean instance
        return $session->createInitialContext()->lookup($lookupName);
    }

    /**
     * Invoke the passed remote method on the described session bean and return the result.
     *
     * @param \AppserverIo\RemoteMethodInvocation\RemoteMethodInterface $remoteMethod The remote method description
     * @param \AppserverIo\Collections\CollectionInterface              $sessions     The collection with the sessions
     *
     * @return mixed The result of the remote method invocation
     */
    public function invoke(RemoteMethodInterface $remoteMethod, CollectionInterface $sessions)
    {

        // prepare method name and parameters and invoke method
        $className  = $remoteMethod->getClassName();
        $methodName = $remoteMethod->getMethodName();
        $parameters = $remoteMethod->getParameters();

        // load a fresh bean instance and add it to the session container
        $instance = $this->lookup($className);

        // invoke the remote method call on the local instance
        return call_user_func_array(array($instance, $methodName), $parameters);
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
