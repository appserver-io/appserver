<?php

/**
 * AppserverIo\Appserver\Ldap\EntityManagerFactory
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

use Symfony\Component\Ldap\Ldap;
use AppserverIo\Appserver\Ldap\Symfony\LdapClient;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface;

/**
 * Factory implementation for a Symfony LDAP Adapter instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EntityManagerFactory
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * The persistence unit configuration instance.
     *
     *  @var \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface
     */
    protected $persistenceUnitNode;

    /**
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface                            $application         The application instance
     * @param \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface $persistenceUnitNode The persistence unit configuration node
     */
    public function __construct(
        ApplicationInterface $application,
        PersistenceUnitConfigurationInterface $persistenceUnitNode
    ) {
        $this->application = $application;
        $this->persistenceUnitNode = $persistenceUnitNode;
    }

    /**
     * Return's the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    protected function getApplication()
    {
        return $this->application;
    }

    /**
     * Return's the persistence unit configuration instance.
     *
     * @return \AppserverIo\Description\Configuration\PersistenceUnitConfigurationInterface The configuration instance
     */
    protected function getPersistenceUnitNode()
    {
        return $this->persistenceUnitNode;
    }

    /**
     * Creates a new Symfony LDAP adapter instance based on the passed configuration.
     *
     * @return object The Symfony LDAP Adapter instance
     */
    public function factory()
    {

        // load the application and persistence unit configuration
        $application = $this->getApplication();
        $persistenceUnitNode = $this->getPersistenceUnitNode();

        // query whether or not an initialize EM configuration is available
        if ($application->hasAttribute($persistenceUnitNode->getName()) === false) {
            // load the datasource node
            $datasourceNode = null;
            foreach ($application->getInitialContext()->getSystemConfiguration()->getDatasources() as $datasourceNode) {
                if ($datasourceNode->getName() === $persistenceUnitNode->getDatasource()->getName()) {
                    break;
                }
            }

            // throw a exception if the configured datasource is NOT available
            if ($datasourceNode == null) {
                throw new \Exception(sprintf('Can\'t find a datasource node for persistence unit %s', $persistenceUnitNode->getName()));
            }

            // load the database node
            $databaseNode = $datasourceNode->getDatabase();

            // throw an exception if the configured database is NOT available
            if ($databaseNode == null) {
                throw new \Exception(sprintf('Can\'t find database node for persistence unit %s', $persistenceUnitNode->getName()));
            }

            // load the driver node
            $driverNode = $databaseNode->getDriver();

            // throw an exception if the configured driver is NOT available
            if ($driverNode == null) {
                throw new \Exception(sprintf('Can\'t find driver node for persistence unit %s', $persistenceUnitNode->getName()));
            }

            // load the connection parameters
            $connectionParameters = ConnectionUtil::get($application)->fromDatabaseNode($databaseNode);

            // append the initialized EM configuration to the application
            $application->setAttribute($persistenceUnitNode->getName(), array($connectionParameters));
        }

        try {
            // load the initialized EM configuration from the application
            list ($connectionParameters) = $application->getAttribute($persistenceUnitNode->getName());

            // initialize the LDAP client
            $ldapClient = new LdapClient(
                Ldap::create($connectionParameters['driver'], [
                    'host' => $connectionParameters['host'],
                    'port' => $connectionParameters['port']
                ])
            );

            // bind the LDAP adapter to the given credentials
            $ldapClient->bind($connectionParameters['user'], $connectionParameters['password']);

            // initialize and return the bound LDAP entity manager
            return new EntityManager($ldapClient, $application, $persistenceUnitNode);
        } catch (\Exception $e) {
            error_log($e->__toString());
        }
    }
}
