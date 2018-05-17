<?php

/**
 * AppserverIo\Appserver\Core\Commands\ConsoleCommand
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
use AppserverIo\Appserver\Console\ConsoleContextInterface;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * The command implementation for application based commands.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ConsoleCommand extends AbstractCommand
{

    /**
     * The unique command name.
     *
     * @var string
     */
    const COMMAND = 'console';

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
     * Executes the command.
     *
     * @param array $params The arguments passed to the command
     *
     * @return mixed|null The result of the command
     * @see \AppserverIo\Appserver\Core\Commands\CommandInterface::execute()
     */
    public function execute(array $params = array())
    {
        $this->doExcute($params);
    }

    /**
     * Execute the Doctrine CLI tool.
     *
     * @param array $command The Doctrine command to be executed
     *
     * @return string The commands output
     */
    protected function doExcute(array $command = array())
    {

        try {
            // prepare the application's lookup name with the first argument, which has to be the application name
            $lookupName = sprintf('php:global/combined-appserver/%s/ApplicationInterface', array_shift($command));

            // try to load the application
            /** \AppserverIo\Psr\Application\ApplicationInterface $application */
            $application = $this->getNamingDirectory()->search($lookupName);

            // try to load the application's console manager and execute the command
            /** @var \AppserverIo\Appserver\Console\ConsoleContextInterface $consoleManager */
            $consoleManager = $application->search(ConsoleContextInterface::IDENTIFIER);
            $consoleManager->execute($this->connection, $command);

        } catch (\Exception $e) {
            // log the exception
            $this->getSystemLogger()->error($e->__toString());
            // write the error message to the output
            $this->write("{$e->__toString()}ERROR\n");
        }
    }
}
