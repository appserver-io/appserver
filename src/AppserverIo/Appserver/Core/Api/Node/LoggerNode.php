<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\LoggerNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer logger information.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoggerNode extends AbstractNode
{

    /**
     * A params node trait.
     *
     * @var \TraitInterface
     */
    use ParamsNodeTrait;

    /**
     * The loggers name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The loggers class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The loggers channel name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $channelName;

    /**
     * Array with nodes for the registered processors.
     *
     * @var array
     * @AS\Mapping(nodeName="processors/processor", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ProcessorNode")
     */
    protected $processors = array();

    /**
     * Array with nodes for the registered handlers.
     *
     * @var array
     * @AS\Mapping(nodeName="handlers/handler", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\HandlerNode")
     */
    protected $handlers = array();

    /**
     * Initializes the node with default values.
     *
     * @param string $name        The loggers name
     * @param string $type        The loggers class name
     * @param string $channelName The loggers channel name
     * @param array  $processors  The array with nodes for the registered processors
     * @param array  $handlers    The array with nodes for the registered handlers
     * @param array  $params      The handler params
     */
    public function __construct($name = '', $type = '', $channelName = '', array $processors = array(), array $handlers = array(), array $params = array())
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
        $this->channelName = $channelName;
        $this->processors = $processors;
        $this->handlers = $handlers;
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
        return $this->getName();
    }

    /**
     * Returns information about the system loggers class name.
     *
     * @return string The system loggers class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns loggers name
     *
     * @return string The loggers name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns information about the system loggers channel name.
     *
     * @return string The system loggers channel name
     */
    public function getChannelName()
    {
        return $this->channelName;
    }

    /**
     * Returns the array with all registered processors.
     *
     * @return array The registered processors
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Returns the array with all registered handlers.
     *
     * @return array The registered handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}
