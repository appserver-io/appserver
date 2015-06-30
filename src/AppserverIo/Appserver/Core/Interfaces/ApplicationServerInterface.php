<?php

/**
 * AppserverIo\Appserver\Core\Interfaces\ApplicationServerInterface
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

namespace AppserverIo\Appserver\Core\Interfaces;

/**
 * Interface for the application server instance.

 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ApplicationServerInterface
{

    /**
     * The available runlevels.
     *
     * @var integer
     */
    const SHUTDOWN       = 0;
    const ADMINISTRATION = 1;
    const DAEMON         = 2;
    const NETWORK        = 3;
    const SECURE         = 4;
    const FULL           = 5;
    const REBOOT         = 6;

    /**
     * Translates and returns a string representation of the passed runlevel.
     *
     * @param integer $runlevel The runlevel to return the string representation for
     *
     * @return string The string representation for the passed runlevel
     *
     * @throws \Exception Is thrown if the passed runlevel is not available
     */
    public function runlevelToString($runlevel);

    /**
     * The runlevel to switch to.
     *
     * @param integer $runlevel The new runlevel to switch to
     *
     * @return void
     */
    public function init($runlevel = ApplicationServerInterface::FULL);

    /**
     * Switch to the passed mode, which can either be 'dev' or 'prod'.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function mode($mode);

    /**
     * Query whether the application server should keep running or not.
     *
     * @return boolean TRUE if the server should keep running, else FALSE
     */
    public function keepRunning();
}
