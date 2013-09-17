<?php

/**
 * TechDivision\ApplicationServer\Mock\InitialContext\MockStorage
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock\InitialContext;

use TechDivision\ApplicationServer\InitialContext\AbstractStorage;

/**
 *
 * @package TechDivision\ApplicationServer
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
     * @see TechDivision\ApplicationServer\InitialContext\AbstractStorage::init();
     */
    public function init()
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
     */
    public function set($entryIdentifier, $data, array $tags = array(), $lifetime = NULL)
    {
        $this->attributes[$entryIdentifier] = $data;
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::get($entryIdentifier)
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
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getByTag($tag)
     */
    public function getByTag($tag)
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::has($entryIdentifier)
     */
    public function has($entryIdentifier)
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::remove($entryIdentifier)
     */
    public function remove($entryIdentifier)
    {
        unset($this->attributes[$entryIdentifier]);
    }

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::flush()
     */
    public function flush()
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::flushByTag($tag)
     */
    public function flushByTag($tag)
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::collectGarbage()
     */
    public function collectGarbage()
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::isValidEntryIdentifier($identifier)
     */
    public function isValidEntryIdentifier($identifier)
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::isValidTag($tag)
     */
    public function isValidTag($tag)
    {}

    /**
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getAllKeys()
     */
    public function getAllKeys()
    {}
}