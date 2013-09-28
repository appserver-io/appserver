<?php

/**
 * TechDivision\ApplicationServer\Api\HostService
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
 * A service that handles host configuration data.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class HostService extends AbstractService
{

    /**
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * XPath expression for a container's host configuration.
     *
     * @var string
     */
    const XPATH_HOST = '/container/host';

    /**
     * Return's all host configurations.
     *
     * @return array<\stdClass> An array with all host configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $hosts = array();
        $hostIds = 1;
        $containerIds = 1;

        $result = new \stdClass();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $hostConfiguration = $containerConfiguration->getChild(self::XPATH_HOST);

            $hostConfiguration->setData(self::PRIMARY_KEY, $hostIds);
            $host = $this->normalize($hostConfiguration);

            if (array_key_exists($hostIds, $hosts)) {
                $hosts[$hostIds]->container_ids[] = $containerIds;
            } else {
                $hosts[$hostIds] = $host;
                $hosts[$hostIds]->container_ids = array();
                $hosts[$hostIds]->container_ids[] = $containerIds;
            }

            $hostIds ++;
            $containerIds ++;
        }

        $result->hosts = array_values($hosts);

        return $result;
    }

    /**
     * Returns the host with the passed ID.
     *
     * @param integer $id
     *            ID of the host to return
     * @return \stdClass The host with the ID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($id)
    {
        $result = new \stdClass();
        foreach ($this->findAll()->hosts as $host) {
            if ($host->{self::PRIMARY_KEY} == $id) {
                $result->host = $host;
                return $result;
            }
        }
    }

    /**
     * Returns the host for the container with the passed ID.
     *
     * @param string $containerId
     *            ID of the container to return the host for
     * @return \stdClass The host for the passed container ID
     */
    public function loadByContainerId($containerId)
    {
        foreach ($this->findAll()->hosts as $host) {
            if (in_array($containerId, $host->container_ids)) {
                return $host;
            }
        }
    }

    /**
     * Creates a new host based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the host to be created
     * @return string The new ID of the created host
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create()
     */
    public function create(\stdClass $stdClass)
    {}

    /**
     * Updates the host with the passed data.
     *
     * @param \stdClass $stdClass
     *            The host data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update()
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * Deletes the host with passed ID.
     *
     * @param string $id
     *            The ID of the host to be deleted
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::delete()
     */
    public function delete($id)
    {}
}