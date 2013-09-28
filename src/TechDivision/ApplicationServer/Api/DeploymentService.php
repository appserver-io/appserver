<?php

/**
 * TechDivision\ApplicationServer\Api\DeploymentService
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
 * A service that handles deployment configuration data.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class DeploymentService extends AbstractService
{

    /**
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * XPath expression for a container's deployment configuration.
     *
     * @var string
     */
    const XPATH_DEPLOYMENT = '/container/deployment';

    /**
     * Return's all deployment configurations.
     *
     * @return array<\stdClass> An array with all deployment configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $deployments = array();
        $deploymentIds = 1;
        $containerIds = 1;

        $result = new \stdClass();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $deploymentConfiguration = $containerConfiguration->getChild(self::XPATH_DEPLOYMENT);

            $deploymentConfiguration->setData(self::PRIMARY_KEY, $deploymentIds);
            $deployment = $this->normalize($deploymentConfiguration);

            if (array_key_exists($deploymentIds, $deployments)) {
                $deployments[$deploymentIds]->deployment->container_ids[] = $containerIds;
            } else {
                $deployments[$deploymentIds] = $deployment;
                $deployments[$deploymentIds]->deployment->container_ids = array();
                $deployments[$deploymentIds]->deployment->container_ids[] = $containerIds;
            }

            $deploymentIds ++;
            $containerIds ++;
        }

        $result->deployments = array_values($deployments);

        return $result;
    }

    /**
     * Returns the deployment with the passed name.
     *
     * @param integer $id
     *            ID of the deployment to return
     * @return \stdClass The deployment with the ID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($id)
    {}

    /**
     * Returns the deployment for the container with the passed ID.
     *
     * @param string $containerId
     *            ID of the container to return the deployment for
     * @return \stdClass The deployment for the passed container ID
     */
    public function loadByContainerId($containerId)
    {
        foreach ($this->findAll()->deployments as $deployment) {
            if (in_array($containerId, $deployment->deployment->container_ids)) {
                return $deployment;
            }
        }
    }

    /**
     * Creates a new deployment based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the deployment to be created
     * @return string The new ID of the created deployment
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create()
     */
    public function create(\stdClass $stdClass)
    {}

    /**
     * Updates the deployment with the passed data.
     *
     * @param \stdClass $stdClass
     *            The deployment data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update()
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * Deletes the deployment with passed ID.
     *
     * @param string $id
     *            The ID of the deployment to be deleted
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::delete()
     */
    public function delete($id)
    {}
}