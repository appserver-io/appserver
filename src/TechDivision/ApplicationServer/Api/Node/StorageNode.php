<?php
/**
 * TechDivision\ApplicationServer\Api\Node\StorageNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * DTO to transfer storage information.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StorageNode extends AbstractNode
{

    /**
     * The storage class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Array with the servers used by the storage.
     *
     * @var array
     * @AS\Mapping(nodeName="storageServers/storageServer", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\StorageServerNode")
     */
    protected $storageServers = array();

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the array with the servers used by the storage.
     *
     * @return array The servers used by the storage
     */
    public function getStorageServers()
    {
        return $this->storageServers;
    }
}
