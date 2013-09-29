<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ContainerNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a container.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ContainerNode extends AbstractNode
{

    /**
     * The container's name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The container's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The thread class name that start's the container.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $threadType;

    /**
     *
     * @var  \TechDivision\ApplicationServer\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="TechDivision\ApplicationServer\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ReceiverNode
     * @AS\Mapping(nodeName="receiver", nodeType="TechDivision\ApplicationServer\Api\Node\ReceiverNode")
     */
    protected $receiver;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Node\HostNode
     * @AS\Mapping(nodeName="host", nodeType="TechDivision\ApplicationServer\Api\Node\HostNode")
     */
    protected $host;

    /**
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DeploymentNode
     * @AS\Mapping(nodeName="deployment", nodeType="TechDivision\ApplicationServer\Api\Node\DeploymentNode")
     */
    protected $deployment;

    /**
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\AppNode>
     * @AS\Mapping(nodeName="apps/app", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\AppNode")
     */
    protected $apps = array();
}