<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\EntityManagerFactory
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

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine\V2;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use AppserverIo\Appserver\Core\Api\Node\AnnotationRegistryNode;
use AppserverIo\Appserver\Core\Api\Node\MetadataConfigurationNode;
use AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\DoctrineEntityManagerDecorator;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\DriverFactories\DriverKeys;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\CacheFactories\CacheConfigurationNodeInterface;

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
            // register the annotations specified by the annotation registery
            $annotationRegistryType = $annotationRegistry->getType();
            $registry = new $annotationRegistryType();
            $registry->register($annotationRegistry);
        }

        // query whether or not an initialize EM configuration is available
        if ($application->hasAttribute($persistenceUnitNode->getName()) === false) {
            // globally ignore configured annotations to ignore
            foreach ($persistenceUnitNode->getIgnoredAnnotations() as $ignoredAnnotation) {
                AnnotationReader::addGlobalIgnoredName($ignoredAnnotation->getNodeValue()->__toString());
            }

            // load the metadata configuration
            $metadataConfiguration = $persistenceUnitNode->getMetadataConfiguration();

            // prepare the setup properties
            $absolutePaths = $metadataConfiguration->getDirectoriesAsArray();
            $proxyDir = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_PROXY_DIR);
            $proxyNamespace = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_PROXY_NAMESPACE);
            $autoGenerateProxyClasses = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_AUTO_GENERATE_PROXY_CLASSES);
            $useSimpleAnnotationReader = $metadataConfiguration->getParam(MetadataConfigurationNode::PARAM_USE_SIMPLE_ANNOTATION_READER);

            // load the metadata driver factory class name
            $metadataDriverFactory = $metadataConfiguration->getFactory();

            // initialize the params to be passed to the factory
            $metadataDriverParams = array(DriverKeys::USE_SIMPLE_ANNOTATION_READER => $useSimpleAnnotationReader);

            // create the database configuration and initialize the entity manager
            /** @var \Doctrine\DBAL\Configuration $configuration */
            $configuration = new Configuration();
            $configuration->setMetadataDriverImpl($metadataDriverFactory::get($configuration, $absolutePaths, $metadataDriverParams));

            // initialize the metadata cache configuration
            $metadataCacheConfiguration = $persistenceUnitNode->getMetadataCacheConfiguration();
            $configuration->setMetadataCacheImpl(
                EntityManagerFactory::getCacheImpl($persistenceUnitNode, $metadataCacheConfiguration)
            );

            // initialize the query cache configuration
            $queryCacheConfiguration = $persistenceUnitNode->getQueryCacheConfiguration();
            $configuration->setQueryCacheImpl(
                EntityManagerFactory::getCacheImpl($persistenceUnitNode, $queryCacheConfiguration)
            );

            // initialize the result cache configuration
            $resultCacheConfiguration = $persistenceUnitNode->getResultCacheConfiguration();
            $configuration->setResultCacheImpl(
                EntityManagerFactory::getCacheImpl($persistenceUnitNode, $resultCacheConfiguration)
            );

            // proxy configuration
            $configuration->setProxyDir($proxyDir = $proxyDir ?: sys_get_temp_dir());
            $configuration->setProxyNamespace($proxyNamespace = $proxyNamespace ?: 'Doctrine\Proxy');
            $configuration->setAutoGenerateProxyClasses($autoGenerateProxyClasses = $autoGenerateProxyClasses ?: true);

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

            // load the connection parameters
            $connectionParameters = ConnectionUtil::get($application)->fromDatabaseNode($databaseNode);

            // append the initialized EM configuration to the application
            $application->setAttribute($persistenceUnitNode->getName(), array($connectionParameters, $configuration));
        }

        // load the initialized EM configuration from the application
        list ($connectionParameters, $configuration) = $application->getAttribute($persistenceUnitNode->getName());

        // initialize and return a entity manager decorator instance
        return new DoctrineEntityManagerDecorator(
            EntityManager::create($connectionParameters, $configuration)
        );
    }

    /**
     * Factory method to create a new cache instance from the passed configuration.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNodeInterface                                    $persistenceUnit    The persistence unit node
     * @param \AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\CacheFactory\CacheConfigurationNodeInterface $cacheConfiguration The cache configuration
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
