<?php
/**
 * TechDivision\ApplicationServer\InitialContext\ApcuStorage
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
 * Class ApcuStorage
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage InitialContext
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ApcuStorage extends AbstractStorage
{

    /**
     * Initializes the storage when the instance is constructed and the __wakeup() method is invoked.
     *
     * @return void
     * @see TechDivision\ApplicationServer\InitialContext\AbstractStorage::init();
     */
    public function init()
    {
        return;
    }

    /**
     * (non-PHPdoc)
     *
     * @param string  $entryIdentifier Something which identifies the data - depends on concrete cache
     * @param mixed   $data            The data to cache - also depends on the concrete cache implementation
     * @param array   $tags            Tags to associate with this cache entry
     * @param integer $lifetime        Lifetime of this cache entry in seconds.
     *                                 If NULL is specified, the default lifetime is used. "0" means unlimited lifetime.
     *
     * @return void
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::set()
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = null)
    {
        // create a unique cache key and add the passed value to the storage
        $cacheKey = $this->getIdentifier() . $entryIdentifier;
        apc_store($cacheKey, $data, $lifetime);
        
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
            apc_store($tag, $tagData);
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
        return apc_fetch($entryIdentifier);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $entryIdentifier An identifier specifying the cache entry
     *
     * @return boolean TRUE if such an entry exists, FALSE if not
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::has()
     */
    public function has($entryIdentifier)
    {
        return apc_exists($this->getIdentifier() . $entryIdentifier);
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
        return apc_delete($this->getIdentifier() . $entryIdentifier);
    }

    /**
     * (non-PHPdoc)
     *
     * @return array
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {
        $iter = new \APCIterator('user');
        $keys = array();
        foreach ($iter as $item) {
            echo $keys[] = $item['key'];
        }
        return $keys;
    }
}
