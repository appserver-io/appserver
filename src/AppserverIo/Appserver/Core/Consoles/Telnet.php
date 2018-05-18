<?php

/**
 * AppserverIo\Appserver\Core\Consoles\Telnet
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

namespace AppserverIo\Appserver\Core\Consoles;

use AppserverIo\Appserver\Core\Commands\CommandFactory;
use AppserverIo\Appserver\Core\Commands\Helper\Arguments;
use AppserverIo\Psr\Cli\ConsoleInterface;
use AppserverIo\Psr\Cli\Configuration\ConsoleConfigurationInterface;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * A Telnet based management console implementation using a React PHP socket server.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Telnet extends \Thread implements ConsoleInterface
{

    /**
     * The configuration parameter name for the port to listen to.
     *
     * @var string
     */
    const PARAM_PORT = 'port';

    /**
     * The configuration parameter name for the IP address to listen to.
     *
     * @var string
     */
    const PARAM_ADDRESS = 'address';

    /**
     * appserver.io written in ASCII art.
     *
     * @var string
     */
    protected static $logo = '                                                    _
  ____ _____  ____  ________  ______   _____  _____(_)___
 / __ `/ __ \/ __ \/ ___/ _ \/ ___/ | / / _ \/ ___/ / __ \
/ /_/ / /_/ / /_/ (__  )  __/ /   | |/ /  __/ /  / / /_/ /
\__,_/ .___/ .___/____/\___/_/    |___/\___/_(_)/_/\____/
    /_/   /_/

';

    /**
     * Initialize and start the management console.
     *
     * @param \AppserverIo\Psr\ApplicationServer\ApplicationServerInterface    $applicationServer The reference to the server
     * @param \AppserverIo\Psr\Cli\Configuration\ConsoleConfigurationInterface $consoleNode       The console configuration
     *
     * @return void
     */
    public function __construct(ApplicationServerInterface $applicationServer, ConsoleConfigurationInterface $consoleNode)
    {
        $this->applicationServer = $applicationServer;
        $this->consoleNode = $consoleNode;
        $this->start(PTHREADS_INHERIT_ALL);
    }

    /**
     * Return's the console name.
     *
     * @return string The console name
     */
    public function getName()
    {
        return $this->consoleNode->getName();
    }

    /**
     * Returns the port to listen to.
     *
     * @return integer The port to listen to
     */
    protected function getPort()
    {
        return $this->consoleNode->getParam(Telnet::PARAM_PORT);
    }

    /**
     * Returns the IP address to listen to.
     *
     * @return integer The IP address to listen to
     */
    protected function getAddress()
    {
        return $this->consoleNode->getParam(Telnet::PARAM_ADDRESS);
    }

    /**
     * Shutdown handler that checks for fatal/user errors.
     *
     * @return void
     */
    public function shutdown()
    {
        // check if there was a fatal error caused shutdown
        if ($lastError = error_get_last()) {
            // initialize type + message
            $type = 0;
            $message = '';
            // extract the last error values
            extract($lastError);
            // query whether we've a fatal/user error
            if ($type === E_ERROR || $type === E_USER_ERROR) {
                echo $message . PHP_EOL;
            }
        }
    }

    /**
     * Stop the console and closes all connections.
     *
     * @return void
     */
    public function stop()
    {
        $this->kill();
    }

    /**
     * The thread's run() method that runs asynchronously.
     *
     * @return void
     * @link http://www.php.net/manual/en/thread.run.php
     */
    public function run()
    {

        // register a shutdown handler for controlled shutdown
        register_shutdown_function(array(&$this, 'shutdown'));

        // we need the autloader again
        require SERVER_AUTOLOADER;

        // create a reference to the application server instance
        $applicationServer = $this->applicationServer;

        // initialize the event loop and the socket server
        $loop = \React\EventLoop\Factory::create();
        $socket = new \React\Socket\Server($loop);

        // wait for connections
        $socket->on('connection', function ($conn) use ($applicationServer) {
            // wait for user input => usually a command
            $conn->on('data', function ($data) use ($conn, $applicationServer) {
                try {
                    // extract command name and parameters
                    $params = Arguments::split($data);
                    $commandName = array_shift($params);

                    try {
                        // initialize and execute the command
                        $command = CommandFactory::factory($commandName, array($conn, $applicationServer));
                        $command->execute($params);

                    } catch (\ReflectionException $re) {
                        $conn->write(sprintf("Unknown command %sERROR\n", $commandName));
                    }

                } catch (\Exception $e) {
                    $conn->write("{$e->__toString()}ERROR\n");
                }
            });
        });

        // listen to the management socket
        $socket->listen($this->getPort(), $this->getAddress());

        // start the event loop and the socket server, but disable warnings as some React warnings cannot (or won't) be dealt with.
        // Specifically the warning if a client disconnects unexpectedly or does not even connect to begin with ("Interrupted system call") is unevitable
        // @see https://github.com/reactphp/react/pull/297
        // @see https://github.com/reactphp/react/issues/296
        // @see http://php.net/manual/de/function.stream-select.php
        $currentReportingLevel = error_reporting();
        error_reporting(E_ALL ^ E_WARNING);
        $loop->run();
        error_reporting($currentReportingLevel);
    }
}
