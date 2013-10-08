<?php

/**
 * TechDivision\ApplicationServer\Api\ContainerService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api;

use TechDivision\ApplicationServer\Api\AbstractService;

/**
 * A service that handles container configuration data.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ContainerService extends AbstractService
{

    /**
     * Return's all container node configurations.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\ContainerNode> An array with container node configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        return $this->getSystemConfiguration()->getContainers();
    }

    /**
     * Returns the container for the passed UUID.
     *
     * @param string $uuid
     *            Unique UUID of the container to return
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The container with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($uuid)
     */
    public function load($uuid)
    {
        $containers = $this->findAll();
        if (array_key_exists($uuid, $containers)) {
            return $containers[$uuid];
        }
    }

    /**
     * Returns the application base directory for the container
     * with the passed UUID.
     *
     * @param string $uuid UUID of the container to return the application base directory for
     * @return string The application base directory for this container
     */
    public function getAppBase($uuid)
    {
        return $this->load($uuid)->getHost()->getAppBase();
    }
}