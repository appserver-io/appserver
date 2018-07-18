<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\VirtualHostsNodeTrait
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
 * Trait which allows for the management of analytic nodes within another node.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait VirtualHostsNodeTrait
{

    /**
     * The virtual hosts.
     *
     * @var array
     * @DI\Mapping(nodeName="virtualHosts/virtualHost", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\VirtualHostNode")
     */
    protected $virtualHosts = array();

    /**
     * Returns the virtual hosts.
     *
     * @return array
     */
    public function getVirtualHosts()
    {
        return $this->virtualHosts;
    }

    /**
     * Returns the virtual hosts as an associative array
     *
     * @return array The array with the sorted virtual hosts
     */
    public function getVirtualHostsAsArray()
    {

        // prepare the array containing the virtual host configuration
        $virtualHosts = array();

        // iterate hosts
        /** @var \AppserverIo\Appserver\Core\Api\Node\VirtualHostNode $virtualHost */
        foreach ($this->getVirtualHosts() as $virtualHost) {
            // explode the virtual host names
            $virtualHostNames = explode(' ', $virtualHost->getName());

            // Some virtual hosts might have an extensionType to expand their name attribute, check for that
            if ($virtualHost->hasInjector()) {
                $virtualHostNames = array_merge($virtualHostNames, explode(' ', $virtualHost->getInjection()));
            }

            // prepare the virtual hosts
            foreach ($virtualHostNames as $virtualHostName) {
                // set all virtual hosts params per key for faster matching later on
                $virtualHosts[trim($virtualHostName)] = array(
                    'params' => $virtualHost->getParamsAsArray(),
                    'headers' => $virtualHost->getHeadersAsArray(),
                    'rewriteMaps' => $virtualHost->getRewriteMapsAsArray(),
                    'rewrites' => $virtualHost->getRewritesAsArray(),
                    'environmentVariables' => $virtualHost->getEnvironmentVariablesAsArray(),
                    'accesses' => $virtualHost->getAccessesAsArray(),
                    'locations' => $virtualHost->getLocationsAsArray(),
                    'authentications' => $virtualHost->getAuthenticationsAsArray(),
                    'analytics' => $virtualHost->getAnalyticsAsArray()
                );
            }
        }

        // return the virtual host configuration
        return $virtualHosts;
    }
}
