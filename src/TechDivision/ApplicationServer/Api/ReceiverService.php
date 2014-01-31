<?php
/**
 * TechDivision\ApplicationServer\Api\ReceiverService
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
 * A service that handles receiver configuration data.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ReceiverService extends AbstractService
{

    /**
     * Return's all receiver configurations.
     *
     * @return array The receiver configurations
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::findAll()
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
     * @return \TechDivision\ApplicationServer\Api\Node\ReceiverNode The receiver node with the UUID passed as parameter
     * @see \TechDivision\ApplicationServer\Api\ServiceInterface::load()
     */
    public function load($uuid)
    {
        $receivers = $this->findAll();
        if (array_key_exists($uuid, $receivers)) {
            return $receivers[$uuid];
        }
    }
}
