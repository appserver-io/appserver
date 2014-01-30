<?php

/**
 * TechDivision\ApplicationServer\InitialContext\StackableStorage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\InitialContext;

use TechDivision\ApplicationServer\GenericStackable;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;

/**
 * A storage implementation that uses a \Stackable to hold the data persistent
 * in memory.
 * 
 * This storage will completely be flushed when the the object is destroyed,
 * there is no automatic persistence functionality available.
 * 
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class StackableStorage extends AbstractStorage
{
    
    /**
     * Passes the configuration and initializes the storage.
     * The identifier will be
     * set after the init() function has been invoked, so it'll overwrite the one
     * specified in the configuration if set.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\NodeInterface $storageNode
     *            The storage configuration node
     * @param string $identifier
     *            Unique identifier for the cache storage
     * @return void
     */
    public function __construct(NodeInterface $storageNode, $identifier = null)
    {
        // initialize the stackable storage
        $this->storage = new GenericStackable();
        $this->storage[__CLASS__] = __FILE__;
        parent::__construct($storageNode, $identifier);
    }

    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\InitialContext\AbstractStorage::init()
     */
    public function init()
    {
        return;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::set()
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = null)
    {
        // create a unique cache key and add the passed value to the storage
        $cacheKey = $this->getIdentifier() . $entryIdentifier;

        // set the data in the storage
        $this->storage->lock();
        $this->storage[$cacheKey] = $data;
        $this->storage->unlock();
        
        // if tags has been set, tag the data additionally
        foreach ($tags as $tag) {
            
            // assemble the tag data
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

            // tag the data
            $this->storage->lock();
            $this->storage[$tag] = $tagData;
            $this->storage->unlock();
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::get()
     */
    public function get($entryIdentifier)
    {
        return $this->storage[$entryIdentifier];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::has()
     */
    public function has($entryIdentifier)
    {
        return isset($this->storage[$this->getIdentifier() . $entryIdentifier]);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::remove()
     */
    public function remove($entryIdentifier)
    {
        if ($this->has($entryIdentifier)) {
            $this->storage->lock();
            unset($this->storage[$this->getIdentifier() . $entryIdentifier]);
            $this->storage->unlock();
            return true;
        }
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {
        $keys = array();
        foreach ($this->storage as $key => $value) {
            $keys[] = $key;
        }
        return $keys;
    }
}
