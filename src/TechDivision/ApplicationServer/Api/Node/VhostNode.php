<?php

/**
 * TechDivision\ApplicationServer\Api\Node\VhostNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a vhost.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class VhostNode extends AbstractNode
{

    /**
     * The vhost's name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The vhost's application base directory.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * The vhost aliases configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="aliases/alias", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\AliasNode")
     */
    protected $aliases = array();

    /**
     * Returns the vhost's name.
     *
     * @return string The vhost's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the vhost's application base directory.
     *
     * @return string The vhost's application base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns the vhost's aliases configuration.
     *
     * @return array The aliases configuration
     */
    public function getAliases()
    {
        return $this->aliases;
    }
}
