<?php

/**
 * TechDivision\ApplicationServer\SocketReceiver
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
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class SocketReceiver extends AbstractReceiver {
    
    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {
        
        try {
        
            // load the receiver params
            $parameters = $this->getContainer()->getParameters();
            
            // load the stackable type
            $stackableType = $this->getContainer()->getStackableType();
            
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
                    
                    // accept a new connection and process it asynchrously
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
}