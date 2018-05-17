<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\Runlevels
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

namespace AppserverIo\Appserver\Core\Utilities;

use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * Utility class that contains the application server's runlevels.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Runlevels
{

    /**
     * String mappings for the runlevels.
     *
     * @var array
     */
    protected $runlevels = array();

    /**
     * The singleton instance.
     *
     * @var \AppserverIo\Appserver\Core\Utilities\Runlevels
     */
    protected static $instance;

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     */
    private function __construct()
    {
        // initialize the array with the runlevels
        $this->runlevels = array(
            'shutdown'       => ApplicationServerInterface::SHUTDOWN,
            'administration' => ApplicationServerInterface::ADMINISTRATION,
            'daemon'         => ApplicationServerInterface::DAEMON,
            'network'        => ApplicationServerInterface::NETWORK,
            'secure'         => ApplicationServerInterface::SECURE,
            'full'           => ApplicationServerInterface::FULL,
            'reboot'         => ApplicationServerInterface::REBOOT
        );
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Return's the singleton instance.
     *
     * @return \AppserverIo\Appserver\Core\Utilities\Runlevels The singleton instance
     */
    public static function singleton()
    {
        if (Runlevels::$instance == null) {
            Runlevels::$instance = new Runlevels();
        }
        return Runlevels::$instance;
    }

    /**
     * Return's an array with the available runlevels.
     *
     * @return array The available runlevels
     */
    public function getRunlevels()
    {
        return $this->runlevels;
    }

    /**
     * Return's the value for the passed runlevel representation.
     *
     * @param string $runlevel The runlevel to return the value for
     *
     * @return integer The runlevel's value
     */
    public function getRunlevel($runlevel)
    {
        return $this->runlevels[$runlevel];
    }

    /**
     * Query whether or not, the passed runlevel is valid.
     *
     * @param string $runlevel The runlevel to query
     *
     * @return boolean TRUE if the runlevel is valid, else FALSE
     */
    public function isRunlevel($runlevel)
    {
        return isset($this->runlevels[$runlevel]);
    }
}
