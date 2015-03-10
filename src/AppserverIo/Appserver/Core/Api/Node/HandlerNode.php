<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\HandlerNode
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
 * DTO to transfer handler information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class HandlerNode extends AbstractNode
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The handler's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The handler's formatter configuration.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FormatterNode
     * @AS\Mapping(nodeName="formatter", nodeType="AppserverIo\Appserver\Core\Api\Node\FormatterNode")
     */
    protected $formatter;

    /**
     * Initializes the provisioner node with the necessary data.
     *
     * @param string                                             $type      The provisioner type
     * @param \AppserverIo\Appserver\Core\Api\Node\FormatterNode $formatter The formatter node
     * @param array                                              $params    The handler params
     */
    public function __construct($type = '', FormatterNode $formatter = null, array $params = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->type = $type;
        $this->formatter = $formatter;
        $this->params = $params;
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
     * Returns information about the handler's class name.
     *
     * @return string The handler's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the formatter configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormatterNode The formatter configuration node
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}
