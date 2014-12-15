<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockInitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core\Mock;

use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Mock\InitialContext\MockSystemLogger;

/**
 *
 * @package AppserverIo\Appserver\Core
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