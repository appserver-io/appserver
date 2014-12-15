<?php

/**
 * AppserverIo\Appserver\MemcacheServer\GarbageCollector
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Philipp Dittert <pd@appserver.io>
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @link       https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */

namespace AppserverIo\Appserver\MemcacheServer;

/**
 * This thread is responsible for handling the garbage collection.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Philipp Dittert <pd@appserver.io>
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @link       https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
class GarbageCollector extends \Thread
{

    /**
     * Holds the cache API.
     *
     * @var \AppserverIo\Appserver\MemcacheServer\Cache
     */
    protected $cache;

    /**
     * Constructs the garbage collector instance.
     *
     * @param \AppserverIo\Appserver\MemcacheServer\Cache $cache The cache API
     *
     * @return void
     */
    public function __construct(Cache $cache)
    {

        // set the cache API
        $this->cache = $cache;

        // start server thread
        $this->start();
    }

    /**
     * Returns the context instance.
     *
     * @return \AppserverIo\Appserver\MemcacheServer\Cache The cache API
     */
    public function getCache()
    {
        return $this->cache;
    }

    /**
     * This method is called when the thread is started.
     *
     * @return void
     */
    public function run()
    {
        while (true) {
            $this->getCache()->gc();
        }
    }
}
