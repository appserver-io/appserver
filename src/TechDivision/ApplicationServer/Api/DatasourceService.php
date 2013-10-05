<?php

/**
 * TechDivision\ApplicationServerApi\DatasourceService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\Api\AbstractService;
use TechDivision\ApplicationServer\Api\Node\DatasourceNode;

/**
 * This services provides access to the deployed datasources and allows
 * to deploy new datasources or remove a deployed one.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class DatasourceService extends AbstractService
{

    /**
     * Returns all deployed applications.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\DatasourceNode> All deployed applications
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $datasourceNodes = array();
        foreach ($this->getSystemConfiguration()->getDatasources() as $datasourceNode) {
            $datasourceNodes[$datasourceNode->getPrimaryKey()] = $datasourceNode;
        }
        return $datasourceNodes;
    }

    /**
     * Returns the datasources with the passed name.
     *
     * @param string $name
     *            Name of the datasource to return
     * @return array<\TechDivision\ApplicationServer\Api\Node\DatasourceNode> The datasources with the name passed as parameter
     */
    public function findAllByName($name)
    {
        $datasourceNodes = array();
        foreach ($this->findAll() as $datasourceNode) {
            if ($datasourceNode->getName() == $name) {
                $datasourceNodes[$datasourceNode->getPrimaryKey()] = $datasourceNode;
            }
        }
        return $datasourceNodes;
    }

    /**
     * Returns the datasource with the passed UUID.
     *
     * @param string $uuid
     *            UUID of the datasource to return
     * @return \TechDivision\ApplicationServer\Api\Node\DatasourceNode The datasource with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {
        foreach ($this->findAll() as $datasourceNode) {
            if ($datasourceNode->getPrimaryKey() == $uuid) {
                return $datasourceNode;
            }
        }
    }

    /**
     * Creates an array with datasources found in the configuration
     * file with the passed name.
     *
     * @param string $filename The filename to initialize the datasources from
     * @return array<\TechDivision\ApplicationServer\Api\Node\DatasourceNode>
     */
    public function initFromFile($filename)
    {

        // initialize the configuration and load the data from the passed file
        $configuration = new Configuration();
        $configuration->initFromFile($filename);

        // iterate over the found datasources, append them to the array and return the array
        $datasourceNodes = array();
        foreach ($configuration->getChilds('/datasources/datasource') as $datasourceConfiguration) {
            $datasourceNode = $this->newInstance('TechDivision\ApplicationServer\Api\Node\DatasourceNode');
            $datasourceNode->initFromConfiguration($datasourceConfiguration);
            $datasourceNodes[$datasourceNode->getPrimaryKey()] = $datasourceNode;
        }
        return $datasourceNodes;
    }

    /**
     * Persists the passed datasource.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\DatasourceNode $datasourceNode The datasource to persist
     * @return void
     */
    public function attachDatasource(DatasourceNode $datasourceNode)
    {
        $systemConfiguration = $this->getSystemConfiguration();
        $systemConfiguration->attachDatasource($datasourceNode);
        $this->setSystemConfiguration($systemConfiguration);
    }
}