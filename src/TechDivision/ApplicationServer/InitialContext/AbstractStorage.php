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

use TechDivision\ApplicationServer\Api\Node\NodeInterface;

/**
 * Class AbstractStorage
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage InitialContext
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractStorage implements StorageInterface
{

    /**
     * The storage configuration node.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\NodeInterface
     */
    protected $storageNode;

    /**
     * A storage backend, \Memcached for example.
     *
     * @var object
     */
    protected $storage;

    /**
     * Unique identifier for the cache storage.
     *
     * @var string
     */
    protected $identifier;

    /**
     * Passes the configuration and initializes the storage.
     *
     * The identifier will be set after the init() function has been invoked, so it'll overwrite the one
     * specified in the configuration if set.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $storageNode The storage configuration node
     * @param string                                                 $identifier  Unique identifier for the cache storage
     *
     * @return void
     */
    public function __construct(NodeInterface $storageNode, $identifier = null)
    {
        $this->storageNode = $storageNode;
        $this->init();
        $this->flush();
        if ($identifier != null) {
            $this->identifier = $identifier;
        }
    }

    /**
     * Restores the storage after the instance has been recovered
     * from sleep.
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->init();
    }

    /**
     * Initializes the storage when the instance is constructed and the
     * __wakeup() method is invoked.
     *
     * @return void
     */
    abstract public function init();

    /**
     * Returns the storage node configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The storage node configuration
     */
    public function getStorageNode()
    {
        return $this->storageNode;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getIdentifier()
     * @return string The identifier for this cache
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * (non-PHPdoc)
     *
     * @return void
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::collectGarbage()
     */
    public function collectGarbage()
    {
        // nothing to do here, because gc is handled by memcache
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $tag The tag to search for
     *
     * @return array An array with the identifier (key) and content (value) of all matching entries. An empty array if no entries matched
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getByTag()
     */
    public function getByTag($tag)
    {
        return $this->get($this->getIdentifier() . $tag);
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
        if ($this->get($this->getIdentifier() . $entryIdentifier) !== false) {
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @return void
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::flush()
     */
    public function flush()
    {
        if ($allKeys = $this->getAllKeys()) {
            foreach ($allKeys as $key) {
                if (substr_compare($key, $this->getIdentifier(), 0)) {
                    $this->remove($key);
                }
            }
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $tag The tag the entries must have
     *
     * @return void
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::flushByTag()
     */
    public function flushByTag($tag)
    {
        $tagData = $this->get($this->getIdentifier() . $tag);
        if (is_array($tagData)) {
            foreach ($tagData as $cacheKey) {
                $this->remove($cacheKey);
            }
            $this->remove($this->getIdentifier() . $tag);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $tag A tag to be checked for validity
     *
     * @return boolean
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::isValidTag()
     */
    public function isValidTag($tag)
    {
        return $this->isValidEntryIdentifier($tag);
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $identifier An identifier to be checked for validity
     *
     * @return boolean
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::isValidEntryIdentifier()
     */
    public function isValidEntryIdentifier($identifier)
    {
        if (preg_match('^[0-9A-Za-z_]+$', $identifier) === 1) {
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @return object The storage object itself
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getStorage()
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
