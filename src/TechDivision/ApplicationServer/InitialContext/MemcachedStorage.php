<?php
/**
 * TechDivision\ApplicationServer\InitialContext\MemcachedStorage
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage InitialContext
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\InitialContext;

/**
 * Class MemcachedStorage
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage InitialContext
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class MemcachedStorage extends AbstractStorage
{

    /**
     * (non-PHPdoc)
     *
     * @return void
     * @see TechDivision\ApplicationServer\InitialContext\AbstractStorage::init();
     */
    public function init()
    {
        // initialze the memcache servers
        $this->storage = new \Memcached(__CLASS__);
        $serverList = $this->storage->getServerList();
        if (empty($serverList)) {
            $this->storage->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
            foreach ($this->getStorageNode()->getServers() as $server) {
                $this->storage->addServer($server->getAddress(), $server->getPort(), $server->getWeight());
            }
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @param string  $entryIdentifier Something which identifies the data - depends on concrete cache
     * @param mixed   $data            The data to cache - also depends on the concrete cache implementation
     * @param array   $tags            Tags to associate with this cache entry
     * @param integer $lifetime        Lifetime of this cache entry in seconds. If NULL is specified,
     *                                 the default lifetime is used. "0" means unlimited lifetime.
     *
     * @return void
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::set()
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = null)
    {
        // create a unique cache key and add the passed value to the storage
        $cacheKey = $this->getIdentifier() . $entryIdentifier;
        $this->storage->set($cacheKey, $data, $lifetime);

        // if tags has been set, tag the data additionally
        foreach ($tags as $tag) {
            $tagData = $this->get($this->getIdentifier() . $tag);
            if (is_array($tagData) && in_array($cacheKey, $tagData, true) === true) {
                // do nothing here
            } elseif (is_array($tagData) && in_array($cacheKey, $tagData, true) === false) {
                $tagData[] = $cacheKey;
            } else {
                $tagData = array(
                    $cacheKey
                );
            }
            $this->storage->set($tag, $tagData);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $entryIdentifier Something which identifies the cache entry - depends on concrete cache
     *
     * @return mixed
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::get()
     */
    public function get($entryIdentifier)
    {
        return $this->storage->get($entryIdentifier);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $entryIdentifier An identifier specifying the cache entry
     *
     * @return boolean TRUE if such an entry exists, FALSE if not
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::remove()
     */
    public function remove($entryIdentifier)
    {
        $this->storage->delete($this->getIdentifier() . $entryIdentifier);
    }

    /**
     * (non-PHPdoc)
     *
     * @return array
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {
        $this->storage->getAllKeys();
    }
}
