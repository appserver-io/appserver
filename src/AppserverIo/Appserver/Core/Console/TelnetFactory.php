<?php

/**
 * AppserverIo\Appserver\Core\Console\TelnetFactory
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Console;

use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * Factory to create new telnet instances.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TelnetFactory
{

    /**
     * Factory method to create new telnet console instances.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface $applicationServer The application server instance
     *
     * @return AppserverIo\Appserver\Core\Console\ConsoleInterface The telnet console instance
     */
    public static function factory(ApplicationServerInterface $applicationServer)
    {
        return new Telnet($applicationServer);
    }
}
