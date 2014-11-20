<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\ReceiverNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a receiver.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ReceiverNode extends AbstractNode
{
    // We use several traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
    use ParamsNodeTrait;

    /**
     * The receiver's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The thread configuration the receiver uses.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ThreadNode
     * @AS\Mapping(nodeName="thread", nodeType="AppserverIo\Appserver\Core\Api\Node\ThreadNode")
     */
    protected $thread;

    /**
     * The worker configuration the receiver uses.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\WorkerNode
     * @AS\Mapping(nodeName="worker", nodeType="AppserverIo\Appserver\Core\Api\Node\WorkerNode")
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
