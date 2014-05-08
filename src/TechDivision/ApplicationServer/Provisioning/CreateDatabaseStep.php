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
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
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
     * Executes the functionality for this step, in this case the execution of
     * the PHP script defined in the step configuration.
     *
     * @return void
     * @throws \Exception Is thrown if the script can't be executed
     * @see \TechDivision\ApplicationServer\Provisioning\Step::execute()
     */
    public function execute()
    {

		error_log(__METHOD__ . ':' . __LINE__);

    	// check if we have a valid datasource node
		if ($this->getDatasourceNode() == null) {
			return;
		}

		error_log(__METHOD__ . ':' . __LINE__);

		// initialize the connection parameters
		$this->initConnectionParameters();

		// prepare the path to the entities
		$absolutePaths = array();
		if ($relativePaths = $this->getStepNode()->getParam(CreateDatabaseStep::PARAM_PATH_TO_ENTITIES)) {
			foreach (explode(PATH_SEPARATOR, $relativePaths) as $relativePath) {
				$absolutePaths[] = $this->getWebappPath() . DIRECTORY_SEPARATOR . $relativePath;
			}
		}

		// initialize and load the entity manager and the schema tool
		$metadataConfiguration = Setup::createAnnotationMetadataConfiguration($absolutePaths, true);
		$entityManager = EntityManager::create($this->getConnectionParameters(), $metadataConfiguration);
		$schemaTool = new SchemaTool($entityManager);

		// load the class definitions
		$classes = $entityManager->getMetadataFactory()->getAllMetadata();

		// drop the schema if it already exists and create it new
		$schemaTool->dropSchema($classes);
		$schemaTool->createSchema($classes);

		error_log(__METHOD__ . ':' . __LINE__);
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
            'driver'   => $databaseNode->getDriver()->getNodeValue()->__toString(),
            'user'     => $databaseNode->getUser()->getNodeValue()->__toString(),
            'password' => $databaseNode->getPassword()->getNodeValue()->__toString()
        );

        // initialize the path to the database when we use sqlite for example
        if ($path = $databaseNode->getPath()->getNodeValue()->__toString()) {
            $connectionParameters['path'] = $this->getWebappPath() . DIRECTORY_SEPARATOR . $path;
        }

        // set the connection parameters
        return $connectionParameters;
    }
}
