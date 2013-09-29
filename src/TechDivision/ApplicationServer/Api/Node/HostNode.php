<?php

/**
 * TechDivision\ApplicationServer\Api\Node\HostNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a host.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class HostNode extends AbstractNode
{

    /**
     * The host name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The application base directory.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * The server admin mail address.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverAdmin;

    /**
     * The server software string.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverSoftware;

    /**
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\VhostNode>
     * @AS\Mapping(nodeName="vhosts/vhost", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\VhostNode")
     */
    protected $vhosts = array();
}