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

        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {

            $containerConfiguration->setData(self::PRIMARY_KEY, $containerIds ++);
            $container = $this->normalize($containerConfiguration);
            $container->app_ids = array();

            $appIds = 1;
            foreach ($containerConfiguration->getChilds(self::XPATH_APPLICATION) as $application) {
                $container->app_ids[] = $appIds;
                $appIds ++;
            }

            $containers[] = $container;
        }
        return array(
            'containers' => $containers
        );
    }

    /**
     * Returns the containers with the passed name.
     *
     * @param integer $id
     *            ID of the container to return
     * @return \stdClass The container with the ID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load($id)
     */
    public function load($id)
    {
        $containers = $this->findAll();
        foreach ($containers['containers'] as $container) {
            if ($container->{self::PRIMARY_KEY} == $id) {
                return array(
                    'container' => $container
                );
            }
        }
    }

    /**
     * Creates a new container based on the passed information.
     *
     * @param \stdClass $stdClass
     *            The data with the information for the container to be created
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::create($id)
     */
    public function create($stdClass)
    {}

    /**
     * Updates the container with the passed data.
     *
     * @param \stdClass $stdClass
     *            The container data to update
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::update($id)
     */
    public function update($stdClass)
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