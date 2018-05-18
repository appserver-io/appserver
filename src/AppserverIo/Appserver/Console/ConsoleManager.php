<?php

/**
 * \AppserverIo\Appserver\Console\ConsoleManager
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
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Console;

use React\Socket\ConnectionInterface;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Psr\Cli\ConsoleContextInterface;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The console manager handles the console applications registered for the application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ConsoleManager extends AbstractManager implements ConsoleContextInterface
{

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {
    }

    /**
     * Executes the command with defined by the passed input.
     *
     * @param \React\Socket\ConnectionInterface $connection The socket connection
     * @param array                             $argv       The arguments from the console
     *
     * @return void
     */
    public function execute(ConnectionInterface $connection, array $argv)
    {

        // initialize input/output instances
        $input = new ArgvInput($argv);
        $output = new BufferedOutput();

        // initialize the execution context
        $executionContext = new ExecutionContext();
        $executionContext->injectInput($input);
        $executionContext->injectOutput($output);
        $executionContext->injectApplication($this->getApplication());

        // start the execution context
        $executionContext->start();
        $executionContext->join();

        // write the result back to the connection
        $executionContext->write($connection);
    }

    /**
     * Returns the identifier for the servlet manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::getIdentifier()
     */
    public function getIdentifier()
    {
        return ConsoleContextInterface::IDENTIFIER;
    }
}
