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
     * The mutex to prevent PHAR deployment errors.
     *
     * @var \Mutex
     */
    protected $mutex;

    /**
     * Initializes the the server with the base directory.
     *
     * @param \TechDivision\ApplicationServer\Configuration $systemConfiguration
     *            The system configuration instance
     * @return void
     */
    public function __construct($systemConfiguration)
    {

        // initialize the configuration and the base directory
        $this->systemConfiguration = $systemConfiguration;

        // initialize the mutex to prevent PHAR deployment errors
        $this->mutex = \Mutex::create(false);

        // initialize the server
        $this->init();
    }

    /**
     * Destroys the mutex to prevent PHAR deployment errors.
     *
     * @return void
     */
    public function __destruct()
    {
        \Mutex::unlock($this->getMutex());
        \Mutex::destroy($this->getMutex());
    }

    /**
     * Returns the mutex to prevent PHAR deployment errors.
     *
     * @return \Mutex The mutex
     */
    public function getMutex()
    {
        return $this->mutex;
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
            $params = array();
            if ($processorConfiguration->getParams() != null) {
                foreach ($processorConfiguration->getParams()->getChilds('/params/param') as $param) {
                    $value = $param->getValue();
                    settype($value, $param->getType());
                    $params[$param->getName()] = $value;
                }
            }
            $processor = $this->newInstance($processorConfiguration->getType(), $params);
            $this->systemLogger->pushProcessor($processor);
        }

        // initialize the handlers
        foreach ($systemLoggerConfiguration->getChilds('/systemLogger/handlers/handler') as $handlerConfiguration) {
            $params = array();
            if ($handlerConfiguration->getParams() != null) {
                foreach ($handlerConfiguration->getParams()->getChilds('/params/param') as $param) {
                    $value = $param->getValue();
                    settype($value, $param->getType());
                    $params[$param->getName()] = $value;
                }
            }
            $handler = $this->newInstance($handlerConfiguration->getType(), $params);

            // initialize the handlers formatter
            $params = array();
            $formatterConfiguration = $handlerConfiguration->getFormatter();
            if ($formatterConfiguration->getParams() != null) {
                foreach ($formatterConfiguration->getParams()->getChilds('/params/param') as $param) {
                    $value = $param->getValue();
                    settype($value, $param->getType());
                    $params[$param->getName()] = $value;
                }
            }

            // set the handlers formatter and add the handler to the logger
            $handler->setFormatter($this->newInstance($formatterConfiguration->getType(), $params));
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

        $node = $this->newInstance('TechDivision\ApplicationServer\Api\Node\AppserverNode');
        $node->initFromConfiguration($this->getSystemConfiguration());

        // add the system configuration to the initial context
        $this->initialContext->setSystemConfiguration($node);
    }

    /**
     * Initialize the container threads.
     *
     * @return void
     */
    protected function initContainers()
    {

        // load the container service
        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');

        // and initialize a container thread for each container
        foreach ($containerService->findAll() as $containerNode) {

            // initialize the container configuration with the base directory and pass it to the thread
            $params = array(
                $this->getInitialContext(),
                $this->getMutex(),
                $containerNode
            );

            // create and append the thread instance to the internal array
            $this->threads[] = $this->newInstance($containerNode->getThreadType(), $params);
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
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->systemConfiguration;
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
     * Return's the initial context configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The initial context configuration
     */
    public function getInitialContextConfiguration()
    {
        return $this->getSystemConfiguration()->getChild(self::XPATH_INITIAL_CONTEXT);
    }

    /**
     * Return's the system logger configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The system logger configuration
     */
    public function getSystemLoggerConfiguration()
    {
        return $this->getSystemConfiguration()->getChild(self::XPATH_SYSTEM_LOGGER);
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
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }
}