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
     * Path to the container's deployment configuration.
     * @var string
     */
    const CONTAINER_DEPLOYMENT = '/container/deployment';
    
    /**
     * The container's configuration
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;
    
    /**
     * Set's the configuration with the container information to be started in the thread.
     * 
     * @param \TechDivision\ApplicationServer\Configuration $configuration The container's configuration
     */
    public function __construct($initialContext, $configuration) {
        $this->initialContext = $initialContext;
        $this->configuration = $configuration;
    }

    public function getInitialContext()
    {
        return $this->initialContext;
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
        
        // load the container type and deploy the applications
        $containerType = $configuration->getType();

        // deploy the applications and return them in an array
        $applications = $this->getDeployment()->deploy()->getApplications();

        // create and start the container instance
        $containerInstance = $this->newInstance($containerType, array($this->getInitialContext(), $configuration, $applications));
        $containerInstance->run();
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
        return $this->getInitialContext()->newInstance($className, $args);
    }
    
    /**
     * The configuration found in the cfg/appserver.xml file.
     * 
     * @return \TechDivision\ApplicationServer\Configuration The configuration instance
     */
    public function getConfiguration() {
        return $this->configuration;
    }

    /**
     * @return object
     */
    public function getDeployment() {
        $deploymentType = $this->getConfiguration()->getChild(self::CONTAINER_DEPLOYMENT)->getType();
        return $this->newInstance($deploymentType, array($this->getInitialContext(), $this));
    }
}