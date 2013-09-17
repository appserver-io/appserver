<?php

/**
 * TechDivision\ApplicationServer\Mock\MockApplication
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock;

use TechDivision\ApplicationServer\AbstractApplication;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockApplication extends AbstractApplication
{

    /**
     * (non-PHPdoc)
     * @see \TechDivision\ApplicationServer\AbstractApplication::connect()
     */
    public function connect()
    {
        return $this;
    }
}