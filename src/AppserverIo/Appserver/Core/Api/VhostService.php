<?php
/**
 * AppserverIo\Appserver\Core\Api\VhostService
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

namespace AppserverIo\Appserver\Core\Api;

/**
 * A stateless session bean implementation handling the vhost data.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class VhostService extends AbstractService
{

    /**
     * Returns all vhost configurations.
     *
     * @return \stdClass The vhost configurations
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
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
     * @return \AppserverIo\Appserver\Core\Api\Node\VhostNode The vhost with the UUID passed as parameter
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {

    }
}
