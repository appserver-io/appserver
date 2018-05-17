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
use AppserverIo\Psr\ApplicationServer\Configuration\DatabaseConfigurationInterface;

/**
 * DTO to transfer a datasource.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DatabaseNode extends AbstractNode implements DatabaseConfigurationInterface
{

    /**
     * The database driver information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="driver", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $driver;

    /**
     * The database user information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="user", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $user;

    /**
     * The database password information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="password", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $password;

    /**
     * The database name information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="databaseName", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $databaseName;

    /**
     * The database path information (when using sqlite for example).
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="path", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $path;

    /**
     * The flag to run Sqlite in memory (mutually exclusive with the path option).
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="memory", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $memory;

    /**
     * The database host information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="databaseHost", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $databaseHost;

    /**
     * The database port information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="databasePort", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $databasePort;

    /**
     * The database charset information.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="charset", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $charset;

    /**
     * The database driver options.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="driverOptions", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $driverOptions;

    /**
     * The name of the socket used to connect to the database.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="unixSocket", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $unixSocket;

    /**
     * The DB platform.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="platform", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $platform;

    /**
     * The server version we want to connect to.
     *
     * @var \AppserverIo\Description\Api\Node\ValueNode
     * @AS\Mapping(nodeName="serverVersion", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $serverVersion;

    /**
     * Returns the database driver information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database driver information
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns the database user information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database user information
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the database password information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database password information
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the database name information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database name information
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Returns the database path information (when using sqlite for example).
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database path information
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the flag to run Sqlite in memory (mutually exclusive with the path option).
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The flag to run Sqlite in memory
     */
    public function getMemory()
    {
        return $this->memory;
    }

    /**
     * Returns the database host information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database host information
     */
    public function getDatabaseHost()
    {
        return $this->databaseHost;
    }

    /**
     * Returns the database port information.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database port information
     */
    public function getDatabasePort()
    {
        return $this->databasePort;
    }

    /**
     * Returns the database charset to use.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database charset
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Returns the database driver options.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The database driver options
     */
    public function getDriverOptions()
    {
        return $this->driverOptions;
    }

    /**
     * Returns the name of the socket used to connect to the database.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The name of the socket
     */
    public function getUnixSocket()
    {
        return $this->unixSocket;
    }

    /**
     * Returns the server version we want to connect to.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The server version to connect to
     */
    public function getServerVersion()
    {
        return $this->serverVersion;
    }

    /**
     * Returns the DB platform.
     *
     * @return \AppserverIo\Description\Api\Node\ValueNode The DB platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }
}
