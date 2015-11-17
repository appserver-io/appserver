<?php

/**
 * \AppserverIo\Appserver\Core\Api\VirtualHostService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

/**
 * This services provides access to the deployed application
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class VirtualHostService extends AbstractService
{

    /**
     * Returns all configured virtual hosts.
     *
     * @return array All configured virtual hosts
     * @see ServiceInterface::findAll()
     */
    public function findAll()
    {
        $virtualHostNodes = array();
        foreach ($this->getSystemConfiguration()->getContainers() as $containerNodes) {
            foreach ($containerNodes->getServers() as $serverNode) {
                foreach ($serverNode->getVirtualHosts() as $virtualHostNode) {
                    $virtualHostNodes[$virtualHostNode->getPrimaryKey()] = $virtualHostNode;
                }
            }
        }
        return $virtualHostNodes;
    }

    /**
     * Returns the virtual host with the passed UUID.
     *
     * @param string $uuid UUID of the virtual host to return
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\VirtualHostNode|null The virtual host with the UUID passed as parameter
     * @see ServiceInterface::load()
     */
    public function load($uuid)
    {
        foreach ($this->findAll() as $virtualHostNode) {
            if ($virtualHostNode->getPrimaryKey() == $uuid) {
                return $virtualHostNode;
            }
        }
    }
}
