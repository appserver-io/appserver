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
    const XPATH_CONTAINERS = '/appserver';

    /**
     * Initialize the array for the running threads.
     * @var array
     */
    protected $threads = array();

    /**
     * The server's base directory.
     * @var string
     */
    protected $baseDirectory;

    /**
     * Initializes the the server with the base directory.
     *
     * @return void
     */
    public function __construct() {
        $this->baseDirectory = APPSERVER_BP;
    }

    /**
     * Returns the server's base directory.
     *
     * @return string The server's base directory
     */
    public function getBaseDirectory() {
        return $this->baseDirectory;
    }

    /**
     * Start's the server and initializes the containers.
     *
     * @param string $configurationFile The path to the configuration file relativ to the base directory
     * @return void
     * @see TechDivision\ApplicationServer\Server::getBaseDirectory()
     */
    public function start($configurationFile = 'etc/appserver.xml') {
        
        // the initial context instance to use
        $initialContext = new InitialContext();
        $initialContext->setClassLoader(new SplClassLoader());

        // initialize the SimpleXMLElement with the content XML configuration file
        $configuration = $initialContext->newInstance('TechDivision\ApplicationServer\Configuration');
        $configuration->initFromFile($this->getBaseDirectory() . APPSERVER_DS . $configurationFile);

        // initialize a configuration node containing the base directory
        $node = $initialContext->newInstance('TechDivision\ApplicationServer\Configuration');
        $node->setNodeName('baseDirectory');
        $node->setValue($this->getBaseDirectory());

        // start each container in his own thread
        foreach ($configuration->getChilds('/appserver/containers/container') as $i => $containerConfiguration) {
            
            // add the base directory to the container configuration
            $containerConfiguration->addChild($node);
            
            // initialize the container configuration with the base directory and pass it to the thread
            $this->threads[$i] = $initialContext->newInstance('TechDivision\ApplicationServer\ContainerThread', array($initialContext, $containerConfiguration));
            $this->threads[$i]->start();
        }
    }
}