<?php

/**
 * TechDivision\ApplicationServer\Api\Node\HandlerNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer handler information.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class HandlerNode extends AbstractParamsNode
{

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
     * @var \TechDivision\ApplicationServer\Api\Node\FormatterNode
     * @AS\Mapping(nodeName="formatter", nodeType="TechDivision\ApplicationServer\Api\Node\FormatterNode")
     */
    protected $formatter;

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
     * @return \TechDivision\ApplicationServer\Api\Node\FormatterNode The formatter configuration node
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
}