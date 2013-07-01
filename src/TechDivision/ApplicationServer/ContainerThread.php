<?php

/**
 * TechDivision\ApplicationServer\ContainerThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\SplClassLoader;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ContainerThread extends \Thread {
    
    /**
     * The fully qualified class name of the container to start in the thread.
     * @var string
     */
    protected $configuration;
    
    /**
     * Set's the fully qualified class name of the container to start 
     * in the thread.
     * 
     * @param string $containerType The container's fully qualified class name
     */
    public function __construct($configuration) {
        $this->configuration = $configuration;
    }
    
    /**
     * @see \Thread::run()
     */
    public function run() {
        
        // register class loader again, because we are in a thread
        $classLoader = new SplClassLoader();
        $classLoader->register();
        
        // load the container configuration
        $configuration = $this->getConfiguration();
        
        // load the container type to initialize
        $containerType = $configuration->getType();
        
        // create and start the container instance
        $containerInstance = $this->newInstance($containerType, array($configuration));
        $containerInstance->start();
    }
    
    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     * 
     * @param string $className The class name to create the instance of
     * @param array $args The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array()) { 
        return InitialContext::get()->newInstance($className, $args);
    }
    
    /**
     * The configuration found in the cfg/appserver.xml file.
     * 
     * @return \TechDivision\ApplicationServer\Configuration The configuration instance
     */
    public function getConfiguration() {
        return $this->configuration;
    }
}