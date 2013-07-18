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

use TechDivision\ApplicationServer\AbstractReceiver;

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
        $this->setWorkerType($this->getContainer()->getWorkerType());
    }

    /**
     * @see TechDivision\ApplicationServer\Interfaces\ReceiverInterface::start()
     */
    public function start() {

        try {
        
            // load the receiver params
            $parameters = $this->getContainer()->getParameters();
            
            // load the socket instance
            /** @var \TechDivision\Socket\Client $socket */
            $socket = $this->newInstance('\TechDivision\Socket\Client');
            
            // prepare the main socket and listen
            $socket->create()
                   ->setAddress($parameters->getAddress())
                   ->setPort($parameters->getPort())
                   ->setBlock()
                   ->setReuseAddr()
                   ->bind()
                   ->listen();

            try {
                // check if resource been initiated
                if ($socket->getResource()) {
                    // init worker number
                    $worker = 0;
                    // init workers array holder
                    $workers = array();
                    // open threads where accept connections
                    while ($worker++ < $this->getWorkerNumber()) {
                        // init thread
                        $workers[$worker] = $this->newThread($socket->getResource());
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
     * Returns a thread
     *
     * @return \Thread The request acceptor thread
     */
    public function newThread($socketResource) {
        return $this->newInstance($this->getWorkerType(), array($this->getContainer(), $socketResource));
    }

}