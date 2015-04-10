<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\InitialContextNode
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

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer initial context information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class InitialContextNode extends AbstractNode
{

    /**
     * The initial context's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Description for the initial context configuration.
     *
     * @var  \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * Node containing information about the storage implementation used by the inital context.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\StorageNode
     * @AS\Mapping(nodeName="storage", nodeType="AppserverIo\Appserver\Core\Api\Node\StorageNode")
     */
    protected $storage;

    /**
     * Initializes the initial context node with the necessary data.
     *
     * @param string                                               $type        The initial context type
     * @param \AppserverIo\Appserver\Core\Api\Node\DescriptionNode $description A short description
     * @param \AppserverIo\Appserver\Core\Api\Node\StorageNode     $storage     The default storage configuration
     */
    public function __construct($type = '', DescriptionNode $description = null, StorageNode $storage = null)
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->type = $type;
        $this->description = $description;
        $this->storage = $storage;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Appserver\Core\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getType();
    }

    /**
     * Returns information about the initial context's class name.
     *
     * @return string The initial context's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the initial context description.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The initial context description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the storage configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\StorageNode The node with the storage information
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
