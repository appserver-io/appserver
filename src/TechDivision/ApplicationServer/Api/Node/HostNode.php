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
     * The applications base directory.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * The server admin's mail address.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverAdmin;

    /**
     * The server's software signature.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $serverSoftware;

    /**
     * The server's vhosts configuration.
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\VhostNode>
     * @AS\Mapping(nodeName="vhosts/vhost", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\VhostNode")
     */
    protected $vhosts = array();

    /**
     * Returns the host name.
     *
     * @return string The host name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the applications base directory.
     *
     * @return string The applications base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the server admin's mail address.
     *
     * @return string The server admin's mail address
     */
    public function getServerAdmin()
    {
        return $this->serverAdmin;
    }

    /**
     * Returns the server's software signature.
     *
     * @return string The server's software signature
     */
    public function getServerSoftware()
    {
        return $this->serverSoftware;
    }

    /**
     * Returns the server's vhosts configuration.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\VhostNode> The server's vhosts configuration
     */
    public function getVhosts()
    {
        return $this->vhosts;
    }
}