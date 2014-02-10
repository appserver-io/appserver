<?php
/**
 * TechDivision\ApplicationServer\Api\Node\ReceiverNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a receiver.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ReceiverNode extends AbstractParamsNode
{

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
     * @var \TechDivision\ApplicationServer\Api\Node\ThreadNode
     * @AS\Mapping(nodeName="thread", nodeType="TechDivision\ApplicationServer\Api\Node\ThreadNode")
     */
    protected $thread;

    /**
     * The worker configuration the receiver uses.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\WorkerNode
     * @AS\Mapping(nodeName="worker", nodeType="TechDivision\ApplicationServer\Api\Node\WorkerNode")
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
     * @return \TechDivision\ApplicationServer\Api\Node\ThreadNode The thread configuration
     */
    public function getThread()
    {
        return $this->thread;
    }

    /**
     * Returns the worker configuration the receiver uses.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ThreadNode The worker configuration
     */
    public function getWorker()
    {
        return $this->worker;
    }
}
