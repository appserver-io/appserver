<?php
/**
 * TechDivision\ServletContainer\Stream\Worker
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Stream
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Stream;

use TechDivision\ApplicationServer\AbstractWorker;

/**
 * The worker implementation that handles the request.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Stream
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class Worker extends AbstractWorker
{

    /**
     * Returns the resource class used to receive data over the socket.
     *
     * @return string.
     * @see \TechDivision\ApplicationServer\AbstractWorker::getResourceClass()
     */
    protected function getResourceClass()
    {
        return 'TechDivision\Stream';
    }
}
