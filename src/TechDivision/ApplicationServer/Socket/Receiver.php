<?php
/**
 * TechDivision\ApplicationServer\Socket\Receiver
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Socket
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Socket;

use TechDivision\ApplicationServer\AbstractReceiver;

/**
 * Class Receiver
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Socket
 * @author     Johann Zelger <jz@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class Receiver extends AbstractReceiver
{

    /**
     * Returns the resource class used to create a new socket.
     *
     * @return string The resource class name
     * @see \TechDivision\ApplicationServer\AbstractReceiver::getResourceClass()
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Socket\Server';
    }
}
