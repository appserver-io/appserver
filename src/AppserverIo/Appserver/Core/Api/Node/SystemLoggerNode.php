<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SystemLoggerNode
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

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer system logger information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SystemLoggerNode extends AbstractNode
{

    /**
     * The system logger's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * the logger's name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The system logger's channel name.
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
     * Returns information about the system logger's class name.
     *
     * @return string The system logger's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns logger's name
     *
     * @return string The logger's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns information about the system logger's channel name.
     *
     * @return string The system logger's channel name
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
