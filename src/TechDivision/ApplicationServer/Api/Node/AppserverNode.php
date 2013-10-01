<?php

/**
 * TechDivision\ApplicationServer\Api\Node\AppserverNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer the application server's complete configuration.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AppserverNode extends AbstractNode
{

    /**
     * The node containing information about the base directory.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode
     * @AS\Mapping(nodeName="baseDirectory", nodeType="TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode")
     */
    protected $baseDirectory;

    /**
     * The node containing information about the initial context.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\InitialContextNode
     * @AS\Mapping(nodeName="initialContext", nodeType="TechDivision\ApplicationServer\Api\Node\InitialContextNode")
     */
    protected $initialContext;

    /**
     * The node containing information about the system logger.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\SystemLoggerNode
     * @AS\Mapping(nodeName="systemLogger", nodeType="TechDivision\ApplicationServer\Api\Node\SystemLoggerNode")
     */
    protected $systemLogger;

    /**
     * Array with nodes for the registered containers.
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\ContainerNode>
     * @AS\Mapping(nodeName="containers/container", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ContainerNode")
     */
    protected $containers = array();

    /**
     * Array with the information about the deployed applications.
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\AppNode>
     * @AS\Mapping(nodeName="apps/app", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\AppNode")
     */
    protected $apps = array();

    /**
     * Returns the node with the base directory information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode The base directory information
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Returns the node containing information about the initial context.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode The initial context information
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the node containing information about the system logger.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode The system logger information
     */
    public function getSystemLogger()
    {
        return $this->systemLogger;
    }

    /**
     * Returns the array with all available containers.
     *
     * @return array The available containers
     */
    public function getContainers()
    {
        return $this->containers;
    }

    /**
     * Returns an array with the information about the deployed applications.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\ReceiverNode> The array with the information about the deployed applications
     */
    public function getApps()
    {
        return $this->apps;
    }

    public function attachApp(AppNode $app)
    {
        $this->apps[$app->getPrimaryKey()] = $app;
    }
}