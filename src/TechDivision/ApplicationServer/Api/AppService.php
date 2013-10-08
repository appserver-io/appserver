<?php

/**
 * TechDivision\ApplicationServerApi\AppService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Api\AbstractService;
use TechDivision\ApplicationServer\Api\Node\AppNode;

/**
 * This services provides access to the deplyed applications and allows
 * to deploy new applications or remove a deployed one.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AppService extends AbstractService
{

    /**
     * Returns all deployed applications.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\AppNode> All deployed applications
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $appNodes = array();
        foreach ($this->getSystemConfiguration()->getApps() as $appNode) {
            $appNodes[$appNode->getPrimaryKey()] = $appNode;
        }
        return $appNodes;
    }

    /**
     * Returns the applications with the passed name.
     *
     * @param string $name
     *            Name of the application to return
     * @return array<\TechDivision\ApplicationServer\Api\Node\AppNode> The applications with the name passed as parameter
     */
    public function findAllByName($name)
    {
        $appNodes = array();
        foreach ($this->findAll() as $appNode) {
            if ($appNode->getName() == $name) {
                $appNodes[$appNode->getPrimaryKey()] = $appNode;
            }
        }
        return $appNodes;
    }

    /**
     * Returns the application with the passed UUID.
     *
     * @param string $uuid
     *            UUID of the application to return
     * @return \TechDivision\ApplicationServer\Api\Node\AppNode The application with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {;
        foreach ($this->findAll() as $appNode) {
            if ($appNode->getPrimaryKey() == $uuid) {
                return $appNode;
            }
        }
    }

    /**
     * Persists the passed app.
     *
     * @param \TechDivision\ApplicationServer\Api\Node\AppNode $appNode The app to persist
     * @return void
     */
    public function attachApp(AppNode $appNode)
    {
        $systemConfiguration = $this->getSystemConfiguration();
        $systemConfiguration->attachApp($appNode);
        $this->setSystemConfiguration($systemConfiguration);
    }
}