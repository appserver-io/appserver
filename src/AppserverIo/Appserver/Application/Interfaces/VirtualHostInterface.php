<?php

/**
 * AppserverIo\Appserver\Application\Interfaces\VirtualHostInterface
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
 * @subpackage Application
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application\Interfaces;

use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A virtual host interface.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
interface VirtualHostInterface
{

    /**
     * Returns the vhosts domain name.
     *
     * @return string The vhosts domain name
     */
    public function getName();

    /**
     * Returns the vhosts base directory.
     *
     * @return string The vhosts base directory
     */
    public function getAppBase();

    /**
     * Returns TRUE if the application matches this virtual host configuration.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to match
     *
     * @return boolean TRUE if the application matches this virtual host, else FALSE
     */
    public function match(ApplicationInterface $application);
}
