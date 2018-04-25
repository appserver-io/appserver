<?php

/**
 * \AppserverIo\Appserver\Console\ExecutionContext
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

use Psr\Log\LogLevel;
use React\Socket\ConnectionInterface;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\Utilities\ErrorUtil;
use AppserverIo\Appserver\Core\Utilities\LoggerUtils;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * The console manager handles the console applications registered for the application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ExecutionContext extends \Thread
{

    /**
     * Inject's the input instance.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input The input instance
     *
     * @return void
     */
    public function injectInput(InputInterface $input)
    {
        $this->input = $input;
    }

    /**
     * Inject's the output instance.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output The output instance
     *
     * @return void
     */
    public function injectOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Inject's the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * The thread's main method.
     *
     * @return void
     */
    public function run()
    {

        try {
            // register the default autoloader
            require SERVER_AUTOLOADER;

            // register shutdown handler
            set_error_handler(array(&$this, 'errorHandler'));
            register_shutdown_function(array(&$this, 'shutdown'));

            // try to load the application
            $application = $this->application;

            // register the applications class loaders
            $application->registerClassLoaders();

            // load input/output instances
            $input = $this->input;
            $output = $this->output;

            // add the application instance to the environment
            Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

            // create a simulated request/session ID whereas session equals request ID (as long as session has NOT been started)
            Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $requestId = SessionUtils::generateRandomString());
            Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $requestId);

            // load and initialize the Symfony console application
            /** @var \Symfony\Component\Console\Application $cli */
            $cli = $application->search('Console');
            $cli->setAutoExit(false);
            $cli->setCatchExceptions(true);

            // run the Symfony console application
            $cli->run($input, $output);

            // update the output instance
            $this->output = $output;

        } catch (\Exception $e) {
            // log the exception
            LoggerUtils::log(LogLevel::ERROR, $e->__toString());
        }
    }

    /**
     * Write's the output to the passed connection.
     *
     * @param \React\Socket\ConnectionInterface $connection The connection to write to
     *
     * @return void
     */
    public function write(ConnectionInterface $connection)
    {
        $connection->write("{$this->output->fetch()}\$\n");
    }

    /**
     * PHP error handler implemenation that replaces the defaulf PHP error handling.
     *
     * As this method will NOT handle Fatal Errors with code E_ERROR or E_USER, so
     * these have to be processed by the shutdown handler itself.
     *
     * @param integer $errno   The intern PHP error number
     * @param string  $errstr  The error message itself
     * @param string  $errfile The file where the error occurs
     * @param integer $errline The line where the error occurs
     *
     * @return boolean Always return TRUE, because we want to disable default PHP error handling
     * @link http://docs.php.net/manual/en/function.set-error-handler.php
     */
    public function errorHandler($errno, $errstr, $errfile, $errline)
    {

        // query whether or not we've to handle the passed error
        if ($errno > error_reporting()) {
            return true;
        }

        // add the passed error information to the array with the errors
        $error = ErrorUtil::singleton()->fromArray(array($errno, $errstr, $errfile, $errline));
        // log the error messge and return TRUE, to prevent execution of additinoal error handlers
        LoggerUtils::log(LogLevel::ERROR, ErrorUtil::singleton()->prepareMessage($error));
        return true;
    }

    /**
     * Does shutdown logic for request handler if something went wrong and
     * produces a fatal error for example.
     *
     * @return void
     */
    public function shutdown()
    {

        // query whether or not, class has been shutdown by an unhandled error
        if ($lastError = error_get_last()) {
            // add the passed error information to the array with the errors
            $error = ErrorUtil::singleton()->fromArray($lastError);
            // log the error messge and return TRUE, to prevent execution of additinoal error handlers
            LoggerUtils::log(LogLevel::ERROR, ErrorUtil::singleton()->prepareMessage($error));
        }
    }
}
