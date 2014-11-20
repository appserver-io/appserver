<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ManagersNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 *
 * Abstract node that a contexts manager nodes.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait ManagersNodeTrait
{

    /**
     * The contexts manager configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="managers/manager", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ManagerNode")
     */
    protected $managers = array();

    /**
     * Sets the contexts manager configuration.
     *
     * @param array $managers The contexts manager configuration
     *
     * @return void
     */
    public function setManagers($managers)
    {
        $this->managers = $managers;
    }

    /**
     * Returns the contexts manager configuration.
     *
     * @return array The contexts manager configuration
     */
    public function getManagers()
    {
        return $this->managers;
    }
}
