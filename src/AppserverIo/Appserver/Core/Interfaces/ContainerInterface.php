<?php

/**
 * AppserverIo\Appserver\Core\Interfaces\ContainerInterface
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
 * Interface for container implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ContainerInterface
{

    /**
     * Returns the containers configuration node.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNode The configuration node
     */
    public function getContainerNode();

    /**
     * Returns the containers naming directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The containers naming directory
     */
    public function getNamingDirectory();

    /**
     * Returns the initial context instance.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ContextInterface The initial context instance
     */
    public function getInitialContext();

    /**
     * Returns the deployed applications.
     *
     * @return \AppserverIo\Storage\GenericStackable The with applications
     */
    public function getApplications();

    /**
     * Returns the application instance with the passed name.
     *
     * @param string $name The name of the application to return
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication($name);
}
