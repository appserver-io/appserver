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
namespace TechDivision\ApplicationServerApi\Services;

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
     * Returns all vhost configurations.
     *
     * @return array<\stdClass> An array with all vhost configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {}

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