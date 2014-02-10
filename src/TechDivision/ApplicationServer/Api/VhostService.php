<?php
/**
 * TechDivision\ApplicationServer\Api\VhostService
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

namespace TechDivision\ApplicationServer\Api;

/**
 * A stateless session bean implementation handling the vhost data.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class VhostService extends AbstractService
{

    /**
     * Returns all vhost configurations.
     *
     * @return \stdClass The vhost configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $vhostNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {
            foreach ($containerNode->getHost()->getVhosts() as $vhostNode) {
                $vhostNodes[$vhostNode->getPrimaryKey()] = $vhostNode;
            }
        }
        return $vhostNodes;
    }

    /**
     * Returns the vhost with the passed UUID.
     *
     * @param string $uuid The UUID of the vhost to return
     *
     * @return \TechDivision\ApplicationServer\Api\Node\VhostNode The vhost with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {

    }
}
