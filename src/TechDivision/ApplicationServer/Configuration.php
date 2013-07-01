<?php

/**
 * TechDivision\ApplicationServer\Configuration
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ContainerConfiguration;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class Configuration implements ContainerConfiguration {

    /**
     * the node name to use.
     * @var string
     */
    protected $nodeName;
    
    /**
     * The node value.
     * @var string
     */
    protected $value;
    
    /**
     * The array with configuration parameters.
     * @var array
     */
    protected $data = array();
    
    /**
     * The array with the child configurations.
     * @var array
     */
    protected $children = array();
    
    /**
     * Initializes the configuration with the node name of the
     * node in the XML structure.
     * 
     * @param string $nodeName The configuration element's node name
     * @return void
     */
    public function __construct($nodeName = null) {
        $this->setNodeName($nodeName);
    }
    
    /**
     * Set's the configuration element's node name.
     * 
     * @param string $nodeName The node name
     * @return \TechDivision\ApplicationServer\Configuration The instance itself
     */
    public function setNodeName($nodeName) {
        $this->nodeName = $nodeName;
    }
    
    /**
     * Return's the configuration element's node name.
     * 
     * @return string The node name
     */
    public function getNodeName() {
        return $this->nodeName;
    }
    
    /**
     * @see \TechDivision\ApplicationServer\Interfaces\ContainerConfiguration::equals()
     */
    public function equals($configuration) {
         return $this === $configuration;
    }
    
    /**
     * Adds a new child configuration.
     *
     * @param Configuration $configuration The child configuration itself
     * @return \TechDivision\ApplicationServer\Configuration The configuration instance
     */
    public function addChild($configuration) {
        $this->children[] = $configuration;
    }
    
    /**
     * 
     * @param unknown $file
     * @return \TechDivision\ApplicationServer\Configuration
     */
    public static function loadFromFile($file) {

        // initialize the SimpleXMLElement with the content XML configuration file
        $root = simplexml_load_file($file);
        
        // initialize and return the root node
        $cnt = new Configuration();
        return $cnt->init($root);
    }
    
    /**
     * Recursively initializes the configuration instance with the data from the
     * passed SimpleXMLElement.
     * 
     * @param \SimpleXMLElement $node The node to load the data from
     * @param string $xpath The XPath expression of the XML node to load the data from
     * @return \TechDivision\ApplicationServer\Configuration The node instance itself
     */
    public function init($node, $xpath = '/') {
        
        // set the node name + value
        $this->setNodeName($node->getName());
        
        $nodeValue = (string) $node;
        
        if (empty($nodeValue) === false) {
        	$this->setValue(trim($nodeValue));
        }
            
        // load the attributes
        foreach ($node->attributes() as $key => $value) {
            $this->setData($key, (string) $value);
        }
        
        // append childs
        foreach ($node->children() as $name => $child) {
            
            // create a new configuration node
            $cnt = new Configuration();
            
            // parse the configuration recursive
            $cnt->init($child, $name);
        
            // append the configuration node to the parent
            $this->addChild($cnt);
        }
        
        // return the instance node itself
        return $this;
    }
    
    /**
     * Returns the child configuration with the passed type.
     * 
     * @param string $name The name of the configuration to return
     * @return Configuration The requested configuration
     */
    public function getChilds($path) {

        $token = strtok($path, '/');

        $next = substr($path, strlen('/' . $token));
        
        if ($this->getNodeName() == $token && empty($next)) {
        
            return $this;
            
        } elseif ($this->getNodeName() == $token && !empty($next)) {

            $matches = array();

            foreach ($this->getChildren() as $child) {
                
                $result = $child->getChilds($next);
                
                if (is_array($result)) {
                    $matches = $result;
                } elseif ($result instanceof Configuration) {
                    $matches[] = $result;
                } else {
                    // do nothing
                }
            }
            
            return $matches;
            
        } else {
            
            return;
        }
    }
    
    public function getChild($path) {
    	
    	$childs = $this->getChilds($path);
    	
    	if (is_array($childs)) {
    		return current($childs);
    	}
    }
    
    /**
     * Returns all child configurations.
     * 
     * @return array The child configurations 
     */
    public function getChildren() {
        return $this->children;
    }
    
    /**
     * Check's if the node has children, if yes the method
     * returns TRUE, else the method returns FALSE.
     * 
     * @return boolean TRUE if the node has children, else FALSE
     */
    public function hasChildren() {
        // check the children size
        if (sizeof($this->children) == 0) {
            return false;
        }
        return true;
    }
    
    /**
     * Adds the passed configuration value.
     * 
     * @param string $key Name of the configuration value
     * @param mixed $value The configuration value
     */
    public function setData($key, $value) {
        $this->data[$key] = $value;
    }
    
    /**
     * Returns the configuration value with the passed name.
     * 
     * @param string $key The name of the requested configuration value.
     * @return mixed The configuration value itself
     */
    public function getData($key) {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
    }
    
    /**
     * Wrapper method for getter/setter methods.
     * 
     * @param string $method The called method name
     * @param array $args The methods arguments
     * @return mixed The value if a getter has been invoked
     * @throws \Exception Is thrown if nor a getter/setter has been invoked
     */
    public function __call($method, $args) {
                
        // lowercase the first character of the member
        $key = lcfirst(substr($method, 3));
        
        // check if a getter/setter has been called
        switch (substr($method, 0, 3)) {
            case 'get':
                return $this->getData($key);
                break;
            case 'set':
                $this->setData($key, isset($args[0]) ? $args[0] : null);
                break;
            default:
                throw new \Exception("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
        }
    }
    
    public function setValue($value) {
    	$this->value = $value;
    }
    
    public function getValue() {
    	return $this->value;
    }	
    
    public function __toString() {
    	return $this->getValue();
    }
}