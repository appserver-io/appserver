<?php

/**
 * AppserverIo\Appserver\Core\Commands\AbstractCommand
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

namespace AppserverIo\Appserver\Core\Commands;

use React\Socket\ConnectionInterface;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * An abstract command implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractCommand implements CommandInterface
{

    /**
     * The connection instance.
     *
     * @var \React\Socket\ConnectionInterface
     */
    protected $connection;

    /**
     * The application server instance.
     *
     * @var \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface
     */
    protected $applicationServer;

    /**
     * Initializes the command with the connection and the application server
     * instance to execute the command on.
     *
     * @param \React\Socket\ConnectionInterface                             $connection        The connection instance
     * @param \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface $applicationServer The application server instance
     */
    public function __construct(ConnectionInterface $connection, ApplicationServerInterface $applicationServer)
    {
        $this->connection = $connection;
        $this->applicationServer = $applicationServer;
    }

    /**
     * Returns the naming directory instance.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface $namingDirectory The default naming directory
     */
    protected function getNamingDirectory()
    {
        return $this->applicationServer->getNamingDirectory();
    }

    /**
     * Returns the deployment service instance.
     *
     * @return \AppserverIo\Appserver\Core\Api\DeploymentService The deployment service instance
     */
    protected function getDeploymentService()
    {
        return $this->applicationServer->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface The system logger instance
     */
    protected function getSystemLogger()
    {
        return $this->applicationServer->getSystemLogger();
    }

    /**
     * Write's the passed data to the actual connection.
     *
     * @param mixed $data The data to write
     *
     * @return void
     */
    protected function write($data)
    {
        $this->connection->write($data);
    }
}
