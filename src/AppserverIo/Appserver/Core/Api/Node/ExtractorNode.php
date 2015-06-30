<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ExtractorNode
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
 * DTO to transfer the extractor information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ExtractorNode extends AbstractNode implements ExtractorNodeInterface
{

    /**
     * The extractors name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The extractors type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The extractors factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The flag to create backups before deleting the application folder.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $createBackups;

    /**
     * The flag to restore backups after extracting the archive to the application folder.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $restoreBackups;

    /**
     * Initializes the extractor node with the necessary data.
     *
     * @param string  $name           The extractor's name
     * @param string  $type           The extractor's type
     * @param string  $factory        The extractor's factory class name
     * @param boolean $createBackups  The flag to create backups
     * @param boolean $restoreBackups The flag to restore backups
     */
    public function __construct($name = '', $type = '', $factory = '', $createBackups = false, $restoreBackups = false)
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
        $this->createBackups = $createBackups;
        $this->restoreBackups = $restoreBackups;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Appserver\Core\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getName();
    }

    /**
     * Returns the extractor's type.
     *
     * @return string The extractor's type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the extractor's name.
     *
     * @return string The extractor's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the extractor's factory class name.
     *
     * @return string The extractor's factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns the flag that backups should be created.
     *
     * @return boolean The flag to create backups
     */
    public function isCreateBackups()
    {
        return $this->createBackups;
    }

    /**
     * Returns the flag that backups should be restored.
     *
     * @return boolean The flag to restore backups
     */
    public function isRestoreBackups()
    {
        return $this->restoreBackups;
    }
}
