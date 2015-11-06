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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use AppserverIo\Psr\Application\ApplicationInterface;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\AnnotationReader;
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

        // globally ignore configured annotations to ignore
        foreach ($persistenceUnitNode->getIgnoredAnnotations() as $ignoredAnnotation) {
            AnnotationReader::addGlobalIgnoredName($ignoredAnnotation->getNodeValue()->__toString());
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

        // load the datasource node
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
                    $persistenceUnitNode->getName()
                )
            );
        }

        // load the database node
        $databaseNode = $datasourceNode->getDatabase();

        // throw an exception if the configured database is NOT available
        if ($databaseNode == null) {
            throw new \Exception(
                sprintf(
                    'Can\'t find database node for persistence unit %s',
                    $persistenceUnitNode->getName()
                )
            );
        }

        // load the driver node
        $driverNode = $databaseNode->getDriver();

        // throw an exception if the configured driver is NOT available
        if ($driverNode == null) {
            throw new \Exception(
                sprintf(
                    'Can\'t find driver node for persistence unit %s',
                    $persistenceUnitNode->getName()
                )
            );
        }

        // initialize the connection parameters with the mandatory driver
        $connectionParameters = array(
            'driver' => $databaseNode->getDriver()->getNodeValue()->__toString()
        );

        // initialize the path/memory to the database when we use sqlite for example
        if ($pathNode = $databaseNode->getPath()) {
            $connectionParameters['path'] = $application->getWebappPath() . DIRECTORY_SEPARATOR . $pathNode->getNodeValue()->__toString();
        } elseif ($memoryNode = $databaseNode->getMemory()) {
            $connectionParameters['memory'] = Boolean::valueOf(new String($memoryNode->getNodeValue()->__toString()))->booleanValue();
        } else {
            // do nothing here, because there is NO option
        }

        // add username, if specified
        if ($userNode = $databaseNode->getUser()) {
            $connectionParameters['user'] = $userNode->getNodeValue()->__toString();
        }

        // add password, if specified
        if ($passwordNode = $databaseNode->getPassword()) {
            $connectionParameters['password'] = $passwordNode->getNodeValue()->__toString();
        }

        // add database name if using another PDO driver than sqlite
        if ($databaseNameNode = $databaseNode->getDatabaseName()) {
            $connectionParameters['dbname'] = $databaseNameNode->getNodeValue()->__toString();
        }

        // add database host if using another PDO driver than sqlite
        if ($databaseHostNode = $databaseNode->getDatabaseHost()) {
            $connectionParameters['host'] = $databaseHostNode->getNodeValue()->__toString();
        }

        // add database port if using another PDO driver than sqlite
        if ($databasePortNode = $databaseNode->getDatabasePort()) {
            $connectionParameters['port'] = $databasePortNode->getNodeValue()->__toString();
        }

        // add charset, if specified
        if ($charsetNode = $databaseNode->getCharset()) {
            $connectionParameters['charset'] = $charsetNode->getNodeValue()->__toString();
        }

        // add driver options, if specified
        if ($driverOptionsNode = $databaseNode->getDriverOptions()) {
            // explode the raw options separated with a semicolon
            $rawOptions = explode(';', $driverOptionsNode->getNodeValue()->__toString());

            // prepare the array with the driver options key/value pair (separated with a =)
            $options = array();
            foreach ($rawOptions as $rawOption) {
                list ($key, $value) = explode('=', $rawOption);
                $options[$key] = $value;
            }

            // set the driver options
            $connectionParameters['driverOptions'] = $options;
        }

        // add driver options, if specified
        if ($unixSocketNode = $databaseNode->getUnixSocket()) {
            $connectionParameters['unix_socket'] = $unixSocketNode->getNodeValue()->__toString();
        }

        // initialize and return a entity manager decorator instance
        return new DoctrineEntityManagerDecorator(
            EntityManager::create($connectionParameters, $configuration)
        );
    }
}
