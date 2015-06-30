<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\Doctrine\EntityManagerFactory
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

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use AppserverIo\Psr\Application\ApplicationInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode;
use AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface;

/**
 * Factory implementation for a Doctrin EntityManager instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EntityManagerFactory
{

    /**
     * Mapping metadata type to factory method.
     *
     * @var array
     */
    protected static $metadataMapping = array(
        'xml' => 'createXMLMetadataConfiguration',
        'yaml' => 'createYAMLMetadataConfiguration',
        'annotation' => 'createAnnotationMetadataConfiguration'
    );

    /**
     * Creates a new entity manager instance based on the passed configuration.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface                 $application         The application instance to create the entity manager for
     * @param \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface $persistenceUnitNode The datasource configuration
     *
     * @return object The entity manager instance
     */
    public static function factory(ApplicationInterface $application, PersistenceUnitNodeInterface $persistenceUnitNode)
    {

        // register additional annotation libraries
        foreach ($persistenceUnitNode->getAnnotationRegistries() as $annotationRegistry) {
            AnnotationRegistry::registerAutoloadNamespace(
                $annotationRegistry->getNamespace(),
                $annotationRegistry->getDirectoriesAsArray($application->getWebappPath())
            );
        }

        // load the metadata configuration
        $metadataConfiguration = $persistenceUnitNode->getMetadataConfiguration();

        // prepare the setup properties
        $absolutePaths = $metadataConfiguration->getDirectoriesAsArray($application->getWebappPath());
        $proxyDir = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_PROXY_DIR);
        $isDevMode = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_IS_DEV_MODE);
        $useSimpleAnnotationReader = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_USE_SIMPLE_ANNOTATION_READER);

        // load the factory method from the available mappings
        $factoryMethod = EntityManagerFactory::$metadataMapping[$metadataConfiguration->getType()];

        // create the database configuration and initialize the entity manager
        $configuration = Setup::$factoryMethod($absolutePaths, $isDevMode, $proxyDir, null, $useSimpleAnnotationReader);

        // load the datasource
        $datasourceNode = null;
        foreach ($application->getInitialContext()->getSystemConfiguration()->getDatasources() as $datasourceNode) {
            if ($datasourceNode->getName() === $persistenceUnitNode->getDatasource()->getName()) {
                break;
            }
        }

        // throw a exception if the configured datasource is NOT available
        if ($datasourceNode == null) {
            throw new \Exception(
                sprintf(
                    'Can\'t find a datasource node for persistence unit %s',
                    $persistenceUnitNode->getDatasource()->getName()
                )
            );
        }

        // initialize the database node
        $databaseNode = $datasourceNode->getDatabase();

        // initialize the connection parameters
        $connectionParameters = array(
            'driver'   => $databaseNode->getDriver()->getNodeValue()->__toString(),
            'user'     => $databaseNode->getUser()->getNodeValue()->__toString(),
            'password' => $databaseNode->getPassword()->getNodeValue()->__toString()
        );

        // initialize the path to the database when we use sqlite for example
        if ($databaseNode->getPath()) {
            if ($path = $databaseNode->getPath()->getNodeValue()->__toString()) {
                $connectionParameters['path'] = $application->getWebappPath() . DIRECTORY_SEPARATOR . $path;
            }
        }

        // add database name if using another PDO driver than sqlite
        if ($databaseNode->getDatabaseName()) {
            $databaseName = $databaseNode->getDatabaseName()->getNodeValue()->__toString();
            $connectionParameters['dbname'] = $databaseName;
        }

        // add database host if using another PDO driver than sqlite
        if ($databaseNode->getDatabaseHost()) {
            $databaseHost = $databaseNode->getDatabaseHost()->getNodeValue()->__toString();
            $connectionParameters['host'] = $databaseHost;
        }

        // initialize and return a entity manager decorator instance
        return new DoctrineEntityManagerDecorator(
            EntityManager::create($connectionParameters, $configuration)
        );
    }
}
