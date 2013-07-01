<?php

/**
 * TechDivision\ApplicationServer\Interfaces\SenderInterface
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
interface SenderInterface {
    
    /**
     * Sends the passed data to the receiver (the client by default).
     * 
     * @param string $data The data to send to the client
     * @return void
     */
    public function sendLine($data);
    
    /**
     * Closes the sender and all connections, e. g. to the client.
     * 
     * @return void 
     */
    public function close();
    
    /**
     * Prepares the sender with the data of the passed remote method and
     * returns the initialized instance, ready to send data.
     * 
     * @param \TechDivision\PersistenceContainerClient\Interfaces\RemoteMethod $remoteMethod The remote method
     * @return \TechDivision\ApplicationServer\Interfaces\SenderInterface The instance itself
     */
    public function prepare($remoteMethod);
}