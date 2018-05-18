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

use AppserverIo\Description\Api\Node\QueryCacheConfigurationNode;
use AppserverIo\Description\Api\Node\ResultCacheConfigurationNode;
use AppserverIo\Description\Api\Node\MetadataCacheConfigurationNode;
use AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\CacheFactories\ArrayCacheFactory;

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
     * Initialize the node with the default cache configuration.
     */
    public function __construct()
    {
        $this->queryCacheConfiguration = new QueryCacheConfigurationNode(ArrayCacheFactory::class);
        $this->resultCacheConfiguration = new ResultCacheConfigurationNode(ArrayCacheFactory::class);
        $this->metadataCacheConfiguration = new MetadataCacheConfigurationNode(ArrayCacheFactory::class);
    }
}
