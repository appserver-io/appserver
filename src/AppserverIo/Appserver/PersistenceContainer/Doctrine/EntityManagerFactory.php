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
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode;
use AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactory\CacheConfigurationNodeInterface;

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
                $annotationRegistry->getDirectoriesAsArray()
            );
        }

        // globally ignore configured annotations to ignore
        foreach ($persistenceUnitNode->getIgnoredAnnotations() as $ignoredAnnotation) {
            AnnotationReader::addGlobalIgnoredName($ignoredAnnotation->getNodeValue()->__toString());
        }

        // load the metadata configuration
        $metadataConfiguration = $persistenceUnitNode->getMetadataConfiguration();

        // prepare the setup properties
        $absolutePaths = $metadataConfiguration->getDirectoriesAsArray();
        $isDevMode = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_IS_DEV_MODE);
        $proxyDir = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_PROXY_DIR);
        $proxyNamespace = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_PROXY_NAMESPACE);
        $autoGenerateProxyClasses = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_AUTO_GENERATE_PROXY_CLASSES);
        $useSimpleAnnotationReader = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_USE_SIMPLE_ANNOTATION_READER);

        // load the factory method from the available mappings
        $factoryMethod = EntityManagerFactory::$metadataMapping[$metadataConfiguration->getType()];

        // create the database configuration and initialize the entity manager
        /** @var \Doctrine\DBAL\Configuration $configuration */
        $configuration = Setup::$factoryMethod($absolutePaths, $isDevMode, $proxyDir, null, $useSimpleAnnotationReader);

        // initialize the metadata cache configuration
        $configuration->setMetadataCacheImpl(
            EntityManagerFactory::getCacheImpl(
                $persistenceUnitNode,
                $persistenceUnitNode->getMetadataCacheConfiguration()
            )
        );

        // initialize the query cache configuration
        $configuration->setQueryCacheImpl(
            EntityManagerFactory::getCacheImpl(
                $persistenceUnitNode,
                $persistenceUnitNode->getQueryCacheConfiguration()
            )
        );

        // initialize the result cache configuration
        $configuration->setResultCacheImpl(
            EntityManagerFactory::getCacheImpl(
                $persistenceUnitNode,
                $persistenceUnitNode->getResultCacheConfiguration()
            )
        );

        // proxy configuration
        $configuration->setProxyDir($proxyDir);
        $configuration->setProxyNamespace($proxyNamespace);
        $configuration->setAutoGenerateProxyClasses($autoGenerateProxyClasses);

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

        // initialize and return a entity manager decorator instance
        return new DoctrineEntityManagerDecorator(
            EntityManager::create(ConnectionUtil::get($application)->fromDatabaseNode($databaseNode), $configuration)
        );
    }

    /**
     * Factory method to create a new cache instance from the passed configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface                                 $persistenceUnit    The persistence unit node
     * @param \AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactory\CacheConfigurationNodeInterface $cacheConfiguration The cache configuration
     *
     * @return \Doctrine\Common\Cache\CacheProvider The cache instance
     */
    public static function getCacheImpl(
        PersistenceUnitNodeInterface $persistenceUnit,
        CacheConfigurationNodeInterface $cacheConfiguration
    ) {

        // load the factory class
        $factory = $cacheConfiguration->getFactory();

        // create a cache instance
        $cache = $factory::get($cacheConfiguration->getParams());
        $cache->setNamespace(sprintf('dc2_%s_', md5($persistenceUnit->getName())));

        // return the cache instance
        return $cache;
    }
}
