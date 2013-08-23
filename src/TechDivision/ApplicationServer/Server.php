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
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Server {
    
    /**
     * XPath expression for the container configurations.
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';
    
    /**
     * XPath expression for the container's base directory configuration.
     * @var string
     */
    const XPATH_BASE_DIRECTORY = '/appserver/baseDirectory';

    /**
     * XPath expression for the initial context configuration.
     * @var string
     */
    const XPATH_INITIAL_CONTEXT = '/appserver/initialContext';

    /**
     * Initialize the array for the running threads.
     * @var array
     */
    protected $threads = array();
    
    /**
     * The container configuration.
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;
    
    /**
     * The server's initial context instance.
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Initializes the the server with the base directory.
     * 
     * @param string $baseDirectory The application servers base directory
     * @param string $configurationFile The path to the configuration file relativ to the base directory
     * @return void
     */
    public function __construct($configuration) {
        
        // initialize the configuration and the base directory
        $this->configuration = $configuration;
        
        // initialize the initial context instance
        $reflectionClass = new \ReflectionClass($this->getInitialContextConfiguration()->getType());
        $this->initialContext = $reflectionClass->newInstanceArgs(array($this->getInitialContextConfiguration()));
    }
    
    /**
     * Returns the running container threads.
     * 
     * @return array<\TechDivision\ApplicationServer\ContainerThread> Array with the running container threads
     */
    public function getThreads() {
        return $this->threads;
    }
    
    /**
     * Returns the container configuration.
     * 
     * @return \TechDivision\ApplicationServer\Configuration The container configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    /**
     * Returns the initial context instance.
     * 
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext() {
        return $this->initialContext;
    }

    /**
     * Returns the server's base directory.
     *
     * @return string The server's base directory
     * @see \TechDivision\ApplicationServer\Server::getBaseDirectoryConfiguration()
     */
    public function getBaseDirectory() {
        return $this->getBaseDirectoryConfiguration()->getValue();
    }

    /**
     * Returns the server's base directory configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The server's base directory configuration
     */
    public function getBaseDirectoryConfiguration() {
        return $this->getConfiguration()->getChild(self::XPATH_BASE_DIRECTORY);
    }
    
    /**
     * Return's the initial context configuration.
     * 
     * @return \TechDivision\ApplicationServer\Configuration The initial context configuration
     */
    public function getInitialContextConfiguration() {
        return $this->getConfiguration()->getChild(self::XPATH_INITIAL_CONTEXT);
    }
    
    /**
     * Return's the container configuration.
     * 
     * @return array<\TechDivision\ApplicationServer\Configuration> The container configuration
     */
    public function getContainerConfiguration() {
        return $this->getConfiguration()->getChilds(self::XPATH_CONTAINERS);
    }
    
    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {

        // start each container in his own thread
        foreach ($this->getContainerConfiguration() as $i => $containerConfiguration) {
            
            // pass the base directory through to the container configuration
            $containerConfiguration->addChild($this->getBaseDirectoryConfiguration());
            
            // initialize the container configuration with the base directory and pass it to the thread
            $this->threads[$i] = $this->newInstance('TechDivision\ApplicationServer\ContainerThread', array($this->getInitialContext(), $containerConfiguration));
            $this->threads[$i]->start();
        }
    }
    
    /**
     * Returns a new instance of the passed class name.
     * 
     * @param string $className The fully qualified class name to return the instance for
     * @param array $args Arguments to pass to the constructor of the instance
     * @return object The instance itself
     * @todo Has to be refactored to avoid registering autoloader on every call
     */
    public function newInstance($className, array $args = array()) {
        return $this->getInitialContext()->newInstance($className, $args);
    }
}