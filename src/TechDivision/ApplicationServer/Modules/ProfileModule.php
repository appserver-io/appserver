<?php

/**
 * TechDivision\ApplicationServer\Modules\ProfileModule
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Modules
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Modules;

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Logger\ThreadSafeLoggerInterface;
use TechDivision\Connection\ConnectionRequestInterface;
use TechDivision\Connection\ConnectionResponseInterface;
use TechDivision\Server\Dictionaries\ModuleHooks;
use TechDivision\Server\Interfaces\ModuleInterface;
use TechDivision\Server\Interfaces\RequestContextInterface;
use TechDivision\Server\Interfaces\ServerContextInterface;

/**
 * Profile module that allows to send profile log messages
 * via PSR-3 compatible logger implementation.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Modules
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ProfileModule implements ModuleInterface
{

    /**
     * Defines the module name
     *
     * @var string
     */
    const MODULE_NAME = 'profile';

    /**
     * The server context instance.
     *
     * @var \TechDivision\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Holds the profile logger instance, if registered.
     *
     * @var \AppserverIo\Logger\ThreadSafeLoggerInterface
     */
    protected $profileLogger;

    /**
     * Initiates the module
     *
     * @param \TechDivision\Server\Interfaces\ServerContextInterface $serverContext The server's context instance
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function init(ServerContextInterface $serverContext)
    {

        // initialize the server context
        $this->serverContext = $serverContext;

        // initialize the profile logger
        if ($profileLogger = $serverContext->getLogger(LoggerUtils::PROFILE)) {
            $this->profileLogger = $profileLogger;
        }
    }

    /**
     * Implements module logic for given hook.
     *
     * @param \TechDivision\Connection\ConnectionRequestInterface     $request        A request object
     * @param \TechDivision\Connection\ConnectionResponseInterface    $response       A response object
     * @param \TechDivision\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                     $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function process(ConnectionRequestInterface $request, ConnectionResponseInterface $response, RequestContextInterface $requestContext, $hook)
    {

        // profile this request if we've a logger instance
        if (ModuleHooks::RESPONSE_POST === $hook && $this->profileLogger) {
            $this->profileLogger->debug($request->getUri());
        }
    }

    /**
     * Return's an array of module names which should be executed first
     *
     * @return array The array of module names
     */
    public function getDependencies()
    {
        return array();
    }

    /**
     * Returns the module name
     *
     * @return string The module name
     */
    public function getModuleName()
    {
        return self::MODULE_NAME;
    }

    /**
     * Prepares the module for upcoming request in specific context
     *
     * @return bool
     * @throws \TechDivision\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
        if ($this->profileLogger) { // set the thread-worker context in the profile logger
            $this->profileLogger->appendThreadContext('thread-worker');
        }
    }
}
