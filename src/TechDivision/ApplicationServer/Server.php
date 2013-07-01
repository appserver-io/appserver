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
    const XPATH_CONTAINERS = '/appserver';
    
    /**
     * The container configurations.
     * @var array
     */
    protected $configurations = array();

    /**
     * Initialize the array for the running threads.
     * @var array
     */
    protected $threads = array();
    
    /**
     * Start's the server and initializes the containers.
     * 
     * @return void
     */
    public function start() {
        
        // initialize the SimpleXMLElement with the content XML configuration file
        $sxe = simplexml_load_file('cfg/appserver.xml');
        
        // load the container configurations
        $this->configurations = Configuration::loadFromFile('cfg/appserver.xml');
        
        // start each container in his own thread
        foreach ($this->configurations->getChilds('/appserver/containers/container') as $i => $configuration) {
            $this->threads[$i] = new ContainerThread($configuration);
            $this->threads[$i]->start();
        }
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
}