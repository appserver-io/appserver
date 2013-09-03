<?php

/**
 * TechDivision\ApplicationServer\AbstractReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\ReceiverInterface;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 * @author      Johann Zelger <jz@techdivision.com>
 */
abstract class AbstractReceiver implements ReceiverInterface {
    
    /**
     * Path to the receiver's initialization parameters.
     * @var string
     */
    const XPATH_CONFIGURATION_PARAMETERS = '/receiver/params/param';
    
    /**
     * The container instance.
     * @var \TechDivision\ApplicationServer\Interfaces\ContainerInterface
     */
    protected $container;
    
    /**
     * The worker type to use.
     * @var string
     */
    protected $workerType;

    /**
     * The thread type to use.
     * @var string
     */
    protected $threadType;

    /**
     * Sets the reference to the container instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($initialContext, $container) {

        // initialize the initial context
        $this->initialContext = $initialContext;
        
        // set the container instance
        $this->container = $container;

        // enable garbage collector
        $this->gcEnable();

        // set the receiver configuration
        $this->setConfiguration($this->getContainer()->getReceiverConfiguration());

        // load the worker type
        $this->setWorkerType($this->getContainer()->getWorkerType());

        // load the thread type
        $this->setThreadType($this->getContainer()->getThreadType());
    }

    /**
     * Returns the resource class used to create a new socket.
     *
     * @return string The resource class name
     */
    protected abstract function getResourceClass();

    /**
     *
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start()
    {
        try {
            
            /** @var \TechDivision\Socket\Client $socket */
            $socket = $this->newInstance($this->getResourceClass());
            
            // prepare the main socket and listen
            $socket->setAddress($this->getAddress())
                ->setPort($this->getPort())
                ->start();
            
            try {
                // check if resource been initiated
                if ($socket->getResource()) {
                    // init worker number
                    $worker = 0;
                    // init workers array holder
                    $workers = array();
                    // open threads where accept connections
                    while ($worker ++ < $this->getWorkerNumber()) {
                        // init thread
                        $workers[$worker] = $this->newWorker($socket->getResource());
                        // start thread async
                        $workers[$worker]->start();
                    }
                }
            } catch (\Exception $e) {
                error_log($e->__toString());
            }
        } catch (\Exception $ge) {
            
            error_log($ge->__toString());
            
            if (is_resource($socket->getResource())) {
                $socket->close();
            }
        }
    }

    /**
     * Returns the refrence to the container instance.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerInterface The container instance
     */
    public function getContainer() {
        return $this->container;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getWorkerNumber()
     */
    public function getWorkerNumber() {
        foreach ($this->getConfiguration()->getChilds(self::XPATH_CONFIGURATION_PARAMETERS) as $param) {
            if ($param->getData('name') == 'workerNumber') {
                return $param->getValue();
            }
        }
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getAddress()
     */
    public function getAddress() {
        foreach ($this->getConfiguration()->getChilds(self::XPATH_CONFIGURATION_PARAMETERS) as $param) {
            if ($param->getData('name') == 'address') {
                return $param->getValue();
            }
        }
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getPort()
     */
    public function getPort() {
        foreach ($this->getConfiguration()->getChilds(self::XPATH_CONFIGURATION_PARAMETERS) as $param) {
            if ($param->getData('name') == 'port') {
                return $param->getValue();
            }
        }   
    }

    /**
     * Set's the worker's class name to use.
     * 
     * @param string $workerType The worker's class name to use
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function setWorkerType($workerType) {
        $this->workerType = $workerType;
        return $this;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getWorkerType()
     */
    public function getWorkerType() {
        return $this->workerType;
    }

    /**
     * Set's the thread's class name to use.
     *
     * @param string $threadType The thread's class name to use
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function setThreadType($threadType) {
        $this->threadType = $threadType;
        return $this;
    }

    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ReceiverInterface::getThreadType()
     */
    public function getThreadType() {
        return $this->threadType;
    }
    
    /**
     * Sets the passed container configuration.
     * 
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration $configuration The configuration for the container
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     * @todo Actually it's not possible to add interfaces as type hints for method parameters, this results in an infinite loop 
     */
    public function setConfiguration($configuration) {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * Returns the actual container configuration.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration The actual container configuration
     */
    public function getConfiguration() {
        return $this->configuration;
    }
    
    /**
     * Forces collection of any existing garbage cycles.
     * 
     * @return integer The number of collected cycles
     * @link http://php.net/manual/en/features.gc.collecting-cycles.php
     */
    public function gc() {
        return gc_collect_cycles();
    }
    
    /**
     * Returns TRUE if the PHP internal garbage collection is enabled.
     * 
     * @return boolean TRUE if the PHP internal garbage collection is enabled
     * @link http://php.net/manual/en/function.gc-enabled.php
     */
    public function gcEnabled() {
        return gc_enabled();
    }
    
    /**
     * Enables PHP internal garbage collection.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     * @link http://php.net/manual/en/function.gc-enable.php
     */
    public function gcEnable() {
        gc_enable();
        return $this;
    }
    
    /**
     * Disables PHP internal garbage collection.
     * 
     * @return \TechDivision\PersistenceContainer\Container The container instance
     * @link http://php.net/manual/en/function.gc-disable.php
     */
    public function gcDisable() {
        gc_disable();
        return $this;
    }

    /**
     * Returns a thread
     *
     * @return \Thread The request acceptor thread
     */
    public function newWorker($socketResource) {
        $params = array($this->initialContext, $this->getContainer(), $socketResource, $this->getThreadType());
        return $this->newInstance($this->getWorkerType(), $params);
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
        return $this->initialContext->newInstance($className, $args);
    }
}