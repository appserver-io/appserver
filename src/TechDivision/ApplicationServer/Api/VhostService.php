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
     * Returns all vhost configurations.
     *
     * @return \stdClass The vhost configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {}

    /**
     * Returns the vhost with the passed UUID.
     *
     * @param string $uuid
     *            The UUID of the vhost to return
     * @return \TechDivision\ApplicationServer\Api\Node\VhostNode The vhost with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {}
}