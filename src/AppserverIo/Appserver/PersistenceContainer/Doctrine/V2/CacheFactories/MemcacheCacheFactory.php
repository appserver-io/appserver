<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\CacheFactories\MemcacheCacheFactory
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
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine\V2\CacheFactories;

use Doctrine\Common\Cache\MemcacheCache;

/**
 * The factory implementation for a MemcacheCache cache instance.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */
class MemcacheCacheFactory implements CacheFactoryInterface
{

    /**
     * Return's the new cache instance.
     *
     * @param array $configuration The cache configuration
     *
     * @return \Doctrine\Common\Cache\CacheProvider The cache instance
     */
    public static function get(array $configuration = array())
    {
        if (extension_loaded('memcache')) {
            $memcache = new \Memcache();
            $memcache->connect($configuration[CacheKeys::HOST]);
            $cache = new MemcacheCache();
            $cache->setMemcache($memcache);
            return $cache;
        }
    }
}