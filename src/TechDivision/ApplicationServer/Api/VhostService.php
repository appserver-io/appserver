<?php

/**
 * TechDivision\ApplicationServerApi\Services\VhostProcessor
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
     * @return array<\TechDivision\ApplicationServer\Vhost> An array with all vhost configurations
     */
    public function getVhostOverviewData()
    {}

    /**
     * Returns the vhost with the passed name.
     *
     * @param string $name
     *            Name of the vhost to return
     * @return \TechDivision\ApplicationServer\Vhost The vhost with the name passed as parameter
     */
    public function getVhostViewData($name)
    {}
}