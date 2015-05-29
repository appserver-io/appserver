<?php

/**
 * AppserverIo\Appserver\Core\Commands\ModeCommand
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

namespace AppserverIo\Appserver\Core\Commands;

use AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface;

/**
 * The mode command implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ModeCommand implements CommandInterface
{

    const COMMAND = 'mode';

    public function __construct(ApplicationServerInterface $applicationServer)
    {
        $this->applicationServer = $applicationServer;
    }

    public function execute(array $params = array())
    {
        $this->applicationServer->mode(array_shift($params));
    }
}
