<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AccessesNodeTrait
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
 * Abstract node that serves nodes having a rewrites/rewrite child.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait AccessesNodeTrait
{

    /**
     * The access definitions
     *
     * @var array
     * @AS\Mapping(nodeName="accesses/access", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AccessNode")
     */
    protected $accesses = array();

    /**
     * Will return access definitions
     *
     * @return array
     */
    public function getAccesses()
    {
        return $this->accesses;
    }

    /**
     * Returns the rewrites as an associative array.
     *
     * @return array The array with the sorted rewrites
     */
    public function getAccessesAsArray()
    {

        // initialize the array for the accesses
        $accesses = array();

        // iterate over the access nodes and sort them into an array
        /**
         * @var \AppserverIo\Appserver\Core\Api\Node\AccessNode $accessNode
         */
        foreach ($this->getAccesses() as $accessNode) {
            $accesses[$accessNode->getType()][] = $accessNode->getParamsAsArray();
        }

        // return the array
        return $accesses;
    }
}
