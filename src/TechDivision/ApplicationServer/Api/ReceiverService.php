<?php

/**
 * TechDivision\ApplicationServer\Api\ReceiverService
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
 * A service that handles receiver configuration data.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ReceiverService extends AbstractService
{

    /**
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * XPath expression for a container's receiver configuration.
     *
     * @var string
     */
    const XPATH_RECEIVER = '/container/receiver';

    /**
     * Return's all receiver configurations.
     *
     * @return \stdClass The receiver configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $receivers = array();
        $receiverIds = 1;
        $containerIds = 1;

        $result = new \stdClass();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $receiverConfiguration = $containerConfiguration->getChild(self::XPATH_RECEIVER);

            $receiverConfiguration->setData(self::PRIMARY_KEY, $receiverIds);
            $receiver = $this->normalize($receiverConfiguration);

            if (array_key_exists($receiverIds, $receivers)) {
                $receivers[$receiverIds]->container_ids[] = $containerIds;
            } else {
                $receivers[$receiverIds] = $receiver;
                $receivers[$receiverIds]->container_ids = array();
                $receivers[$receiverIds]->container_ids[] = $containerIds;
            }

            $receiverIds ++;
            $containerIds ++;
        }

        $result->receivers = array_values($receivers);

        return $result;
    }

    /**
     * Returns the container for the passed ID.
     *
     * @param integer $id
     *            ID of the container to return
     * @return \stdClass The container with the ID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($id)
     */
    public function load($id)
    {
        $result = new \stdClass();
        foreach ($this->findAll()->receivers as $receiver) {
            if ($receiver->{self::PRIMARY_KEY} == $id) {
                return $receiver;
            }
        }
    }

    /**
     * Returns the receiver for the container with the passed ID.
     *
     * @param string $containerId
     *            ID of the container to return the receiver for
     * @return \stdClass The receiver for the passed container ID
     */
    public function loadByContainerId($containerId)
    {
        foreach ($this->findAll()->receivers as $receiver) {
            if (in_array($containerId, $receiver->container_ids)) {
                return $receiver;
            }
        }
    }

    /**
     * Creates a new container based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the container to be created
     * @return string The new ID of the created container
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create(\stdClass $stdClass)
     */
    public function create(\stdClass $stdClass)
    {}

    /**
     * Updates the container with the passed data.
     *
     * @param \stdClass $stdClass
     *            The container data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update(\stdClass $stdClass)
     */
    public function update(\stdClass $stdClass)
    {}

    /**
     * Deletes the container with passed ID.
     *
     * @param string $id
     *            The ID of the container to be deleted
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::delete($id)
     */
    public function delete($id)
    {}
}