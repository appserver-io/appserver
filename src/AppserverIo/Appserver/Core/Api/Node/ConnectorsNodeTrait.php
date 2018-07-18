<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ConnectorsNodeTrait
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
 * Trait which allows for the management of connector nodes within another node.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ConnectorsNodeTrait
{

    /**
     * The connectors specified within the parent node
     *
     * @var array
     * @DI\Mapping(nodeName="connectors/connector", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ConnectorNode")
     */
    protected $connectors = array();

    /**
     * Will return the connectors array
     *
     * @return array The array with the connector nodes
     */
    public function getConnectors()
    {
        return $this->connectors;
    }

    /**
     * Will return the connector node with the specified definition and if nothing could
     * be found we will return false
     *
     * @param string $name The name of the connector in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ConnectorNode|boolean The requested connectors node
     */
    public function getConnector($name)
    {
        // Iterate over all connectors
        /** @var \AppserverIo\Appserver\Core\Api\Node\ConnectorNode $connectorNode */
        foreach ($this->getConnectors() as $connectorNode) {
            // If we found one with a matching URI we will return it
            if ($connectorNode->getName() === $name) {
                return $connectorNode;
            }
        }

        // Still here? Seems we did not find anything
        return false;
    }

    /**
     * Returns the connectors as an associative array
     *
     * @return array The array with the sorted connectors
     */
    public function getConnectorsAsArray()
    {
        // Iterate over the connectors nodes and sort them into an array
        $connectors = array();
        /** @var \AppserverIo\Appserver\Core\Api\Node\ConnectorNode $connectorNode */
        foreach ($this->getConnectors() as $connectorNode) {
            // Restructure to an array
            $connectors[] = array(
                'name' => $connectorNode->getName(),
                'type' => $connectorNode->getType(),
                'params' => $connectorNode->getParamsAsArray()
            );
        }

        // Return what we got
        return $connectors;
    }
}
