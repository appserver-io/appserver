<?php

/**
 * AppserverIo\Appserver\Core\Mock\MockInitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Mock;

use AppserverIo\Appserver\Core\InitialContext;
use AppserverIo\Appserver\Core\Mock\InitialContext\MockSystemLogger;

/**
 * Mocked initial context
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Thomas Kreidenhuber <t.kreidenhuber@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class MockInitialContext extends InitialContext
{

    /**
     * The system logger instance
     *
     * @var \AppserverIo\Appserver\Core\Mock\InitialContext\MockSystemLogger|\Psr\Log\LoggerInterface $systemLogger
     */
    protected $systemLogger;

    /**
     * Returns the system logger instance
     *
     * @return \AppserverIo\Appserver\Core\Mock\InitialContext\MockSystemLogger|\Psr\Log\LoggerInterface
     */
    public function getSystemLogger() {
        if(!$this->systemLogger) {
            $this->systemLogger = new MockSystemLogger();
        }
        return $this->systemLogger;
    }
}
