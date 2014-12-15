<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\DatabaseNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a datasource.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DatabaseNode extends AbstractNode
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
     * The database host information
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode
     * @AS\Mapping(nodeName="databaseHost", nodeType="AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode")
     */
    protected $databaseHost;

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
     * Returns the database host information
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DatabaseHostNode The database host information
     */
    public function getDatabaseHost()
    {
        return $this->databaseHost;
    }
}
