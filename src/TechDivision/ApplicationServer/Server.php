<?php

/**
 * TechDivision\ApplicationServer\Server
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\Socket\Client;
use TechDivision\ApplicationServer\SplClassLoader;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\ContainerThread;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class Server
{

    /**
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * XPath expression for the container's base directory configuration.
     *
     * @var string
     */
    const XPATH_BASE_DIRECTORY = '/appserver/baseDirectory';

    /**
     * XPath expression for the initial context configuration.
     *
     * @var string
     */
    const XPATH_INITIAL_CONTEXT = '/appserver/initialContext';

    /**
     * XPath expression for the system logger configuration.
     *
     * @var string
     */
    const XPATH_SYSTEM_LOGGER = '/appserver/systemLogger';

    /**
     * Initialize the array for the running threads.
     *
     * @var array
     */
    protected $threads = array();

    /**
     * The container configuration.
     *
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * The server's initial context instance.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The server's logger instance.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $systemLogger;

    /**
     * The mutex toprevent PHAR deployment errors
     *
     * @var \Mutex
     */
    protected $mutex;

    /**
     * Initializes the the server with the base directory.
     *
     * @param string $baseDirectory
     *            The application servers base directory
     * @param string $configurationFile
     *            The path to the configuration file relativ to the base directory
     * @return void
     */
    public function __construct($configuration)
    {

        // initialize the configuration and the base directory
        $this->configuration = $configuration;

        // initialize the mutex to prevent PHAR deployment errors
        $this->mutex = \Mutex::create(false);

        // initialize the server
        $this->init();
    }

    /**
     * Destroys the mutexes.
     *
     * @return void
     */
    public function __destruct()
    {
        \Mutex::unlock($this->mutex);
        \Mutex::destroy($this->mutex);
    }

    /**
     * Initialize's the server instance.
     *
     * @return void
     */
    protected function init()
    {
        $this->initInitialContext();
        $this->initSystemLogger();
        $this->initContainers();
    }
    
    /**
     * organize params.
     *
     * @return array
     */
    private function _organizeParams($configuration) {
        $params = array();
        if ($configuration->getParams()) {
            foreach ($configuration->getParams()->getChilds('/params/param') as $param) {
                $value = $param->getValue();
                settype($value, $param->getType());
                $params[$param->getName()] = $value;
            }
        }
        return $params;
    }
    /**
     * Initialize the system logger.
     *
     * @return void
     */
    protected function initSystemLogger()
    {

        // initialize the logger instance itself
        $systemLoggerConfiguration = $this->getSystemLoggerConfiguration();
        $this->systemLogger = $this->newInstance($systemLoggerConfiguration->getType(), array(
            $systemLoggerConfiguration->getChannelName()
        ));

        // initialize the processors
        foreach ($systemLoggerConfiguration->getChilds('/systemLogger/processors/processor') as $processorConfiguration) {
            $processor = $this->newInstance($processorConfiguration->getType(), $this->_organizeParams($processorConfiguration));
            $this->systemLogger->pushProcessor($processor);
        }

        // initialize the handlers
        foreach ($systemLoggerConfiguration->getChilds('/systemLogger/handlers/handler') as $handlerConfiguration) {
            $handler = $this->newInstance($handlerConfiguration->getType(), $this->_organizeParams($handlerConfiguration));

            // initialize the handlers formatter
            $formatterConfiguration = $handlerConfiguration->getFormatter();
            // set the handlers formatter and add the handler to the logger
            $handler->setFormatter($this->newInstance($formatterConfiguration->getType(), $this->_organizeParams($formatterConfiguration)));
            $this->systemLogger->pushHandler($handler);
        }
    }

    /**
     * Initialize the initial context instance.
     *
     * @return void
     */
    protected function initInitialContext()
    {
        $reflectionClass = new \ReflectionClass($this->getInitialContextConfiguration()->getType());
        $this->initialContext = $reflectionClass->newInstanceArgs(array(
            $this->getInitialContextConfiguration()
        ));

        // add the system configuration to the initial context
        $this->initialContext->setSystemConfiguration($this->configuration);
    }

    /**
     * Initialize the container threads.
     *
     * @return void
     */
    protected function initContainers()
    {

        // start each container in his own thread
        foreach ($this->getContainerConfiguration() as $containerConfiguration) {

            // pass the base directory through to the container configuration
            $containerConfiguration->addChild($this->getBaseDirectoryConfiguration());

            // initialize the container configuration with the base directory and pass it to the thread
            $params = array(
                $this->getInitialContext(),
                $containerConfiguration,
                $this->mutex
            );

            $threadType = $containerConfiguration->getThreadType();
            $this->threads[] = $this->newInstance($threadType, $params);
        }
    }

    /**
     * Returns the running container threads.
     *
     * @return array<\TechDivision\ApplicationServer\ContainerThread> Array with the running container threads
     */
    public function getThreads()
    {
        return $this->threads;
    }

    /**
     * Returns the container configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The container configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Returns the initial context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the server's base directory.
     *
     * @return string The server's base directory
     * @see \TechDivision\ApplicationServer\Server::getBaseDirectoryConfiguration()
     */
    public function getBaseDirectory()
    {
        return $this->getBaseDirectoryConfiguration()->getValue();
    }

    /**
     * Returns the server's base directory configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The server's base directory configuration
     */
    public function getBaseDirectoryConfiguration()
    {
        return $this->getConfiguration()->getChild(self::XPATH_BASE_DIRECTORY);
    }

    /**
     * Return's the initial context configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The initial context configuration
     */
    public function getInitialContextConfiguration()
    {
        return $this->getConfiguration()->getChild(self::XPATH_INITIAL_CONTEXT);
    }

    /**
     * Return's the system logger configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The system logger configuration
     */
    public function getSystemLoggerConfiguration()
    {
        return $this->getConfiguration()->getChild(self::XPATH_SYSTEM_LOGGER);
    }

    /**
     * Return's the container configuration.
     *
     * @return array<\TechDivision\ApplicationServer\Configuration> The container configuration
     */
    public function getContainerConfiguration()
    {
        return $this->getConfiguration()->getChilds(self::XPATH_CONTAINERS);
    }

    /**
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger()
    {
        return $this->systemLogger;
    }

    /**
     * Start the container threads.
     *
     * @return void
     */
    public function start()
    {
        foreach ($this->getThreads() as $thread) {
            $thread->start();
        }
        foreach ($this->getThreads() as $thread) {
            $thread->join();
        }
    }

    /**
     * Returns a new instance of the passed class name.
     *
     * @param string $className
     *            The fully qualified class name to return the instance for
     * @param array $args
     *            Arguments to pass to the constructor of the instance
     * @return object The instance itself
     * @todo Has to be refactored to avoid registering autoloader on every call
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}
