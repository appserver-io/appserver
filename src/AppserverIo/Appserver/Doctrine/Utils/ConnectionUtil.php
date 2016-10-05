<?php

/**
 * AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil
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

namespace AppserverIo\Appserver\Doctrine\Utils;

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\DatabaseNodeInterface;
use Doctrine\DBAL\Platforms\MySQL57Platform;
use Doctrine\DBAL\Platforms\MySqlPlatform;

/**
 * Utility class that helps to prepare the Doctrine DBAL connections.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ConnectionUtil
{

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * This is a utility class, so protect it against direct instantiation.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     */
    private function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Creates a new helper instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return \AppserverIo\Appserver\Doctrine\Utils\DoctrineHelper The instance
     */
    public static function get(ApplicationInterface $application)
    {
        return new ConnectionUtil($application);
    }

    /**
     * Creates an array with the connection parameters for a Doctrine DBAL connection from
     * the passed database node.
     *
     * @param AppserverIo\Appserver\Core\Api\Node\DatabaseNodeInterface $databaseNode The database node to create the connection parameters from
     *
     * @return array The DBAL connection parameters
     */
    public function fromDatabaseNode(DatabaseNodeInterface $databaseNode)
    {

        // initialize the connection parameters with the mandatory driver
        $connectionParameters = array(
            'driver' => $databaseNode->getDriver()->getNodeValue()->__toString()
        );

        // initialize the path/memory to the database when we use sqlite for example
        if ($pathNode = $databaseNode->getPath()) {
            $connectionParameters['path'] = $this->getApplication()->getWebappPath() . DIRECTORY_SEPARATOR . $pathNode->getNodeValue()->__toString();
        } elseif ($memoryNode = $databaseNode->getMemory()) {
            $connectionParameters['memory'] = Boolean::valueOf(new String($memoryNode->getNodeValue()->__toString()))->booleanValue();
        } else {
            // do nothing here, because there is NO option
        }

        // add username, if specified
        if ($userNode = $databaseNode->getUser()) {
            $connectionParameters['user'] = $userNode->getNodeValue()->__toString();
        }

        // add password, if specified
        if ($passwordNode = $databaseNode->getPassword()) {
            $connectionParameters['password'] = $passwordNode->getNodeValue()->__toString();
        }

        // add database name if using another PDO driver than sqlite
        if ($databaseNameNode = $databaseNode->getDatabaseName()) {
            $connectionParameters['dbname'] = $databaseNameNode->getNodeValue()->__toString();
        }

        // add database host if using another PDO driver than sqlite
        if ($databaseHostNode = $databaseNode->getDatabaseHost()) {
            $connectionParameters['host'] = $databaseHostNode->getNodeValue()->__toString();
        }

        // add database port if using another PDO driver than sqlite
        if ($databasePortNode = $databaseNode->getDatabasePort()) {
            $connectionParameters['port'] = $databasePortNode->getNodeValue()->__toString();
        }

        // add charset, if specified
        if ($charsetNode = $databaseNode->getCharset()) {
            $connectionParameters['charset'] = $charsetNode->getNodeValue()->__toString();
        }

        // add driver options, if specified
        if ($unixSocketNode = $databaseNode->getUnixSocket()) {
            $connectionParameters['unix_socket'] = $unixSocketNode->getNodeValue()->__toString();
        }

        // add server version, if specified
        if ($serverVersionNode = $databaseNode->getServerVersion()) {
            $connectionParameters['server_version'] = $serverVersionNode->getNodeValue()->__toString();
        }

        // set platform, if specified
        if ($platformNode = $databaseNode->getPlatform()) {
            $platform = $platformNode->getNodeValue()->__toString();
            $connectionParameters['platform'] = new $platform();
        }

        // add driver options, if specified
        if ($driverOptionsNode = $databaseNode->getDriverOptions()) {
            // explode the raw options separated with a semicolon
            $rawOptions = explode(';', $driverOptionsNode->getNodeValue()->__toString());

            // prepare the array with the driver options key/value pair (separated with a =)
            $options = array();
            foreach ($rawOptions as $rawOption) {
                list ($key, $value) = explode('=', $rawOption);
                $options[$key] = $value;
            }

            // set the driver options
            $connectionParameters['driverOptions'] = $options;
        }

        // returns the connection parameters
        return $connectionParameters;
    }
}
