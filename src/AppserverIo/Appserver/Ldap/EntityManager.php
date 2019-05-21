<?php

/**
 * AppserverIo\Appserver\Ldap\EntityManager
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

use Symfony\Component\Ldap\LdapInterface;
use AppserverIo\Ldap\LdapManagerInterface;
use AppserverIo\Ldap\EntityManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface;

/**
 * Simple LDAP Entity Manager implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EntityManager implements EntityManagerInterface
{

    /**
     * The Symfony LDAP client instance.
     *
     * @var \Symfony\Component\Ldap\LdapClientInterface
     */
    protected $ldapClient;

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * The persistence unit configuration instance.
     *
     * @var \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface
     */
    protected $configuration;

    /**
     * Initializes the Entity Manager with the Symfony LDAP adapter instance,
     * the actual application instance and the persistence unit configuration.
     *
     * @param \Symfony\Component\Ldap\LdapInterface                                        $ldapClient    The LDAP client instance
     * @param \AppserverIo\Psr\Application\ApplicationInterface                            $application   The application instance
     * @param \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface $configuration The persistence unit configuration instance
     */
    public function __construct(
        LdapInterface $ldapClient,
        ApplicationInterface $application,
        PersistenceUnitConfigurationInterface $configuration
    ) {

        // set the instances
        $this->ldapClient = $ldapClient;
        $this->application = $application;
        $this->configuration = $configuration;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Returns the Symfony LDAP instance.
     *
     * @return \Symfony\Component\Ldap\LdapInterface The Symfony LDAP instance
     */
    public function getLdapClient()
    {
        return $this->ldapClient;
    }

    /**
     * Returns the persistence unit configuration instance.
     *
     * @return \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface The configuration instance
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the base DN => database in datasource configuration.
     *
     * @return string The base DN
     */
    public function getBaseDn()
    {
        return $this->getApplication()
                     ->search(sprintf('ds/%s', $this->getConfiguration()->getDatasource()->getName()))
                     ->getDatabase()
                     ->getDatabaseName();
    }

    /**
     * Returns the repository instance for the entity with the passed lookup name.
     *
     * @param string $lookupName The lookup name of the entity to return the repository for
     *
     * @return object The LDAP repository instance
     */
    public function getRepository($lookupName)
    {
        return $this->getApplication()->search(LdapManagerInterface::IDENTIFIER)->lookupRepositoryByEntityName($lookupName);
    }

    /**
     * Finds an entity by its lookup name.
     *
     * @param string $lookupName The look name of the entity to find
     * @param mixed  $id         The identity of the entity to find
     *
     * @return object|null The entity instance or NULL if the entity can not be found.
     *
     * @throws \Exception
     */
    public function find($lookupName, $id)
    {
        return $this->getRepository($lookupName)->load($id);
    }

    /**
     * Tells the EntityManager to make an instance managed and persistent.
     *
     * The entity will be entered into the database at or before transaction
     * commit or as a result of the flush operation.
     *
     * NOTE: The persist operation always considers entities that are not yet known to
     * this EntityManager as NEW. Do not pass detached entities to the persist operation.
     *
     * @param object $entity The instance to make managed and persistent.
     *
     * @return void
     * @throws \Exception
     */
    public function persist($entity)
    {
        throw new \Exception(sprintf('Method "%s" has not been implemented yet!', __METHOD__));
    }

    /**
     * Removes an entity instance.
     *
     * A removed entity will be removed from the database at or before transaction commit
     * or as a result of the flush operation.
     *
     * @param object $entity The entity instance to remove
     *
     * @return void
     * @throws \Exception
     */
    public function remove($entity)
    {
        throw new \Exception(sprintf('Method "%s" has not been implemented yet!', __METHOD__));
    }
}
