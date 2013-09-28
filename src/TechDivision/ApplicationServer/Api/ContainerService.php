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
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * XPath expression for the application configurations.
     *
     * @var string
     */
    const XPATH_APPLICATION = '/container/applications/application';

    /**
     * Return's all container configurations.
     *
     * @return array<\stdClass> An array with all container configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $containers = array();
        $containerIds = 1;

        $result = new \stdClass();

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $containerConfiguration->setData(self::PRIMARY_KEY, $containerIds ++);
            $container = $this->normalize($containerConfiguration);
            $container->{$containerConfiguration->getNodeName()}->app_ids = array();

            $appIds = 1;
            foreach ($containerConfiguration->getChilds(self::XPATH_APPLICATION) as $application) {
                $container->{$containerConfiguration->getNodeName()}->app_ids[] = $appIds;
                $appIds ++;
            }

            $containers[] = $container;
        }

        $result->containers = $containers;

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
        foreach ($this->findAll()->containers as $container) {
            if ($container->container->{self::PRIMARY_KEY} == $id) {
                return $container;
            }
        }
    }

    /**
     * Returns the base directory for the container with
     * the passed ID.
     *
     * @param string $id The ID of the container to return the base directory for
     * @return string The container's base directory, /opt/appserver/webapps by default
     */
    public function getAppBase($id)
    {
        $hostService = $this->newService('TechDivision\ApplicationServer\Api\HostService');
        $host = $hostService->loadByContainerId($id)->host;
        return $this->getBaseDirectory() . $host->app_base;
    }

    /**
     * Returns the server software string for the container with
     * the passed ID.
     *
     * @param string $id The ID of the container to return the server software string for
     * @return string The server software string
     */
    public function getServerSoftware($id)
    {
        $hostService = $this->newService('TechDivision\ApplicationServer\Api\HostService');
        $host = $hostService->loadByContainerId($id)->host;
        return $host->server_software;
    }

    /**
     * Returns the server administration mail for the container with
     * the passed ID.
     *
     * @param string $id The ID of the container to return the server administration mail for
     * @return string The server administration mail
     */
    public function getServerAdmin($id)
    {
        $hostService = $this->newService('TechDivision\ApplicationServer\Api\HostService');
        $host = $hostService->loadByContainerId($id)->host;
        return $host->server_admin;
    }

    /**
     * Returns the receiver type defined in the system configuration.
     *
     * @param string $id The ID of the container to return the receiver type for
     * @return string The receiver type for the container with the passed ID
     */
    public function getReceiverType($id)
    {
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ReceiverService');
        $receiver = $receiverService->loadByContainerId($id)->receiver;
        return $receiver->type;
    }

    /**
     * Returns the worker type defined in the system configuration.
     *
     * @param string $id The ID of the container to return the worker type for
     * @return string The worker type for the container with the passed ID
     */
    public function getWorkerType($id)
    {
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ReceiverService');
        $receiver = $receiverService->loadByContainerId($id)->receiver;
        foreach ($receiver->children as $child) {
            if (property_exists($child, $property = 'worker')) {
                return $child->$property->type;
            }
        }
    }

    /**
     * Returns the thread type defined in the system configuration.
     *
     * @param string $id The ID of the container to return the thread type for
     * @return string The thread type for the container with the passed ID
     */
    public function getThreadType($id)
    {
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ReceiverService');
        $receiver = $receiverService->loadByContainerId($id)->receiver;
        foreach ($receiver->children as $child) {
            if (property_exists($child, $property = 'thread')) {
                return $child->$property->type;
            }
        }
    }

    /**
     * Returns the worker number defined in the system configuration.
     *
     * @param string $id The ID of the container to return the worker number for
     * @return integer The worker number for the container with the passed ID
     */
    public function getWorkerNumber($id)
    {
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ReceiverService');
        $receiver = $receiverService->loadByContainerId($id);
        error_log(var_export($receiver, true));
    }

    /**
     * Returns the IP address defined in the system configuration.
     *
     * @param string $id The ID of the container to return the IP address for
     * @return string The IP address for the container with the passed ID
     */
    public function getAddress($id)
    {
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ReceiverService');
        $receiver = $receiverService->loadByContainerId($id);
        error_log(var_export($receiver, true));
    }

    /**
     * Returns the port defined in the system configuration.
     *
     * @param string $id The ID of the container to return the port for
     * @return string The port for the container with the passed ID
     */
    public function getPort($id)
    {
        $receiverService = $this->newService('TechDivision\ApplicationServer\Api\ReceiverService');
        $receiver = $receiverService->loadByContainerId($id);
        error_log(var_export($receiver, true));
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