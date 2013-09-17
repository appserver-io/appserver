<?php

/**
 * TechDivision\ApplicationServer\Mock\MockStatelessSessionBean
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock;

/**
 * The mock stateless session bean implementation.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @Stateless
 */
class MockStatelessSessionBean
{

    protected $aValue;

    public function setAValue($aValue)
    {
        $this->aValue = $aValue;
    }

    public function getAValue()
    {
        return $this->aValue;
    }
}