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
     * @return void
     */
    public function start();
    
    /**
     * Stacks the passed request to one of the
     * internal workers and returns.
     * 
     * If enabled the garbage collection will be run and the configuration
     * will be refreshed.
     * 
     * @param \Stackable $request The request to be stacked
     * @return void
     */
    public function stack(\Stackable $request);
    
    /**
     * Returns the maximum number of workers to start.
     * 
     * @return integer The maximum worker number 
     */
    public function getWorkerNumber();
    
    /**
     * Returns the worker class name to use.
     * 
     * @return string The worker class name 
     */
    public function getWorkerType();
    
    /**
     * Returns the stackable class name to use.
     * 
     * @return string The stackable class name 
     */
    public function getStackableType();
}