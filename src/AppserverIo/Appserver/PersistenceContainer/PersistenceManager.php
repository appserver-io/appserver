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

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\EventManager;
use Doctrine\Common\Annotations\AnnotationRegistry;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Core\Api\Node\PersistenceNode;
use AppserverIo\Description\PersistenceUnitReferenceDescriptor;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\PersistenceContextInterface;
use AppserverIo\Psr\EnterpriseBeans\EntityManagerLookupException;
use AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode;
use AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\EntityManagerFactory;

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
     * Injects the storage for the entity manager instances.
     *
     * @param \AppserverIo\Storage\GenericStackable $entityManagers The storage for the entity manager instances
     *
     * @return void
     */
    public function injectEntityManagers(GenericStackable $entityManagers)
    {
        $this->entityManagers = $entityManagers;
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
        $xmlFiles = $service->globDir($metaInfDir . DIRECTORY_SEPARATOR . 'persistence.xml');

        // gather all the deployed web applications
        foreach ($xmlFiles as $file) {
            try {
                // try to initialize a SimpleXMLElement
                $sxe = new \SimpleXMLElement($file, null, true);
                $sxe->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

                // validate the file here, if it is not valid we can skip further steps
                try {
                    /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
                    $configurationService = $application->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
                    $configurationService->validateFile($file, null, true);

                } catch (InvalidConfigurationException $e) {
                    /** @var \Psr\Log\LoggerInterface $systemLogger */
                    $systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger();
                    $systemLogger->error($e->getMessage());
                    $systemLogger->critical(sprintf('Message queue configuration file %s is invalid, needed queues might be missing.', $file));
                    return;
                }

                // initialize the entity managers found in the deployment descriptor
                $persistenceNode = new PersistenceNode();
                $persistenceNode->initFromFile($file);
                foreach ($persistenceNode->getPersistenceUnits() as $persistenceUnitNode) {
                    $this->registerEntityManager($application, $persistenceUnitNode);
                }

            // if class can not be reflected continue with next class
            } catch (\Exception $e) {
                // log an error message
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());
                // proceed with the next queue
                continue;
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
        $application->bindCallback($lookupName, array(&$this, 'lookup'), array($lookupName));
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
        return EntityManagerInterface::IDENTIFIER;
    }
}
