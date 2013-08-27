<?php

/**
 * TechDivision\ServletContainer\SocketRequestAcceptor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ContainerInterface;
use TechDivision\ApplicationServer\AbstractContextThread;

/**
 * The thread implementation that handles the request.
 *
 * @package     TechDivision\ServletContainer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 */
class SocketRequestAcceptor extends AbstractContextThread {

    /**
     * Holds the container implementation
     *
     * @var ContainerInterface
     */
    public $container;

    /**
     * Holds the main socket resource
     *
     * @var resource
     */
    public $resource;

    /**
     * The thread implementation classname
     *
     * @var string
     */
    public $threadType;

    /**
     * Init acceptor with container and acceptable socket resource
     * and thread type class.
     *
     * @param ContainerInterface $container A container implementation
     * @param resource $resource The client socket instance
     * @param string $threadType The thread type class to instantiate
     * @return void
     */
    public function init(ContainerInterface $container, $resource, $threadType) {
        $this->container = $container;
        $this->resource = $resource;
        $this->threadType = $threadType;
    }

    /**
     * @see \Thread::run()
     */
    public function main() {
        
        while (true) {
            
            // reinitialize the server socket
            $serverSocket = $this->initialContext->newInstance('TechDivision\Socket', array($this->resource)); 
            
            // accept client connection and process the request
            if ($clientSocket = $serverSocket->accept()) {
                
                $params = array($this->initialContext, $this->container, $clientSocket->getResource());
                $request = $this->initialContext->newInstance($this->threadType, $params);
                $request->start();
            }
        }
    }
}