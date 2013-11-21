<?php

/**
 * TechDivision\ApplicationServer\InitialContext\MemcachedStorage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\InitialContext;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MemcachedStorage extends AbstractStorage
{

    /**
     *
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
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::set()
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
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
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::get()
     */
    public function get($entryIdentifier)
    {
        return $this->storage->get($entryIdentifier);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::remove()
     */
    public function remove($entryIdentifier)
    {
        $this->storage->delete($this->getIdentifier() . $entryIdentifier);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {
        $this->storage->getAllKeys();
    }
}