<?php

/**
 * AppserverIo\Appserver\MemcacheServer\Cache
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
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */

namespace AppserverIo\Appserver\MemcacheServer;

use AppserverIo\Appserver\MemcacheProtocol\CacheRequest;

/**
 * Interface for all cache server implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
interface Cache
{

    /**
     * Handle the the passed request instance.
     *
     * @param \AppserverIo\Appserver\MemacheProtocol\CacheRequest $cacheRequest The request instance with the data to handle
     *
     * @return void
     */
    public function request(CacheRequest $cacheRequest);

    /**
     * Reset all attributes for reusing the object.
     *
     * @return void
     */
    public function reset();

    /**
     * Collections the garbage by removing the cache entries from
     * the storage that has been expired.
     *
     * @return void
     */
    public function gc();

    /**
     * Returns following state of the connection, one of resume,
     * reset or close.
     *
     * @return string The state itself
     */
    public function getState();

    /**
     * Returns the response that will be sent back to the client.
     *
     * @return string The response that will be sent back
     */
    public function getResponse();

    /**
     * The new line value used.
     *
     * @return string The new line value
     */
    public function getNewLine();
}
