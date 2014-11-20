<?php

/**
 * AppserverIo\Appserver\Core\Mock\InitialContext\MockStorage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core\Mock\InitialContext;

use TechDivision\Storage\AbstractStorage;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockStorage extends AbstractStorage
{

    /**
     * Array storing the attributes.
     *
     * @var array
     */
    protected $attributes = array();

    /**
     *
     * @see AppserverIo\Appserver\Core\InitialContext\AbstractStorage::init();
     */
    public function init()
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
    {
        $this->attributes[$entryIdentifier] = $data;
    }

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::get($entryIdentifier)
     */
    public function get($entryIdentifier)
    {
        if (! array_key_exists($entryIdentifier, $this->attributes)) {
            return false;
        }
        return $this->attributes[$entryIdentifier];
    }

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::getByTag($tag)
     */
    public function getByTag($tag)
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::has($entryIdentifier)
     */
    public function has($entryIdentifier)
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::remove($entryIdentifier)
     */
    public function remove($entryIdentifier)
    {
        unset($this->attributes[$entryIdentifier]);
    }

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::flush()
     */
    public function flush()
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::flushByTag($tag)
     */
    public function flushByTag($tag)
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::collectGarbage()
     */
    public function collectGarbage()
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::isValidEntryIdentifier($identifier)
     */
    public function isValidEntryIdentifier($identifier)
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::isValidTag($tag)
     */
    public function isValidTag($tag)
    {}

    /**
     *
     * @see \AppserverIo\Appserver\Core\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {}
}