<?php
/**
 * TechDivision\ApplicationServer\Stream\SecureReceiver
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Stream
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Stream;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 * Class SecureReceiver
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Stream
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class SecureReceiver extends AbstractReceiver
{
    /**
     * Sets up the specific socket instance
     *
     * @return void
     */
    protected function setupSocket()
    {
        // first call parent setup routine
        parent::setupSocket();
        // set secure receiver params
        $this->getSocket()
            ->setCertPath($this->getCertPath())
            ->setCertPassphrase($this->getCertPassphrase());
    }

    /**
     * Returns the path to the certificate for ssl connections.
     *
     * @return string The path to cert file
     */
    public function getCertPath()
    {
        return $this->getContainer()->getContainerNode()->getReceiver()->getParam('certPath');
    }

    /**
     * Returns the passphrase for the certificate.
     *
     * @return string The path to cert file
     */
    public function getCertPassphrase()
    {
        return $this->getContainer()->getContainerNode()->getReceiver()->getParam('certPassphrase');
    }

    /**
     * Returns the resource class used to create a new socket.
     *
     * @return string The resource class name
     * @see \TechDivision\ApplicationServer\AbstractReceiver::getResourceClass()
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Stream\SecureServer';
    }
}
