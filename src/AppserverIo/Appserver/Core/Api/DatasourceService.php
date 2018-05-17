<?php

/**
 * \AppserverIo\Appserver\Core\Api\DatasourceService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * This services provides access to the deployed datasources and allows
 * to deploy new datasources or remove a deployed one.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DatasourceService extends AbstractService
{

    /**
     * Returns all deployed applications.
     *
     * @return array<\AppserverIo\Appserver\Core\Api\Node\DatasourceNode> All deployed applications
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::findAll()
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
     * Returns an array with the datasources with the passed name.
     *
     * @param string $name Name of the datasource to return
     *
     * @return array The datasources with the name passed as parameter
     */
    public function findAllByName($name)
    {
        $datasourceNodes = array();
        foreach ($this->findAll() as $datasourceNode) {
            if ($datasourceNode->getName() === $name) {
                $datasourceNodes[$datasourceNode->getPrimaryKey()] = $datasourceNode;
            }
        }

        return $datasourceNodes;
    }

    /**
     * Returns the datasource with the passed name.
     *
     * @param string $name Name of the datasource to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatasourceNode The datasource with the name passed as parameter
     */
    public function findByName($name)
    {
        foreach ($this->findAll() as $datasourceNode) {
            if ($datasourceNode->getName() === $name) {
                return $datasourceNode;
            }
        }
    }

    /**
     * Returns the datasource with the passed UUID.
     *
     * @param string $uuid UUID of the datasource to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatasourceNode The datasource with the UUID passed as parameter
     * @see \AppserverIo\Psr\ApplicationServer\ServiceInterface::load()
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
     * Persists the passed datasource.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeInterface $datasourceNode The datasource to persist
     *
     * @return void
     */
    public function persist(NodeInterface $datasourceNode)
    {
        $systemConfiguration = $this->getSystemConfiguration();
        $systemConfiguration->attachDatasource($datasourceNode);
        $this->setSystemConfiguration($systemConfiguration);
    }
}
