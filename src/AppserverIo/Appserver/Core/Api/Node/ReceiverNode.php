<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ReceiverNode
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

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Description\Api\Node\ParamsNodeTrait;

/**
 * DTO to transfer a receiver.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ReceiverNode extends AbstractNode
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Description\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The receiver's class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The thread configuration the receiver uses.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ThreadNode
     * @DI\Mapping(nodeName="thread", nodeType="AppserverIo\Appserver\Core\Api\Node\ThreadNode")
     */
    protected $thread;

    /**
     * The worker configuration the receiver uses.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\WorkerNode
     * @DI\Mapping(nodeName="worker", nodeType="AppserverIo\Appserver\Core\Api\Node\WorkerNode")
     */
    protected $worker;

    /**
     * Returns information about the receiver's class name.
     *
     * @return string The receiver's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the thread configuration the receiver uses.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ThreadNode The thread configuration
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Returns the worker configuration the receiver uses.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ThreadNode The worker configuration
     */
    public function getWorker()
    {
        return $this->worker;
    }
}
