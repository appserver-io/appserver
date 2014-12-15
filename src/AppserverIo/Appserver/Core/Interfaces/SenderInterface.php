<?php
/**
 * AppserverIo\Appserver\Core\Interfaces\SenderInterface
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Interfaces;

/**
 * Interface SenderInterface
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
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
     * @param \AppserverIo\PersistenceContainerClient\Interfaces\RemoteMethod $remoteMethod The remote method
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\SenderInterface The instance itself
     */
    public function prepare($remoteMethod);
}
