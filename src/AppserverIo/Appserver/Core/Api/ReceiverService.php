<?php
/**
 * AppserverIo\Appserver\Core\Api\ReceiverService
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
 * A service that handles receiver configuration data.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ReceiverService extends AbstractService
{

    /**
     * Return's all receiver configurations.
     *
     * @return array The receiver configurations
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        $receiverNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $container) {
            $receiverNode = $container->getReceiver();
            $receiverNodes[$receiverNode->getUuid()] = $receiverNode;
        }
        return $receiverNodes;
    }

    /**
     * Returns the receiver node for the passed UUID.
     *
     * @param string $uuid UUID of the container to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ReceiverNode The receiver node with the UUID passed as parameter
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {
        $receivers = $this->findAll();
        if (array_key_exists($uuid, $receivers)) {
            return $receivers[$uuid];
        }
    }
}
