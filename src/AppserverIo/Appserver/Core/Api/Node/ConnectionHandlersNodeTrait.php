<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ConnectionHandlersNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Trait which allows for the management of connection handler nodes within another node.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ConnectionHandlersNodeTrait
{

    /**
     * The connection handlers.
     *
     * @var array
     * @AS\Mapping(nodeName="connectionHandlers/connectionHandler", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ConnectionHandlerNode")
     */
    protected $connectionHandlers = array();

    /**
     * Returns the connection handler nodes.
     *
     * @return array
     */
    public function getConnectionHandlers()
    {
        return $this->connectionHandlers;
    }

    /**
     * Returns the connection handlers as an associative array
     *
     * @return array The array with the sorted connection handlers
     */
    public function getConnectionHandlersAsArray()
    {

        // initialize the array for the connection handlers
        $connectionHandlers = array();

        // iterate over the connection handler nodes and sort them into an array
        foreach ($this->getConnectionHandlers() as $connectionHandler) {
            $connectionHandlers[$connectionHandler->getUuid()] = $connectionHandler->getType();
        }

        // return the array
        return $connectionHandlers;
    }
}
