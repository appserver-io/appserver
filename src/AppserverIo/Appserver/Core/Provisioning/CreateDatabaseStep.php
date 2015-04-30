<?php

/**
 * \AppserverIo\Appserver\Core\Provisioning\CreateDatabaseStep
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

namespace AppserverIo\Appserver\Core\Provisioning;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\SchemaTool;

/**
 * An step implementation that creates a database based on the specified datasource.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CreateDatabaseStep extends AbstractStep
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
     * Executes the functionality for this step, in this case the execution of
     * the PHP script defined in the step configuration.
     *
     * @return void
     * @throws \Exception Is thrown if the script can't be executed
     * @see \AppserverIo\Appserver\Core\Provisioning\StepInterface::execute()
     */
    public function execute()
    {

        try {
            // check if we have a valid datasource node
            if ($this->getDatasourceNode() == null) {
                return;
            }

            // prepare the path to the entities
            $absolutePaths = array();
            if ($relativePaths = $this->getStepNode()->getParam(CreateDatabaseStep::PARAM_PATH_TO_ENTITIES)) {
                foreach (explode(PATH_SEPARATOR, $relativePaths) as $relativePath) {
                    $absolutePaths[] = $this->getWebappPath() . DIRECTORY_SEPARATOR . $relativePath;
                }
            }

            // load the database connection parameters
            $connectionParameters = $this->getConnectionParameters();

            // register the class loader again, because in a Thread the context has been lost maybe
            require SERVER_AUTOLOADER;

            // initialize and load the entity manager and the schema tool
            $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($absolutePaths, true);
            $entityManager = EntityManager::create($connectionParameters, $metadataConfiguration);
            $schemaTool = new SchemaTool($entityManager);

            // load the class definitions
            $classes = $entityManager->getMetadataFactory()->getAllMetadata();

            // drop the schema if it already exists and create it new
            $schemaTool->dropSchema($classes);
            $schemaTool->createSchema($classes);

        } catch (\Exception $e) {
            error_log($e->__toString());
        }
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
            CreateDatabaseStep::CONNECTION_PARAM_DRIVER => $databaseNode->getDriver()->getNodeValue()->__toString(),
            CreateDatabaseStep::CONNECTION_PARAM_USER => $databaseNode->getUser()->getNodeValue()->__toString(),
            CreateDatabaseStep::CONNECTION_PARAM_PASSWORD => $databaseNode->getPassword()->getNodeValue()->__toString()
        );

        // initialize the path to the database when we use sqlite for example
        if ($path = $databaseNode->getPath()->getNodeValue()->__toString()) {
            $connectionParameters[CreateDatabaseStep::CONNECTION_PARAM_PATH] = $this->getWebappPath(
            ) . DIRECTORY_SEPARATOR . $path;
        }

        if ($databaseNode->getDatabaseName()) {
            $databaseName = $databaseNode->getDatabaseName()->getNodeValue()->__toString();
            $connectionParameters[CreateDatabaseStep::CONNECTION_PARAM_DATABASENAME] = $databaseName;
        }

        // set the connection parameters
        return $connectionParameters;
    }
}
