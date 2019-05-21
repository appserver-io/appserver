<?php

/**
 * AppserverIo\Appserver\Ldap\LdapManager
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
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Ldap;

use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Di\ObjectManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface;
use AppserverIo\Ldap\LdapManagerInterface;
use AppserverIo\Ldap\Description\EntityDescriptorInterface;
use AppserverIo\Ldap\Description\LdapDescriptorInterface;
use AppserverIo\Ldap\Description\RepositoryDescriptorInterface;

/**
 * The LDAP manager is necessary to load and provides information about all
 * LDAP objects related with the application itself.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface $managerSettings Settings for the LDAP manager
 */
class LdapManager extends AbstractManager implements LdapManagerInterface, ManagerSettingsAwareInterface
{

    /**
     * Injects the LDAP manager settings.
     *
     * @param \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface $managerSettings The LDAP manager settings
     *
     * @return void
     */
    public function injectManagerSettings(ManagerSettingsInterface $managerSettings)
    {
        $this->managerSettings = $managerSettings;
    }

    /**
     * Return's the LDAP manager settings.
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\BeanManagerSettingsInterface The LDAP manager settings
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

        // add the application instance to the environment
        Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

        // create s simulated request/session ID whereas session equals request ID
        Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
        Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

        // finally register the entiies
        $this->registerEntities($application);
    }

    /**
     * Registers the LDAP entities at startup.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function registerEntities(ApplicationInterface $application)
    {

        // register the annotation registries
        $application->registerAnnotationRegistries();

        // parse the object descriptors
        $this->parseObjectDescriptors();

        // load the object manager
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // register the entities found by annotations and the XML configuration
        /** \AppserverIo\Psr\Deployment\DescriptorInterface $objectDescriptor */
        foreach ($objectManager->getObjectDescriptors() as $descriptor) {
            // check if we've found a entity descriptor and register the entity
            if ($descriptor instanceof EntityDescriptorInterface) {
                $this->registerEntity($descriptor);
            }
        }
    }

    /**
     * Register the entity described by the passed descriptor.
     *
     * @param \AppserverIo\Ldap\Description\EntityDescriptorInterface $descriptor The entity descriptor
     *
     * @return void
     */
    public function registerEntity(EntityDescriptorInterface $descriptor)
    {

        try {
            // load the application instance
            $application = $this->getApplication();

            // register the bean with the default name/short class name
            $application->getNamingDirectory()
                ->bind(
                    sprintf('php:global/%s/%s', $application->getUniqueName(), $descriptor->getName()),
                    array(&$this, 'lookup'),
                    array($descriptor->getName())
                );

            // register the entity => repository mapping and vice versa
            $this->setAttribute($descriptor->getName(), $descriptor->getRepository());
            $this->setAttribute($descriptor->getRepository(), $descriptor->getName());

        } catch (\Exception $e) {
            // log the exception
            $this->getApplication()->getInitialContext()->getSystemLogger()->critical($e->__toString());
        }
    }

    /**
     * Runs a lookup for the entity with the passed lookup name.
     *
     * @param string $lookupName The lookpu name of the entity class
     *
     * @return object The requested entity instance
     */
    public function lookup($lookupName)
    {
        return $this->get($lookupName);
    }

    /**
     * Lookup the LDAP repository for the entity with the passed lookup name.
     *
     * @param string $lookupName The lookup name of the entity to return the repository for
     * @param array  $args       The arguments passed to the repository's constructor
     *
     * @return object The repository instance
     * @throws \Exception Is thrown, if the requested instance is NO LDAP repository
     */
    public function lookupRepositoryByEntityName($lookupName, array $args = array())
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // query whether or not the passed lookup name relates to a LDAP repository
        if ($objectManager->hasObjectDescriptor($repositoryLookupName = $this->getAttribute($lookupName))) {
            if ($objectManager->getObjectDescriptor($repositoryLookupName) instanceof RepositoryDescriptorInterface) {
                return $this->getApplication()->search($repositoryLookupName, $args);
            }
        }

        // throw an exception if the requested instance is NOT a LDAP repository
        throw new \Exception(sprintf('Requested instance with lookup name "%s" is not available or is not a LDAP repository', $repositoryLookupName));
    }

    /**
     * Lookup the LDAP entity for the entity with the passed lookup name.
     *
     * @param string $lookupName The lookup name of the repository to return the entity for
     * @param array  $args       The arguments passed to the entity's constructor
     *
     * @return object The entity instance
     * @throws \Exception Is thrown, if the requested instance is NO LDAP entity
     */
    public function lookupEntityByRepositoryName($lookupName, array $args = array())
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // query whether or not the passed lookup name relates to a LDAP entity
        if ($objectManager->hasObjectDescriptor($entityLookupName = $this->getAttribute($lookupName))) {
            // load the object descriptor
            $objectDescriptor = $objectManager->getObjectDescriptor($entityLookupName);
            // make sure that we've an LDAP entity descriptor here
            if ($objectDescriptor instanceof EntityDescriptorInterface) {
                return $this->newInstance($objectDescriptor->getClassName(), $args);
            }
        }

        // throw an exception if the requested instance is NOT a LDAP entity
        throw new \Exception(sprintf('Requested instance with lookup name "%s" is not available or is not a LDAP entity', $entityLookupName));
    }

    /**
     * Returns the object descriptor for the repository with the passed class name.
     *
     * @param string $className The class name to return the object descriptor for
     *
     * @return \AppserverIo\Ldap\Description\RepositoryDescriptorInterface|null The requested repository descriptor instance
     */
    public function lookupDescriptorByClassName($className)
    {

        // load the object manager instance
        /** @var \AppserverIo\Psr\Di\ObjectManagerInterface $objectManager */
        $objectManager = $this->getApplication()->search(ObjectManagerInterface::IDENTIFIER);

        // load the object descriptors
        $objectDescriptors = $objectManager->getObjectDescriptors();

        // iterate over the object descriptors and compare the class names
        foreach ($objectDescriptors as $objectDescriptor) {
            if ($objectDescriptor instanceof LdapDescriptorInterface) {
                if ($objectDescriptor->getClassName() === $className) {
                    return $objectDescriptor;
                }
            }
        }
    }

    /**
     * Returns the identifier for the object manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return LdapManagerInterface::IDENTIFIER;
    }
}
