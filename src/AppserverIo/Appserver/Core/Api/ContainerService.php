<?php
/**
 * AppserverIo\Appserver\Core\Api\ContainerService
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

use AppserverIo\Appserver\Core\Api\AbstractService;

/**
 * A service that handles container configuration data.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ContainerService extends AbstractService
{

    /**
     * Return's all container node configurations.
     *
     * @return array An array with container node configurations
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::findAll()
     */
    public function findAll()
    {
        return $this->getSystemConfiguration()->getContainers();
    }

    /**
     * Returns the container for the passed UUID.
     *
     * @param string $uuid Unique UUID of the container to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContainerNode The container with the UUID passed as parameter
     * @see \AppserverIo\Appserver\Core\Api\ServiceInterface::load($uuid)
     */
    public function load($uuid)
    {
        $containers = $this->findAll();
        if (array_key_exists($uuid, $containers)) {
            return $containers[$uuid];
        }
    }

    /**
     * Returns the application base directory for the container
     * with the passed UUID.
     *
     * @param string $uuid UUID of the container to return the application base directory for
     *
     * @return string The application base directory for this container
     */
    public function getAppBase($uuid)
    {
        return $this->load($uuid)->getHost()->getAppBase();
    }
}
