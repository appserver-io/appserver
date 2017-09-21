<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\DatabaseNode
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

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer a datasource.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DatabaseNode extends AbstractNode implements DatabaseNodeInterface
{

    /**
     * The database driver information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DriverNode
     * @AS\Mapping(nodeName="driver", nodeType="AppserverIo\Appserver\Core\Api\Node\DriverNode")
     */
    protected $driver;

    /**
     * The database user information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\UserNode
     * @AS\Mapping(nodeName="user", nodeType="AppserverIo\Appserver\Core\Api\Node\UserNode")
     */
    protected $user;

    /**
     * The database password information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PasswordNode
     * @AS\Mapping(nodeName="password", nodeType="AppserverIo\Appserver\Core\Api\Node\PasswordNode")
     */
    protected $password;

    /**
     * The database name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatabaseNameNode
     * @AS\Mapping(nodeName="databaseName", nodeType="AppserverIo\Appserver\Core\Api\Node\DatabaseNameNode")
     */
    protected $databaseName;

    /**
     * The database path information (when using sqlite for example).
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PathNode
     * @AS\Mapping(nodeName="path", nodeType="AppserverIo\Appserver\Core\Api\Node\PathNode")
     */
    protected $path;

    /**
     * The flag to run Sqlite in memory (mutually exclusive with the path option).
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\MemoryNode
     * @AS\Mapping(nodeName="memory", nodeType="AppserverIo\Appserver\Core\Api\Node\MemoryNode")
     */
    protected $memory;

    /**
     * The database host information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode
     * @AS\Mapping(nodeName="databaseHost", nodeType="AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode")
     */
    protected $databaseHost;

    /**
     * The database port information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatabasePortNode
     * @AS\Mapping(nodeName="databasePort", nodeType="AppserverIo\Appserver\Core\Api\Node\DatabasePortNode")
     */
    protected $databasePort;

    /**
     * The database charset information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\CharsetNode
     * @AS\Mapping(nodeName="charset", nodeType="AppserverIo\Appserver\Core\Api\Node\CharsetNode")
     */
    protected $charset;

    /**
     * The database driver options.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DriverOptionsNode
     * @AS\Mapping(nodeName="driverOptions", nodeType="AppserverIo\Appserver\Core\Api\Node\DriverOptionsNode")
     */
    protected $driverOptions;

    /**
     * The name of the socket used to connect to the database.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\UnixSocketNode
     * @AS\Mapping(nodeName="unixSocket", nodeType="AppserverIo\Appserver\Core\Api\Node\UnixSocketNode")
     */
    protected $unixSocket;

    /**
     * The DB platform.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PlatformNode
     * @AS\Mapping(nodeName="platform", nodeType="AppserverIo\Appserver\Core\Api\Node\PlatformNode")
     */
    protected $platform;

    /**
     * The server version we want to connect to.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ServerVersionNode
     * @AS\Mapping(nodeName="serverVersion", nodeType="AppserverIo\Appserver\Core\Api\Node\ServerVersionNode")
     */
    protected $serverVersion;

    /**
     * Returns the database driver information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DriverNode The database driver information
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns the database user information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\UserNode The database user information
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the database password information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PasswordNode The database password information
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the database name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseNameNode The database name information
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Returns the database path information (when using sqlite for example).
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PathNode The database path information
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the flag to run Sqlite in memory (mutually exclusive with the path option).
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PathNode The flag to run Sqlite in memory
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Returns the database host information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode The database host information
     */
    public function getDatabaseHost()
    {
        return $this->databaseHost;
    }

    /**
     * Returns the database port information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabasePortNode The database port information
     */
    public function getDatabasePort()
    {
        return $this->databasePort;
    }

    /**
     * Returns the database charset to use.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\CharsetNode The database charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Returns the database driver options.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DriverOpionsNode The database driver options
     */
    public function getDriverOptions()
    {
        return $this->driverOptions;
    }

    /**
     * Returns the name of the socket used to connect to the database.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\UnixSocketNode The name of the socket
     */
    public function getUnixSocket()
    {
        return $this->unixSocket;
    }

    /**
     * Returns the server version we want to connect to.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ServerVersionNode The server version to connect to
     */
    public function getServerVersion()
    {
        return $this->serverVersion;
    }

    /**
     * Returns the DB platform.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PlatformNode The DB platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }
}
