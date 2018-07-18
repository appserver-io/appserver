<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;

/**
 * Abstract node that serves nodes having a rewrites/rewrite child.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait RewritesNodeTrait
{
    /**
     * The virtual host specific rewrite rules.
     *
     * @var array
     * @DI\Mapping(nodeName="rewrites/rewrite", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\RewriteNode")
     */
    protected $rewrites = array();

    /**
     * Will return the rewrites array.
     *
     * @return array
     */
    public function getRewrites()
    {
        return $this->rewrites;
    }

    /**
     * Will return the rewrite node with the specified condition and if nothing could be found we will return false.
     *
     * @param string $condition The condition of the rewrite in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RewriteNode|boolean The requested rewrite node
     */
    public function getRewrite($condition)
    {

        // iterate over all rewrites
        foreach ($this->getRewrites() as $rewriteNode) {
            // if we found one with a matching condition we will return it
            if ($rewriteNode->getCondition() === $condition) {
                return $rewriteNode;
            }
        }

        // we did not find anything
        return false;
    }

    /**
     * Returns the rewrites as an associative array.
     *
     * @return array The array with the sorted rewrites
     */
    public function getRewritesAsArray()
    {

        // initialize the array for the rewrites
        $rewrites = array();

        // prepare the array with the rewrite rules
        /** @var \AppserverIo\Appserver\Core\Api\Node\RewriteNode $rewrite */
        foreach ($this->getRewrites() as $rewrite) {
            // rewrites might be extended using different injector extension types, check for that
            if ($rewrite->hasInjector()) {
                $target = $rewrite->getInjection();
            } else {
                $target = $rewrite->getTarget();
            }

            // build up the array entry
            $rewrites[] = array(
                'condition' => $rewrite->getCondition(),
                'target' => $target,
                'flag' => $rewrite->getFlag()
            );
        }

        // return the array
        return $rewrites;
    }
}
