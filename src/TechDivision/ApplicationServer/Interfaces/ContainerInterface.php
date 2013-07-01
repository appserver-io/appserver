<?php

/**
 * TechDivision\ApplicationServer\Interfaces\ContainerInterface
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
interface ContainerInterface {
    
    /**
     * Starts thei containers deployment process.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ContainerInterface The instance itself
     */
    public function deploy();

    /**
     * Main method that starts the server.
     * 
     * @return void
     */
    public function start();
    
    /**
     * Returns the receiver instance ready to be started.
     * 
     * @return \TechDivision\ApplicationServer\Interfaces\ReceiverInterface The receiver instance
     */
    public function getReceiver();
}