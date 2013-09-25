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
     * Primary key field to use for container entity.
     *
     * @var string
     */
    const PRIMARY_KEY = 'id';

    /**
     * Return's all container configurations.
     *
     * @return array<\stdClass> An array with all container configurations
     */
    public function findAll()
    {
        $containers = array();
        $counter = 1;
        foreach ($this->getSystemConfiguration()->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {
            $containerConfiguration->setData(self::PRIMARY_KEY, $counter++);
            $containers[] = $this->normalize($containerConfiguration);
        }
        return array('containers' => $containers);
    }

    /**
     * Returns the containers with the passed name.
     *
     * @param integer $id
     *            ID of the container to return
     * @return \stdClass The container with the ID passed as parameter
     */
    public function load($id)
    {
        $containers = $this->findAll();
        foreach ($containers['containers'] as $container) {
            if ($container->{self::PRIMARY_KEY} == $id) {
                return array('container' => $container);
            }
        }
    }

    public function create($stdClass)
    {
        error_log(var_export($stdClass, true));
    }

    public function update($stdClass)
    {
        error_log(var_export($stdClass, true));
    }

    public function delete($id)
    {
        error_log("Now deleting ID: $id");
    }
}