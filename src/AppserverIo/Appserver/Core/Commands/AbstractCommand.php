<?php

/**
 * AppserverIo\Appserver\Core\Commands\AbstractCommand
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

namespace AppserverIo\Appserver\Core\Commands;

use React\Socket\ConnectionInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * An abstract command implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractCommand implements CommandInterface
{

    /**
     * The connection instance.
     *
     * @var \React\Socket\ConnectionInterface
     */
    protected $connection;

    /**
     * The application server instance.
     *
     * @var \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface
     */
    protected $applicationServer;

    /**
     * Initializes the command with the connection and the application server
     * instance to execute the command on.
     *
     * @param \React\Socket\ConnectionInterface                                 $connection        The connection instance
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer The application server instance
     */
    public function __construct(ConnectionInterface $connection, ApplicationServerInterface $applicationServer)
    {
        $this->connection = $connection;
        $this->applicationServer = $applicationServer;
    }

    /**
     * Returns the naming directory instance.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The default naming directory
     */
    protected function getNamingDirectory()
    {
        return $this->applicationServer->getNamingDirectory();
    }

    /**
     * Returns the deployment service instance.
     *
     * @return \AppserverIo\Appserver\Core\Api\DeploymentService The deployment service instance
     */
    protected function getDeploymentService()
    {
        return $this->applicationServer->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    protected function getSystemLogger()
    {
        return $this->applicationServer->getSystemLogger();
    }

    /**
     * Write's the passed data to the actual connection.
     *
     * @param mixed $data The data to write
     *
     * @return void
     */
    protected function write($data)
    {
        $this->connection->write($data);
    }

    /**
     * Try to load the default entity manager of the passed application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to load the default entity manager for
     *
     * @return \Doctrine\ORM\EntityManagerInterface The application's default entity manager
     * @throws \Exception Is thrown, if the application has no default entity manager
     */
    protected function loadDefaultEntityManager(ApplicationInterface $application)
    {

        // load the persistence.xml files of the webapp
        $persistenceUnitFiles = $this->getDeploymentService()
                                     ->globDir(
                                         AppEnvironmentHelper::getEnvironmentAwareGlobPattern(
                                                 $application->getWebappPath(),
                                                 DirectoryKeys::realpath('META-INF/persistence*')
                                             )
                                         );

        // iterate over the found files and try to load the entity manager from the application
        foreach ($persistenceUnitFiles as $persistenceUnitFile) {
            // load the apropriate DS
            $xml = simplexml_load_file($persistenceUnitFile);
            $xml->registerXPathNamespace('appserver', 'http://www.appserver.io/appserver');

            // load the persistence unit information
            /** @var \SimpleXMLElement $persistenceUnit */
            foreach ($xml->xpath('//appserver:persistence/appserver:persistenceUnits/appserver:persistenceUnit') as $persistenceUnit) {
                foreach ($persistenceUnit->attributes() as $name => $value) {
                    if ($name === 'name') {
                        // try to load the application's entity manager
                        /** \Doctrine\ORM\EntityManagerInterface $entityManager */
                        return $application->search($value);
                    }
                }
            }
        }

        // throw an exception, if no default entity manager is available
        throw new \Exception(sprintf('Can\'t load default entity manager for application "%s"', $application->getName()));
    }

    /**
     * Try to load the default datasource of the passed application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to load the default datasource for
     *
     * @return \Doctrine\DBAL\Connection The application's default connection
     * @throws \Exception Is thrown, if the application has no default datasource
     */
    protected function loadDefaultConnection(ApplicationInterface $application)
    {

        // load the *.ds files of the webapp
        $persistenceUnitFiles = $this->getDeploymentService()
                                     ->globDir(
                                         AppEnvironmentHelper::getEnvironmentAwareGlobPattern(
                                             $application->getWebappPath(),
                                             DirectoryKeys::realpath('META-INF/*-ds')
                                         )
                                     );

        // iterate over the found files and try to load the entity manager from the application
        foreach ($persistenceUnitFiles as $persistenceUnitFile) {
            // load the apropriate DS
            $xml = simplexml_load_file($persistenceUnitFile);
            $xml->registerXPathNamespace('appserver', 'http://www.appserver.io/appserver');

            // load the database information
            /** @var \SimpleXMLElement $database */
            foreach ($xml->xpath('//appserver:datasources/appserver:datasource/appserver:database') as $database) {
                // initialize the configuration
                $config = new \Doctrine\DBAL\Configuration();
                // initialize the connection parameters
                $connectionParams = array(
                    'dbname'   => (string) $database->{'databaseName'},
                    'user'     => (string) $database->{'user'},
                    'password' => (string) $database->{'password'},
                    'host'     => (string) $database->{'databaseHost'},
                    'driver'   => (string) $database->{'driver'}
                );

                // load and return the connection
                return \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
            }
        }

        // throw an exception, if no default datasource is available
        throw new \Exception(sprintf('Can\'t load default datasource for application "%s"', $application->getName()));
    }
}
