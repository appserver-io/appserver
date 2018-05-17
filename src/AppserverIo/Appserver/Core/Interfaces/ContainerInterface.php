<?php

/**
 * \AppserverIo\Appserver\Core\Interfaces\ContainerInterface
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

use AppserverIo\Psr\Application\ApplicationInterface;

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
     * Returns the unique container name from the configuration.
     *
     * @return string The unique container name
     */
    public function getName();

    /**
     * Stops the container and all servers.
     *
     * @return void
     */
    public function stop();

    /**
     * Append the deployed application to the deployment instance
     * and registers it in the system configuration.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application to append
     *
     * @return void
     */
    public function addApplication(ApplicationInterface $application);

    /**
     * The application base directory for this container
     *
     * @return string The application base directory for this container
     */
    public function getAppBase();

    /**
     * Returns TRUE if application provisioning for the container is enabled, else FALSE.
     *
     * @return boolean TRUE if application provisioning is enabled, else FALSE
     */
    public function hasProvisioningEnabled();

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
     * @return \AppserverIo\Psr\ApplicationServer\ContextInterface The initial context instance
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

    /**
     * Will return a new instance of a given service class
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Psr\ApplicationServer\ContextInterface The service instance
     */
    public function newService($className);

    /**
     * @return string
     */
    public function getRunlevel();

    /**
     * Returns the servers tmp directory, append with the passed directory.
     *
     * @param string|null $directoryToAppend The directory to append
     *
     * @return string
     */
    public function getTmpDir($directoryToAppend = null);
}
