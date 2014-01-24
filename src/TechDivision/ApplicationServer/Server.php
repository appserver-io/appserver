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

use TechDivision\ApplicationServer\Extractors\PharExtractor;
use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\Socket\Client;
use TechDivision\ApplicationServer\SplClassLoader;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\ContainerThread;
use TechDivision\ApplicationServer\Api\Node\NodeInterface;
use TechDivision\ApplicationServer\Api\Node\AppserverNode;
use \Psr\Log\LoggerInterface;

/**
 * This is the main server class that starts the application server
 * and creates a separate thread for each container found in the
 * configuration file.
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
     * @var array
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
     * The server's webapp extractor
     *
     * @var \TechDivision\ApplicationServer\Interfaces\ExtractorInterface
     */
    protected $extractor;

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
        
        // initialize the server
        $this->init();
    }

    /**
     * Initialize's the server instance.
     *
     * @return void
     */
    protected function init()
    {
        // init initial context
        $this->initInitialContext();
        // init the file system
        $this->initFileSystem();
        // init main system logger
        $this->initSystemLogger();
        // init extractor
        $this->initExtractor();
        // init containers
        $this->initContainers();
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
        // set the initial context and flush it initially
        $this->setInitialContext($initialContext);
    }

    /**
     * Prepares filesystem to be sure that everything is on place as expected
     *
     * @return void
     */
    public function initFileSystem()
    {
        
        // init API service to use
        $service = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        
        // check if the log directory already exists, if not, create it
        foreach ($service->getDirectories() as $directory) {
            
            // prepare the path to the directory to be created
            $toBeCreated = $service->realpath($directory);
            // prepare the directory name and check if the directory already exists
            if (is_dir($toBeCreated) === false) {
                // if not create it
                mkdir($toBeCreated, 0755, true);
            }
        }
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
        $this->getInitialContext()->setSystemLogger($systemLogger);
    }

    /**
     * Initializes the extractor
     *
     * @return void
     */
    protected function initExtractor()
    {
        // @TODO: read extractor type from configuration
        $this->setExtractor(new PharExtractor($this->getInitialContext()));
        // extract all webapps
        $this->getExtractor()->deployWebapps();
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
                $containerNode
            );
            
            // create and append the thread instance to the internal array
            $this->threads[] = $this->newInstance($containerNode->getThreadType(), $params);
        }
    }

    /**
     * Returns the running container threads.
     *
     * @return array Array with the running container threads
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
     * Return's the system logger instance.
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getSystemLogger()
    {
        return $this->getInitialContext()->getSystemLogger();
    }

    /**
     * Set's the extractor
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ExtractorInterface $extractor
     *            The initial context instance
     *            
     * @return void
     */
    public function setExtractor(ExtractorInterface $extractor)
    {
        return $this->extractor = $extractor;
    }

    /**
     * Returns the extractor
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ExtractorInterface The extractor instance
     */
    public function getExtractor()
    {
        return $this->extractor;
    }

    /**
     * Start the container threads.
     *
     * @return void
     */
    public function start()
    {
        
        // log that the server will be started now
        $this->getSystemLogger()->info(sprintf('Server successfully started in basedirectory %s ', $this->getSystemConfiguration()
            ->getBaseDirectory()
            ->getNodeValue()
            ->__toString()));
        
        // start the container threads
        foreach ($this->getThreads() as $thread) {
            
            // start the thread
            $thread->start();

            // synchronize container threads to avoid registring apps several times
            $thread->synchronized(function ($self) {
                $self->wait();
            }, $thread);
        }
        
        // wait for the container thread to finish
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