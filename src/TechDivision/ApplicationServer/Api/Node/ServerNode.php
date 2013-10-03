<?php

/**
 * TechDivision\ApplicationServer\Api\Node\ServerNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer server information.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ServerNode extends AbstractNode
{

    /**
     * The server's IP address.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $address;

    /**
     * The server's port.
     *
     * @var integer
     * @AS\Mapping(nodeType="integer")
     */
    protected $port;

    /**
     * The server's weight.
     *
     * @var integer
     * @AS\Mapping(nodeType="integer")
     */
    protected $weight;

    /**
     * Returns the IP address the server listens to.
     *
     * @return string the IP address the server listens to
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Returns the port the server listens to.
     *
     * @return string the port the server listens to
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Returns weight the server has in the storage cluster.
     *
     * @return integer The weight the server has in the storage cluster
     */
    public function getWeight()
    {
        return $this->weight;
    }
}