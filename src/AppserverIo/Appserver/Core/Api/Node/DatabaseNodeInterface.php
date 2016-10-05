<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\DatabaseNodeInterface
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a database node implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface DatabaseNodeInterface extends NodeInterface
{

    /**
     * Returns the database driver information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DriverNode The database driver information
     */
    public function getDriver();

    /**
     * Returns the database user information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\UserNode The database user information
     */
    public function getUser();

    /**
     * Returns the database password information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PasswordNode The database password information
     */
    public function getPassword();

    /**
     * Returns the database name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseNameNode The database name information
     */
    public function getDatabaseName();

    /**
     * Returns the database path information (when using sqlite for example).
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PathNode The database path information
     */
    public function getPath();

    /**
     * Returns the flag to run Sqlite in memory (mutually exclusive with the path option).
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PathNode The flag to run Sqlite in memory
     */
    public function getMemory();

    /**
     * Returns the database host information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode The database host information
     */
    public function getDatabaseHost();

    /**
     * Returns the database port information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabasePortNode The database port information
     */
    public function getDatabasePort();

    /**
     * Returns the database charset to use.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\CharsetNode The database charset
     */
    public function getCharset();

    /**
     * Returns the database driver options.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\CharsetNode The database driver options
     */
    public function getDriverOptions();

    /**
     * Returns the name of the socket used to connect to the database.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\CharsetNode The name of the socket
     */
    public function getUnixSocket();

    /**
     * Returns the server version we want to connect to.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\CharsetNode The server version to connect to
     */
    public function getServerVersion();

    /**
     * Returns the DB platform.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PlatformNode The DB platform
     */
    public function getPlatform();
}
