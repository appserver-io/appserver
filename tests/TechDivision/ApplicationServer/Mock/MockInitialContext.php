<?php

/**
 * TechDivision\ApplicationServer\Mock\MockInitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Mock\InitialContext\MockSystemLogger;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 */
class MockInitialContext extends InitialContext
{

    protected $systemLogger;

    public function getSystemLogger() {
        if(!$this->systemLogger) {
            $this->systemLogger = new MockSystemLogger();
        }
        return $this->systemLogger;
    }

}