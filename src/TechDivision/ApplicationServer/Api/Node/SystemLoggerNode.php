<?php

/**
 * TechDivision\ApplicationServer\Api\Node\SystemLoggerNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer system logger information.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class SystemLoggerNode extends AbstractNode
{

    /**
     * Array with nodes for the registered processors.
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\ProcessorNode>
     * @AS\Mapping(nodeName="processors/processor", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ProcessorNode")
     */
    protected $processors = array();

    /**
     * Array with nodes for the registered handlers.
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\HandlerNode>
     * @AS\Mapping(nodeName="handlers/handler", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\HandlerNode")
     */
    protected $handlers = array();

    /**
     * Returns the array with all registered processors.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\ProcessorNode> The registered processors
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Returns the array with all registered handlers.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\HandlerNode> The registered handlers
     */
    public function getHandlers()
    {
        return $this->handlers;
    }
}