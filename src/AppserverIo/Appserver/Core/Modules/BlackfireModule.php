<?php

/**
 * \AppserverIo\Appserver\Core\Modules\BlackfireModule
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

namespace AppserverIo\Appserver\Core\Modules;

use Blackfire\Client;
use Blackfire\ClientConfiguration;
use Psr\Log\LoggerInterface;
use Blackfire\Exception\ExceptionInterface;
use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Logger\ThreadSafeLoggerInterface;
use AppserverIo\Psr\HttpMessage\RequestInterface;
use AppserverIo\Psr\HttpMessage\ResponseInterface;
use AppserverIo\Server\Dictionaries\ModuleHooks;
use AppserverIo\WebServer\Interfaces\HttpModuleInterface;
use AppserverIo\Server\Interfaces\RequestContextInterface;
use AppserverIo\Server\Interfaces\ServerContextInterface;

/**
 * Module implementation that profiles a HTTP request, started by the
 * PRE_REQUEST and finished with the POST_RESPONSE hook.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class BlackfireModule implements HttpModuleInterface
{

    /**
     * Defines the module name
     *
     * @var string
     */
    const MODULE_NAME = 'profile';

    /**
     * The name of the module variable to hold probe between requests.
     *
     * @var string
     */
    const BLACKFIRE_IO_PROBE = 'BLACKFIRE_IO_PROBE';

    /**
     * The server context instance.
     *
     * @var \AppserverIo\Server\Interfaces\ServerContextInterface
     */
    protected $serverContext;

    /**
     * Holds the system logger instance, if registered.
     *
     * @var \AppserverIo\Logger\ThreadSafeLoggerInterface
     */
    protected $systemLogger;

    /**
     * Initiates the module
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The server's context instance
     *
     * @return bool
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function init(ServerContextInterface $serverContext)
    {

        // initialize the server context
        $this->setServerContext($serverContext);

        // initialize the system logger
        if ($serverContext->hasLogger(LoggerUtils::SYSTEM)) {
            $this->setSystemLogger($this->getServerContext()->getLogger(LoggerUtils::SYSTEM));
        }
    }

    /**
     * Set's the server context.
     *
     * @param \AppserverIo\Server\Interfaces\ServerContextInterface $serverContext The server's context instance
     *
     * @return void
     */
    protected function setServerContext(ServerContextInterface $serverContext)
    {
        $this->serverContext = $serverContext;
    }

    /**
     * Return's the server context.
     *
     * @return \AppserverIo\Server\Interfaces\ServerContextInterface The server's context instance
     */
    protected function getServerContext()
    {
        return $this->serverContext;
    }

    /**
     * Set's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface|null The logger instance
     *
     * @return void
     */
    protected function setSystemLogger(LoggerInterface $systemLogger)
    {
        $this->systemLogger = $systemLogger;
    }

    /**
     * Return's the system logger instance, if available.
     *
     * @return \Psr\Log\LoggerInterface|null The logger instance
     */
    protected function getSystemLogger()
    {
        $this->systemLogger;
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
     * Implement's module logic for given hook
     *
     * @param \AppserverIo\Psr\HttpMessage\RequestInterface          $request        A request object
     * @param \AppserverIo\Psr\HttpMessage\ResponseInterface         $response       A response object
     * @param \AppserverIo\Server\Interfaces\RequestContextInterface $requestContext A requests context instance
     * @param int                                                    $hook           The current hook to process logic for
     *
     * @return bool
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function process(
        RequestInterface $request,
        ResponseInterface $response,
        RequestContextInterface $requestContext,
        $hook
    ) {

        // query whether or not, the blackfire.io extension is loaded
        if (!extension_loaded('blackfire')) {
            if ($systemLogger = $this->getSystemLogger()) {
                $systemLogger->info('Blackfire.io extension is not loaded!');
            }
            return;
        }

        // query whether or not, we are in pre-request processing hook
        if (ModuleHooks::REQUEST_PRE === $hook) {
            try {
                // read the blackfire.io configuration
                $config = ClientConfiguration::createFromFile('etc/appserver/.blackfire.ini');
                $config->getEndPoint();

                // create the blackfire.io client
                $blackfire = new Client($config);

                // store the probe in the module vars
                $requestContext->setModuleVar(BlackfireModule::BLACKFIRE_IO_PROBE, $blackfire->createProbe());

            } catch (\Exception $e) {
                if ($systemLogger = $this->getSystemLogger()) {
                    $systemLogger->error($e->__toString());
                }
            }

            // stop processing
            return;
        }

        // query whether or not, we are in post-response processing hook
        if (ModuleHooks::RESPONSE_POST === $hook) {
            try {
                // try to load the probe from the module vars and send it to blackfire.io
                if ($requestContext->hasModuleVar(BlackfireModule::BLACKFIRE_IO_PROBE)) {
                    $requestContext->getModuleVar(BlackfireModule::BLACKFIRE_IO_PROBE)->close();
                }

            } catch (ExceptionInterface $be) {
                if ($systemLogger = $this->getSystemLogger()) {
                    $systemLogger->error($be->__toString());
                }
            }
        }
    }

    /**
     * Prepares the module for upcoming request in specific context
     *
     * @return bool
     * @throws \AppserverIo\Server\Exceptions\ModuleException
     */
    public function prepare()
    {
    }
}
