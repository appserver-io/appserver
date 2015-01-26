<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\RewriteMapsNodeTrait
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Abstract node that serves nodes having a rewriteMaps/rewriteMap child.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait RewriteMapsNodeTrait
{
    /**
     * The rewriteMaps definitions
     *
     * @var array
     * @AS\Mapping(nodeName="rewriteMaps/rewriteMap", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\RewriteMapNode")
     */
    protected $rewriteMaps = array();

    /**
     * Will return rewriteMaps definitions
     *
     * @return array
     */
    public function getRewriteMaps()
    {
        return $this->rewriteMaps;
    }

    /**
     * Returns the rewriteMaps as an associative array.
     *
     * @return array The array with the rewriteMaps
     */
    public function getRewriteMapsAsArray()
    {

        // initialize the array for the rewrite maps
        $rewriteMaps = array();

        // iterate over the rewriteMaps nodes and sort them into an array
        foreach ($this->getRewriteMaps() as $rewriteMapNode) {
            $rewriteMaps[$rewriteMapNode->getType()] = $rewriteMapNode->getParamsAsArray();
        }

        // return the array
        return $rewriteMaps;
    }
}
