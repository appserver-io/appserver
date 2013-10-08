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
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\Api\Node\AppserverNode;
use \Psr\Log\LoggerInterface;

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
     * Initialize the array for the running threads.
     *
     * @var array<\Thread>
     */
    protected $threads = array();

    /**
     * The system configuration.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\NodeInterface
     */
    protected $systemConfiguration;

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
     * @var integer
     */
    protected $mutex;

    /**
     * Initializes the the server with the parsed configuration file.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration
     *            The parsed configuration file
     * @return void
     */
    public function __construct(Configuration $configuration)
    {

        // initialize the configuration and the base directory
        $systemConfiguration = new AppserverNode();
        $systemConfiguration->initFromConfiguration($configuration);
        $this->setSystemConfiguration($systemConfiguration);

        // initialize the mutex to prevent PHAR deployment errors
        $this->setMutex(\Mutex::create(false));

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
     * Set's the mutex to prevent PHAR deployment errors.
     *
     * @param
     *            integer The mutex
     * @return void
     */
    public function setMutex($mutex)
    {
        $this->mutex = $mutex;
    }

    /**
     * Returns the mutex to prevent PHAR deployment errors.
     *
     * @return integer The mutex
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
        $systemLoggerNode = $this->getSystemConfiguration()->getSystemLogger();
        $systemLogger = $this->newInstance($systemLoggerNode->getType(), array(
            $systemLoggerNode->getChannelName()
        ));

        // initialize the processors
        foreach ($systemLoggerNode->getProcessors() as $processorNode) {
            $processor = $this->newInstance($processorNode->getType(), $processorNode->getParamsAsArray());
            $systemLogger->pushProcessor($processor);
        }

        // initialize the handlers
        foreach ($systemLoggerNode->getHandlers() as $handlerNode) {
            $handler = $this->newInstance($handlerNode->getType(), $handlerNode->getParamsAsArray());
            $formatterNode = $handlerNode->getFormatter();
            $handler->setFormatter($this->newInstance($formatterNode->getType(), $formatterNode->getParamsAsArray()));
            $systemLogger->pushHandler($handler);
        }

        // set the initialized logger finally
        $this->setSystemLogger($systemLogger);
    }

    /**
     * Initialize the initial context instance.
     *
     * @return void
     */
    protected function initInitialContext()
    {
        $initialContextNode = $this->getSystemConfiguration()->getInitialContext();
        $reflectionClass = new \ReflectionClass($initialContextNode->getType());
        $initialContext = $reflectionClass->newInstanceArgs(array(
            $this->getSystemConfiguration()
        ));
        // set the initial context
        $this->setInitialContext($initialContext);
    }

    /**
     * Initialize the container threads.
     *
     * @return void
     */
    protected function initContainers()
    {

        // and initialize a container thread for each container
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {

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
     * Set's the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function setSystemConfiguration(NodeInterface $systemConfiguration)
    {
        return $this->systemConfiguration = $systemConfiguration;
    }

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\NodeInterface The system configuration
     */
    public function getSystemConfiguration()
    {
        return $this->systemConfiguration;
    }

    /**
     * Set's the initial context instance.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @return void
     */
    public function setInitialContext(InitialContext $initialContext)
    {
        return $this->initialContext = $initialContext;
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
     * Set's the system logger instance.
     *
     * @param \Psr\Log\LoggerInterface $systemLogger
     *            The system logger
     * @return void
     */
    public function setSystemLogger(LoggerInterface $systemLogger)
    {
        $this->systemLogger = $systemLogger;
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