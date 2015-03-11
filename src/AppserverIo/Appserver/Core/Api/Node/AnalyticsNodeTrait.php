<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AnalyticsNodeTrait
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

/**
 * Trait which allows for the management of analytic nodes within another node.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait AnalyticsNodeTrait
{

    /**
     * The analytics specified within the parent node
     *
     * @var array
     * @AS\Mapping(nodeName="analytics/analytic", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AnalyticNode")
     */
    protected $analytics = array();

    /**
     * Will return the analytics array
     *
     * @return array The array with the analytic nodes
     */
    public function getAnalytics()
    {
        return $this->analytics;
    }

    /**
     * Will return the analytic node with the specified definition and if nothing could
     * be found we will return false
     *
     * @param string $uri The URI of the analytic in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AnalyticNode|boolean The requested analytics node
     */
    public function getAnalytic($uri)
    {

        // iterate over all analytics
        /** @var \AppserverIo\Appserver\Core\Api\Node\AnalyticNode $analyticNode */
        foreach ($this->getAnalytics() as $analyticNode) {
            // if we found one with a matching URI we will return it
            if ($analyticNode->getUri() === $uri) {
                return $analyticNode;
            }
        }

        // we did not find anything
        return false;
    }

    /**
     * Returns the analytics as an associative array
     *
     * @return array The array with the sorted analytics
     */
    public function getAnalyticsAsArray()
    {

        // initialize the array for the analytics
        $analytics = array();

        // iterate over the analytics nodes and sort them into an array
        /** @var \AppserverIo\Appserver\Core\Api\Node\AnalyticNode $analyticNode */
        foreach ($this->getAnalytics() as $analyticNode) {
            // restructure to an array
            $analytics[] = array(
                'uri' => $analyticNode->getUri(),
                'connectors' => $analyticNode->getConnectorsAsArray()
            );
        }

        // return the array
        return $analytics;
    }
}
