<?php

/**
 * TechDivision\ApplicationServer\Provisioning\CreateDatabaseStep
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Provisioning;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * An step implementation that creates a database based on the specified datasource.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Provisioning
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * Executes the functionality for this step, in this case the execution of
     * the PHP script defined in the step configuration.
     *
     * @return void
     * @throws \Exception Is thrown if the script can't be executed
     * @see \TechDivision\ApplicationServer\Provisioning\Step::execute()
     */
    public function execute()
    {

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

        // register the class loader
        $this->getInitialContext()->getClassLoader()->register(true, true);

        // initialize and load the entity manager and the schema tool
        $metadataConfiguration = Setup::createAnnotationMetadataConfiguration($absolutePaths, true);
        $entityManager = EntityManager::create($connectionParameters, $metadataConfiguration);
        $schemaTool = new SchemaTool($entityManager);

        // load the class definitions
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();

        // drop the schema if it already exists and create it new
        $schemaTool->dropSchema($classes);
        $schemaTool->createSchema($classes);

        // set the user rights for the database we've created
        if (isset($connectionParameters[CreateDatabaseStep::CONNECTION_PARAM_PATH])) {
            $this->getService()->setUserRight(
                new \SplFileInfo($connectionParameters[CreateDatabaseStep::CONNECTION_PARAM_PATH])
            );
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

        // set the connection parameters
        return $connectionParameters;
    }
}
