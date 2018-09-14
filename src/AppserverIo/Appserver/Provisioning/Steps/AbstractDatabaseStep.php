<?php

/**
 * AppserverIo\Appserver\Provisioning\Steps\AbstractDatabaseStep
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

namespace AppserverIo\Appserver\Provisioning\Steps;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\SchemaTool;
use AppserverIo\Provisioning\Steps\AbstractStep;

/**
 * An abstract database step implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractDatabaseStep extends AbstractStep
{

    /**
     * The path to the Doctrine entities.
     *
     * @var string
     */
    const PARAM_PATH_TO_ENTITIES = 'pathToEntities';

    /**
     * The DB connection parameter with the path the database file.
     *
     * @var string
     */
    const CONNECTION_PARAM_PATH = 'path';

    /**
     * The DB connection parameter with the driver to use.
     *
     * @var string
     */
    const CONNECTION_PARAM_DRIVER = 'driver';

    /**
     * The DB connection parameter with user to connect.
     *
     * @var string
     */
    const CONNECTION_PARAM_USER = 'user';

    /**
     * The DB connection parameter with the passwort to connect.
     *
     * @var string
     */
    const CONNECTION_PARAM_PASSWORD = 'password';

    /**
     * The DB connection parameter with the databaseName to connect.
     *
     * @var string
     */
    const CONNECTION_PARAM_DATABASENAME = 'dbname';

    /**
     * The DB connection parameter with the host to connect.
     *
     * @var string
     */
    const CONNECTION_PARAM_HOST = 'host';

    /**
     * Returns an instance of the Doctrine Schema Tool.
     *
     * @return \Doctrine\ORM\Tools\SchemaTool The Doctrine Schema Tool
     */
    public function getSchemaTool()
    {
        return new SchemaTool($this->getEntityManager());
    }

    /**
     * Initializes and returns the Doctrine EntityManager instance.
     *
     * @return \Doctrine\ORM\EntityManager The Doctrine EntityManager instancer
     */
    public function getEntityManager()
    {

        // check if we have a valid datasource node
        if ($this->getDatasourceNode() == null) {
            return;
        }

        // prepare the path to the entities
        $absolutePaths = array();
        if ($relativePaths = $this->getStepNode()->getParam(AbstractDatabaseStep::PARAM_PATH_TO_ENTITIES)) {
            foreach (explode(PATH_SEPARATOR, $relativePaths) as $relativePath) {
                $absolutePaths[] = $this->getWebappPath() . DIRECTORY_SEPARATOR . $relativePath;
            }
        }

        // load the database connection parameters
        $connectionParameters = $this->getConnectionParameters();

        // initialize and load the entity manager and the schema tool
        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($absolutePaths, true, null, null, false);
        return EntityManager::create($connectionParameters, $metadataConfiguration);
    }

    /**
     * Initializes an returns an array with the database connection parameters
     * necessary to connect to the database using Doctrine.
     *
     * @return array An array with the connection parameters
     */
    public function getConnectionParameters()
    {

        // load the datasource node
        $datasourceNode = $this->getDatasourceNode();

        // initialize the database node
        $databaseNode = $datasourceNode->getDatabase();

        // initialize the connection parameters
        $connectionParameters = array(
            AbstractDatabaseStep::CONNECTION_PARAM_DRIVER => $databaseNode->getDriver()->getNodeValue()->__toString(),
            AbstractDatabaseStep::CONNECTION_PARAM_USER => $databaseNode->getUser()->getNodeValue()->__toString(),
            AbstractDatabaseStep::CONNECTION_PARAM_PASSWORD => $databaseNode->getPassword()->getNodeValue()->__toString()
        );

        // initialize the path to the database when we use sqlite for example
        if ($databaseNode->getPath()) {
            if ($path = $databaseNode->getPath()->getNodeValue()->__toString()) {
                $connectionParameters[AbstractDatabaseStep::CONNECTION_PARAM_PATH] = $this->getWebappPath() . DIRECTORY_SEPARATOR . $path;
            }
        }

        // add database name if using another PDO driver than sqlite
        if ($databaseNode->getDatabaseName()) {
            $databaseName = $databaseNode->getDatabaseName()->getNodeValue()->__toString();
            $connectionParameters[AbstractDatabaseStep::CONNECTION_PARAM_DATABASENAME] = $databaseName;
        }

        // add database host if using another PDO driver than sqlite
        if ($databaseNode->getDatabaseHost()) {
            $databaseHost = $databaseNode->getDatabaseHost()->getNodeValue()->__toString();
            $connectionParameters[AbstractDatabaseStep::CONNECTION_PARAM_HOST] = $databaseHost;
        }

        // set the connection parameters
        return $connectionParameters;
    }
}
