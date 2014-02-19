<?php
/**
 * TechDivision\ApplicationServer\Api\Node\DatabaseNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer a datasource.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DatabaseNode extends AbstractNode
{

    /**
     * The database driver information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DriverNode
     * @AS\Mapping(nodeName="driver", nodeType="TechDivision\ApplicationServer\Api\Node\DriverNode")
     */
    protected $driver;

    /**
     * The database user information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\UserNode
     * @AS\Mapping(nodeName="user", nodeType="TechDivision\ApplicationServer\Api\Node\UserNode")
     */
    protected $user;

    /**
     * The database password information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\PasswordNode
     * @AS\Mapping(nodeName="password", nodeType="TechDivision\ApplicationServer\Api\Node\PasswordNode")
     */
    protected $password;

    /**
     * The database name information.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DatabaseNameNode
     * @AS\Mapping(nodeName="databaseName", nodeType="TechDivision\ApplicationServer\Api\Node\DatabaseNameNode")
     */
    protected $databaseName;

    /**
     * The database path information (when using sqlite for example).
     *
     * @var \TechDivision\ApplicationServer\Api\Node\PathNode
     * @AS\Mapping(nodeName="path", nodeType="TechDivision\ApplicationServer\Api\Node\PathNode")
     */
    protected $path;

    /**
     * The database host information
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DatabaseHostNode
     * @AS\Mapping(nodeName="databaseHost", nodeType="TechDivision\ApplicationServer\Api\Node\DatabaseHostNode")
     */
    protected $databaseHost;

    /**
     * Returns the database driver information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DriverNode The database driver information
     */
    public function getDriver()
    {
        return $this->driver;
    }

    /**
     * Returns the database user information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\UserNode The database user information
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Returns the database password information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\PasswordNode The database password information
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Returns the database name information.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DatabaseNameNode The database name information
     */
    public function getDatabaseName()
    {
        return $this->databaseName;
    }

    /**
     * Returns the database path information (when using sqlite for example).
     *
     * @return \TechDivision\ApplicationServer\Api\Node\PathNode The database path information
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Returns the database host information
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DatabaseHostNode The database host information
     */
    public function getDatabaseHost()
    {
        return $this->databaseHost;
    }
}
