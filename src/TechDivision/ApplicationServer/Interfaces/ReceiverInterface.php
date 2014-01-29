<?php

/**
 * TechDivision\ApplicationServer\Interfaces\ReceiverInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer\Interfaces;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
interface ReceiverInterface {
    
    /**
     * Starts the receiver in an infinite loop.
     * 
     * @return boolean TRUE if the receiver has been started successfully, else FALSE
     */
    public function start();
    
    /**
     * Returns the maximum number of workers to start.
     * 
     * @return integer The maximum worker number 
     */
    public function getWorkerNumber();
    
    /**
     * Returns the IP address to listen to.
     * 
     * @return string The IP address to listen to
     */
    public function getAddress();
    
    /**
     * Returns the port to listen to.
     * 
     * @return integer The port to listen to
     */
    public function getPort();
    
    /**
     * Returns the worker class name to use.
     * 
     * @return string The worker class name 
     */
    public function getWorkerType();
    
    /**
     * Returns the thread class name to use.
     * 
     * @return string The thread class name 
     */
    public function getThreadType();
}