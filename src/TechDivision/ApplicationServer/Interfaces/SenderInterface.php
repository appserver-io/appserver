<?php
/**
 * TechDivision\ApplicationServer\Interfaces\SenderInterface
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Interfaces
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Interfaces;

/**
 * Interface SenderInterface
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Interfaces
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface SenderInterface
{
    
    /**
     * Sends the passed data to the receiver (the client by default).
     * 
     * @param string $data The data to send to the client
     *
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
     *
     * @return \TechDivision\ApplicationServer\Interfaces\SenderInterface The instance itself
     */
    public function prepare($remoteMethod);
}
