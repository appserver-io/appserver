<?php

/**
 * AppserverIo\Appserver\Application\VirtualHost
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
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\VirtualHostInterface;

/**
 * A basic virtual host class containing virtual host meta information like
 * domain name and base directory.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class VirtualHost extends GenericStackable implements VirtualHostInterface
{

    /**
     * Initializes the vhost with the necessary information.
     *
     * @param string $name    The vhosts domain name
     * @param string $appBase The vhosts base directory
     */
    public function __construct($name, $appBase)
    {
        $this->name = $name;
        $this->appBase = $appBase;
    }

    /**
     * Returns the vhosts domain name.
     *
     * @return string The vhosts domain name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the vhosts base directory.
     *
     * @return string The vhosts base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }

    /**
     * Returns TRUE if the application matches this virtual host configuration.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationInterface $application The application to match
     *
     * @return boolean TRUE if the application matches this virtual host, else FALSE
     */
    public function match(ApplicationInterface $application)
    {
        return trim($this->getAppBase(), '/') === $application->getName();
    }
}
