<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\MessageQueuesNode
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
 * DTO to transfer MQs information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageQueuesNode extends AbstractNode
{

    /**
     * The datasources.
     *
     * @var array
     * @AS\Mapping(nodeName="message-queue", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\MessageQueueNode")
     */
    protected $messageQueues;

    /**
     * Return's the array with the MQs.
     *
     * @return array The MQs
     */
    public function getMessageQueues()
    {
        return $this->messageQueues;
    }
}
