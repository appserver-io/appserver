<?php

/**
 * TechDivision\ApplicationServer\Api\VhostService
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
 * A stateless session bean implementation handling the vhost data.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class VhostService extends AbstractService
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
    const XPATH_VHOST = '/container/host/vhosts/vhost';

    /**
     * Returns all vhost configurations.
     *
     * @return \stdClass The vhost configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {

        $vhosts = array();
        $containerIds = 1;
        $vhostIds = 1;
        $hostIds = 1;

        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        $hostService = $this->newService('TechDivision\ApplicationServer\Api\HostService');

        $result = new \stdClass();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $host = $hostService->loadByContainerId($containerIds)->host;

            foreach ($containerConfiguration->getChilds(self::XPATH_VHOST) as $vhostConfiguration) {

                $vhostConfiguration->setData(self::PRIMARY_KEY, $vhostIds);
                $vhost = $this->normalize($vhostConfiguration);

                if (array_key_exists($vhost->name, $vhosts)) {
                    $vhosts[$vhost->name]->container_ids[] = $containerIds;
                    $vhosts[$vhost->name]->host_ids[] = $host->id;
                } else {
                    $vhosts[$vhost->name] = $vhost;
                    $vhosts[$vhost->name]->container_ids = array();
                    $vhosts[$vhost->name]->container_ids[] = $containerIds;
                    $vhosts[$vhost->name]->host_ids = array();
                    $vhosts[$vhost->name]->host_ids[] = $host->id;
                    $vhostIds ++;
                }
            }
            $containerIds ++;
        }

        $result->vhosts = array_values($vhosts);

        return $result;
    }

    /**
     * Returns all vhosts having the passed application base directory.
     *
     * @param string $appBase The application base directory to return the vhosts for
     * @return \stdClass The vhosts having the passed application base directory
     */
    public function findAllByAppBase($appBase)
    {
        $vhosts = array();
        $result = new \stdClass();

        foreach ($this->findAll()->vhosts as $vhost) {
            if ($vhost->app_base == $appBase) {
                $vhosts[] = $vhost;
            }
        }

        $result->vhosts = $vhosts;

        return $result;
    }

    /**
     * Returns the vhost with the passed name.
     *
     * @param string $name
     *            Name of the vhost to return
     * @return \stdClass The vhost with the name passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($id)
     */
    public function load($id)
    {}

    /**
     * Creates a new vhost based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the vhost to be created
     * @return string The new ID of the created vhost
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create(\stdClass $stdClass)
     */
    public function create(\stdClass $stdClass)
    {}

    /**
     * Updates the vhost with the passed data.
     *
     * @param \stdClass $stdClass
     *            The vhost data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update(\stdClass $stdClass)
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * Deletes the vhost with passed ID.
     *
     * @param string $id
     *            The ID of the vhost to be deleted
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::delete($id)
     */
    public function delete($id)
    {}
}