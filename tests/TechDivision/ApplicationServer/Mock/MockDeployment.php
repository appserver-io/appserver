<?php

/**
 * TechDivision\ApplicationServer\Mock\MockDeployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock;

use TechDivision\ApplicationServer\AbstractDeployment;
use TechDivision\ApplicationServer\Interfaces\ContainerInterface;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockDeployment extends AbstractDeployment
{

    /**
     * Initializes the available applications and adds them to the deployment instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface The container we want to add the applications to
     *
     * @return void
     */
    public function deploy(ContainerInterface $container)
    {
        return $this;
    }
}