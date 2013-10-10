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

use TechDivision\ApplicationServer\Api\Node\NodeInterface;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
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
        $this->storageNode = $storageNode;
        $this->flush();
        $this->init();
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
    public abstract function init();

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
     *
     * @see \TechDivision\ApplicationServer\InitialContext\StorageInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }
}