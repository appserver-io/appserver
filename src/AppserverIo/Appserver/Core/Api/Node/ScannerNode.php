<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ScannerNode
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
 * DTO to transfer the provisioner information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ScannerNode extends AbstractNode implements ScannerNodeInterface
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * A directories node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DirectoriesNodeTrait
     */
    use DirectoriesNodeTrait;

    /**
     * The scanner name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The scanner type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The scanner factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * Initializes the scanner node with the necessary data.
     *
     * @param string $name        The scanner name
     * @param string $type        The scanner type
     * @param string $factory     The scanner type
     * @param array  $params      The scanner params
     * @param array  $directories The scanner directories
     */
    public function __construct($name = '', $type = '', $factory = '', array $params = array(), array $directories = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
        $this->params = $params;
        $this->directories = $directories;
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
     * Returns the scanner type.
     *
     * @return string The scanner type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the scanner factory class.
     *
     * @return string The scanner factory class
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Returns the provisioner name.
     *
     * @return string The provisioner name
     */
    public function getName()
    {
        return $this->name;
    }
}
