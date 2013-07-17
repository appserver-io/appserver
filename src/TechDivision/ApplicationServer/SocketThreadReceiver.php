<?php

/**
 * TechDivision\ApplicationServer\SocketThreadReceiver
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\Socket\Client;
use TechDivision\ApplicationServer\AbstractReceiver;
use TechDivision\ServletContainer\WorkerRequest;
use TechDivision\ServletContainer\RequestHandler;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <j.zelger@techdivision.com>
 */
class SocketThreadReceiver extends AbstractReceiver {

    /**
     * Sets the reference to the container instance.
     *
     * @param \TechDivision\ApplicationServer\Interfaces\ContainerInterface $container The container instance
     */
    public function __construct($container) {

        // set the container instance
        $this->container = $container;

        // load the receiver configuration
        $configuration = $this->getContainer()->getReceiverConfiguration();

        // set the receiver configuration
        $this->setConfiguration($configuration);

        // set the configuration in the initial context
        InitialContext::get()->setAttribute(get_class($this), $configuration);

        // enable garbage collector and initialize configuration
        $this->gcEnable()->checkConfiguration();

        // load the thread type
        $this->setThreadType($this->getContainer()->getThreadType());
    }

    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {
        
        try {
        
            // load the receiver params
            $parameters = $this->getContainer()->getParameters();
            
            // load the socket instance
            $socket = $this->newInstance('\TechDivision\Socket\Client');
            
            // prepare the main socket and listen
            $socket->create()
                   ->setAddress($parameters->getAddress())
                   ->setPort($parameters->getPort())
                   ->setBlock()
                   ->setReuseAddr()
                   ->bind()
                   ->listen();
            
            // start the infinite loop and listen to clients (in blocking mode)
            while (true) {
    
                try {
                    
                    // accept a new connection and process it asynchronously
                    $client = $socket->accept();
                    $this->processRequest($client);
                    
                } catch (\Exception $e) {
                    error_log($e->__toString());
                }
            }
            
        } catch (\Exception $ge) {
            
            error_log($ge->__toString());
            
            if (is_resource($socket->getResource())) {
                $socket->close();
            }           
        } 
    }

    /**
     * Process the request by creating a new request instance (stackable)
     * and stack's it on one of the workers.
     *
     * @return void
     */
    public function processRequest(\TechDivision\Socket $socket) {

        // create a new request instance
        $request = $this->newThread(array($this->container, $socket->getResource()));

        // initialize a new worker request instance
        $request->start();
    }
}