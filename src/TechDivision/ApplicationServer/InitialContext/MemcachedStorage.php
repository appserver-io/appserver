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
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class MemcachedStorage extends AbstractStorage {
    
    /**
     * XPath expression for the initial context's server configurations.
     * @var string
     */
    const XPATH_SERVER = '/storage/servers/server';
    
    /**
     * @see TechDivision\ApplicationServer\InitialContext\AbstractStorage::init();
     */
    public function init() {
        // initialze the memcache servers
        $this->storage = new \Memcached(__CLASS__);
        $serverList = $this->storage->getServerList();
        if (empty($serverList)) {
            $this->storage->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
            foreach ($this->configuration->getChilds(self::XPATH_SERVER) as $server) {
                $this->storage->addServer($server->getAddress(), $server->getPort(), $server->getWeight());
            } 
        } 
    }
    
    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL) {

        $cacheKey = $this->getIdentifier() . $entryIdentifier;

        $this->storage->set($cacheKey, $data, $lifetime);

        foreach ($tags as $tag) {

            $tagData = $this->get($this->getIdentifier() . $tag);

            if (is_array($tagData) && in_array($cacheKey, $tagData, true) === true) {
                // do nothing here
            } elseif (is_array($tagData) && in_array($cacheKey, $tagData, true) === false) {
                $tagData[] = $cacheKey;
            } else {
                $tagData = array($cacheKey);
            }

            $this->storage->set($tag, $tagData);
        }
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::get($entryIdentifier)
     */
    public function get($entryIdentifier) {
        return $this->storage->get($entryIdentifier);
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getByTag($tag)
     */
    public function getByTag($tag) {
        return $this->get($this->getIdentifier() . $tag);
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::has($entryIdentifier)
     */
    public function has($entryIdentifier) {
        if ($this->get($this->getIdentifier() . $entryIdentifier) !== false) {
            return true;
        }
        return false;
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::remove($entryIdentifier)
     */
    public function remove($entryIdentifier) {
        $this->storage->delete($this->getIdentifier() . $entryIdentifier);
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::flush()
     */
    public function flush() {
        foreach ($this->getAllKeys() as $key) {
            if (substr_compare($key, $this->getIdentifier(), 0)) {
                $this->remove($key);
            }
        }
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::flushByTag($tag)
     */
    public function flushByTag($tag) {

        $tagData = $this->get($this->getIdentifier() . $tag);

        if (is_array($tagData)) {

            foreach ($tagData as $cacheKey) {
                $this->remove($cacheKey);
            }

            $this->remove($this->getIdentifier() . $tag);
        }
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::collectGarbage()
     */
    public function collectGarbage() {
        // nothing to do here, because gc is handled by memcache
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::isValidEntryIdentifier($identifier)
     */
    public function isValidEntryIdentifier($identifier) {
        if (preg_match('^[0-9A-Za-z_]+$', $identifier) === 1) {
            return true;
        }
        return false;
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::isValidTag($tag)
     */
    public function isValidTag($tag) {
        return $this->isValidEntryIdentifier($tag);
    }

    /**
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys() {
        $this->storage->getAllKeys();
    }
}