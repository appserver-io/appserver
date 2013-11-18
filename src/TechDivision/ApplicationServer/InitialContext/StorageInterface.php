<?php

/**
 * TechDivision\ApplicationServer\InitialContext\StorageInterface
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
interface StorageInterface {

    /**
     * "Magic" tag for class-related entries
     */
    const TAG_CLASS = '%CLASS%';

    /**
     * "Magic" tag for package-related entries
     */
    const TAG_PACKAGE = '%PACKAGE%';

    /**
     * Pattern an entry identifier must match.
     */
    const PATTERN_ENTRYIDENTIFIER = '/^[a-zA-Z0-9_%\-&]{1,250}$/';

    /**
     * Pattern a tag must match.
     */
    const PATTERN_TAG = '/^[a-zA-Z0-9_%\-&]{1,250}$/';

    /**
     * Returns this cache's identifier
     *
     * @return string The identifier for this cache
     * @api
     */
    public function getIdentifier();
    
    /**
     * Saves data in the cache.
     *
     * @param string $entryIdentifier Something which identifies the data - depends on concrete cache
     * @param mixed $data The data to cache - also depends on the concrete cache implementation
     * @param array $tags Tags to associate with this cache entry
     * @param integer $lifetime Lifetime of this cache entry in seconds. If NULL is specified, the default lifetime is used. "0" means unlimited lifetime.
     * @return void
     * @api
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL);

    /**
     * Finds and returns data from the cache.
     *
     * @param string $entryIdentifier Something which identifies the cache entry - depends on concrete cache
     * @return mixed
     * @api
     */
    public function get($entryIdentifier);

    /**
     * Finds and returns all cache entries which are tagged by the specified tag.
     *
     * @param string $tag The tag to search for
     * @return array An array with the identifier (key) and content (value) of all matching entries. An empty array if no entries matched
     * @api
     */
    public function getByTag($tag);

    /**
     * Checks if a cache entry with the specified identifier exists.
     *
     * @param string $entryIdentifier An identifier specifying the cache entry
     * @return boolean TRUE if such an entry exists, FALSE if not
     * @api
     */
    public function has($entryIdentifier);

    /**
     * Removes the given cache entry from the cache.
     *
     * @param string $entryIdentifier An identifier specifying the cache entry
     * @return boolean TRUE if such an entry exists, FALSE if not
     * @api
     */
    public function remove($entryIdentifier);

    /**
     * Removes all cache entries of this cache.
     *
     * @return void
     * @api
     */
    public function flush();

    /**
     * Removes all cache entries of this cache which are tagged by the specified tag.
     *
     * @param string $tag The tag the entries must have
     * @return void
     * @api
     */
    public function flushByTag($tag);

    /**
     * Does garbage collection
     *
     * @return void
     * @api
     */
    public function collectGarbage();

    /**
     * Checks the validity of an entry identifier. Returns true if it's valid.
     *
     * @param string $identifier An identifier to be checked for validity
     * @return boolean
     * @api
     */
    public function isValidEntryIdentifier($identifier);

    /**
     * Checks the validity of a tag. Returns true if it's valid.
     *
     * @param string $tag A tag to be checked for validity
     * @return boolean
     * @api
     */
    public function isValidTag($tag);
    
    /**
     * Returns the keys for all values stored.
     * 
     *  @return array
     *  @api
     */
    public function getAllKeys();
    
    /**
     * Returns the internal storage object that
     * handles data persistence.
     * 
     * @return object The storage object itself
     */
    public function getStorage();
}