<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\MessageQueueNode
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
 * DTO to transfer a message queue.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageQueueNode extends AbstractNode implements MessageQueueNodeInterface
{

    /**
     * The type of the message queue receiver.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The message queue destination information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatabaseNode
     * @AS\Mapping(nodeName="destination", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $destination;

    /**
     * Return's the message queue's receiver type.
     *
     * @return string|null The receiver type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return's the message queue's destination information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The message queue destination information
     */
    public function getDestination()
    {
        return $this->destination;
    }
}
