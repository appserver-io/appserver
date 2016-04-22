<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\SupervisorDeploymentScanner
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
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Scanner;

/**
 * This is a scanner that watches a flat directory for files that changed
 * and restarts the appserver by using the Supervisor start/stop script.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2016 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SupervisorDeploymentScanner extends DeploymentScanner
{

    /**
     * Restart command for a system using Supervisor to handle appserver daemons.
     *
     * @var string
     */
    const SUPERVISORD_INIT_STRING = 'supervisorctl restart appserver';

    /**
     * Returns the Supervisor restart command.
     *
     * @param string      $os          The OS to return the restart command for
     * @param string|null $distVersion Version of the operating system to get the restart command for
     *
     * @return string The restart command
     */
    public function getRestartCommand($os, $distVersion = null)
    {
        return SupervisorDeploymentScanner::SUPERVISORD_INIT_STRING;
    }
}
