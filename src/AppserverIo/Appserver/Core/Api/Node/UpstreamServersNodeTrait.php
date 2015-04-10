<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\UpstreamServersNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Trait for upstream server nodes
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait UpstreamServersNodeTrait
{
    /**
     * The upstream servers
     *
     * @var array
     * @AS\Mapping(nodeName="servers/server", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\UpstreamServerNode")
     */
    protected $upstreamServers = array();

    /**
     * Returns the upstream servers
     *
     * @return array
     */
    public function getUpstreamServers()
    {
        return $this->upstreamServers;
    }

    /**
     * Returns the upstream servers as an associative array
     *
     * @return array The array with the upstream servers
     */
    public function getUpstreamServersAsArray()
    {
        // iterate over all upstream servers nodes and transform them to an array
        $upstreamServers = array();
        /** @var \AppserverIo\Appserver\Core\Api\Node\UpstreamServerNode $upstreamServerNode */
        foreach ($this->getUpstreamServers() as $upstreamServerNode) {
            // build up array
            $upstreamServers[] = array(
                'name' => $upstreamServerNode->getName(),
                'type' => $upstreamServerNode->getType(),
                'params' => $upstreamServerNode->getParamsAsArray()
            );
        }
        // return upstream servers array
        return $upstreamServers;
    }
}
