<?php

/**
 * TechDivision\ApplicationServer\InitialContext
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class InitialContext {
    
    /**
     * The cache instance, e. g. Memcached
     * @var \Memcached
     */
    protected $cache;

    /**
     * Factory method implementation.
     * 
     * @return \TechDivision\ApplicationServer\InitialContext The singleton instance
     */
    public static function get() {
        return new InitialContext();
    }
    
    /**
     * Initializes the context with the connection to the persistence
     * backend, e. g. Memcached
     * 
     * @return void
     */
    public function __construct() {
        $this->cache = new \Memcached();
        $this->cache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->cache->addServers(array(array('127.0.0.1', 11211)));        
    }
    
    /**
     * Reinitializes the context with the connection to the persistence
     * backend, e. g. Memcached
     */
    public function __wakeup() {
        $this->cache = new \Memcached();
        $this->cache->setOption(\Memcached::OPT_LIBKETAMA_COMPATIBLE, true);
        $this->cache->addServers(array(array('127.0.0.1', 11211))); 
    }
    
    /**
     * Stores the passed key value pair in the initial context.
     * 
     * @param string $key The key to store the value under
     * @param mixed $value The value to add to the inital context
     * @return mixed The value added to the initial context
     */
    public function setAttribute($key, $value) {
        $this->cache->set($key, $value);
        return $value;
    }
    
    /**
     * Returns the value with the passed key from the initial context.
     * 
     * @param string $key The key of the value to return
     * @return mixed The value stored in the initial context
     */
    public function getAttribute($key) {
        return $this->cache->get($key);
    }
    
    /**
     * Returns a reflection class intance for the passed class name.
     * 
     * @param string $className The class name to return the reflection instance for
     * @return \ReflectionClass The reflection instance
     */
    public function newReflectionClass($className) {
        return new \ReflectionClass($className);
    }
    
    /**
     * Returns a new instance of the passed class name.
     * 
     * @param string $className The fully qualified class name to return the instance for
     * @param array $args Arguments to pass to the constructor of the instance
     * @return object The instance itself
     */
    public function newInstance($className, array $args = array()) { 
        $reflectionClass = $this->newReflectionClass($className);
        return $reflectionClass->newInstanceArgs($args);
    }
    
    /**
     * Run's a lookup for the session bean with the passed class name and 
     * session ID. If the passed class name is a session bean an instance
     * will be returned.
     * 
     * @param string $className The name of the session bean's class
     * @param string $sessionId The session ID
     * @param array $args The arguments passed to the session beans constructor
     * @return object The requested session bean
     * @throws \Exception Is thrown if passed class name is no session bean
     */
    public function lookup($className, $sessionId, array $args = array()) {
        
        // get the reflection class for the passed class name
        $reflectionClass = $this->newReflectionClass($className);
        
        // if the class is a stateless session bean simply return a new instance
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Stateless')) {
            return $reflectionClass->newInstanceArgs($args);
        }
        
        // if the class is a statefull session bean, first check the container for a initialized instance
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Statefull')) {
            
            // load the session's from the initial context
            $session = $this->getAttribute($sessionId);
            
            // if an instance exists, load and return it
            if (is_array($session)) {              
                if (array_key_exists($className, $session)) {
                    return $session[$className];
                }
            } else {
                $session = array();
            }
            
            // if not, initialize a new instance, add it to the container and return it
            $instance = $reflectionClass->newInstanceArgs($args);           
            $session[$className] = $instance;           
            $this->setAttribute($sessionId, $session);        
            return $instance;
        }
        
        // if the class is a singleton session bean, return the singleton instance if available
        if ($reflectionClass->implementsInterface('TechDivision\PersistenceContainer\Interfaces\Singleton')) {
            
            // check if an instance is available
            if ($this->getAttribute($className)) {
                return $this->getAttribute($className);
            }
            
            // if not create a new instance and return it
            $instance = $reflectionClass->newInstanceArgs($args);            
            $this->setAttribute($className, $instance);           
            return $instance;
        }
        
        // if the class is no session bean, throw an exception
        throw new \Exception("Can\'t find session bean with class name '$className'");
    }
}