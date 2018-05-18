<?php

/**
 * AppserverIo\Appserver\Core\Mock\InitialContext\MockStorage
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
 */
namespace AppserverIo\Appserver\Core\Mock\InitialContext;

use AppserverIo\Storage\AbstractStorage;

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
     * @see \AppserverIo\Storage\AbstractStorage::init();
     */
    public function init()
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
    {
        $this->attributes[$entryIdentifier] = $data;
    }

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::get($entryIdentifier)
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
     * @see \AppserverIo\Storage\StorageInterface::getByTag($tag)
     */
    public function getByTag($tag)
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::has($entryIdentifier)
     */
    public function has($entryIdentifier)
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::remove($entryIdentifier)
     */
    public function remove($entryIdentifier)
    {
        unset($this->attributes[$entryIdentifier]);
    }

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::flush()
     */
    public function flush()
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::flushByTag($tag)
     */
    public function flushByTag($tag)
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::collectGarbage()
     */
    public function collectGarbage()
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::isValidEntryIdentifier($identifier)
     */
    public function isValidEntryIdentifier($identifier)
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::isValidTag($tag)
     */
    public function isValidTag($tag)
    {}

    /**
     *
     * @see \AppserverIo\Storage\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {}
}
