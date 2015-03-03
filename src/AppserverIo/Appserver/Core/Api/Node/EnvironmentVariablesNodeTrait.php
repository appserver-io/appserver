<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\EnvironmentVariablesNodeTrait
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
 * This trait is used to give any node class the possibility to manage environmentVariable nodes
 * which might be child elements of it.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait EnvironmentVariablesNodeTrait
{

    /**
     * The environment variables specified within the parent node
     *
     * @var array
     * @AS\Mapping(nodeName="environmentVariables/environmentVariable", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\EnvironmentVariableNode")
     */
    protected $environmentVariables = array();

    /**
     * Will return the environment variables array.
     *
     * @return array The array with the environment variables
     */
    public function getEnvironmentVariables()
    {
        return $this->environmentVariables;
    }

    /**
     * Will return the environmentVariable node with the specified definition and if nothing could
     * be found we will return false.
     *
     * @param string $definition The definition of the environmentVariable in question
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EnvironmentVariableNode|boolean The requested environmentVariable node
     */
    public function getEnvironmentVariable($definition)
    {
        // Iterate over all environmentVariables
        /**
         * @var \AppserverIo\Appserver\Core\Api\Node\EnvironmentVariableNode $environmentVariableNode
         */
        foreach ($this->getEnvironmentVariables() as $environmentVariableNode) {
            // If we found one with a matching definition we will return it
            if ($environmentVariableNode->getDefinition() === $definition) {
                return $environmentVariableNode;
            }
        }

        // Still here? Seems we did not find anything
        return false;
    }

    /**
     * Returns the environmentVariables as an associative array.
     *
     * @return array The array with the sorted environmentVariables
     */
    public function getEnvironmentVariablesAsArray()
    {
        // Iterate over the environmentVariable nodes and sort them into an array
        $environmentVariables = array();
        /**
         * @var \AppserverIo\Appserver\Core\Api\Node\EnvironmentVariableNode $environmentVariableNode
         */
        foreach ($this->getEnvironmentVariables() as $environmentVariableNode) {
            // Restructure to an array
            $environmentVariables[] = array(
                'condition' => $environmentVariableNode->getCondition(),
                'definition' => $environmentVariableNode->getDefinition()
            );
        }

        // Return what we got
        return $environmentVariables;
    }
}
