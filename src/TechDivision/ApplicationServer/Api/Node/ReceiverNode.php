<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ReceiverNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a receiver.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
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
