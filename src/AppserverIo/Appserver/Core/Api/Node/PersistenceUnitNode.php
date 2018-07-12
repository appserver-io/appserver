<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNode
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use Doctrine\ORM\Repository\DefaultRepositoryFactory;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Description\Api\Node\QueryCacheConfigurationNode;
use AppserverIo\Description\Api\Node\ResultCacheConfigurationNode;
use AppserverIo\Description\Api\Node\MetadataCacheConfigurationNode;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\CacheFactories\ArrayCacheFactory;
use Doctrine\ORM\EntityRepository;

/**
 * DTO to transfer a applications persistence unit configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PersistenceUnitNode extends \AppserverIo\Description\Api\Node\PersistenceUnitNode
{

    /**
     * The class loaders factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $repositoryFactory;

    /**
     * The default repository class name to use.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $defaultRepositoryClassName;

    /**
     * Initialize the node with the default cache configuration.
     */
    public function __construct()
    {
        $this->defaultRepositoryClassName = EntityRepository::class;
        $this->repositoryFactory = (new ReflectionClass(DefaultRepositoryFactory::class))->getShortname();
        $this->queryCacheConfiguration = new QueryCacheConfigurationNode(ArrayCacheFactory::class);
        $this->resultCacheConfiguration = new ResultCacheConfigurationNode(ArrayCacheFactory::class);
        $this->metadataCacheConfiguration = new MetadataCacheConfigurationNode(ArrayCacheFactory::class);
    }

    /**
     * Returns the entity manager's repository factory class name.
     *
     * @return string The entity manager's repository factory class name
     */
    public function getRepositoryFactory()
    {
        return $this->repositoryFactory;
    }

    /**
     * Returns the entity manager's default repository class name.
     *
     * @return string The entity manager's default repository class name
     */
    public function getDefaultRepositoryClassName()
    {
        return $this->defaultRepositoryClassName;
    }
}
