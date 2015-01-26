<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\EnvironmentVariableNode
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
 * Node class which represents the EnvironmentVariable node of the configuration.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EnvironmentVariableNode extends AbstractNode
{
    /**
     * The condition under which the definition should take place
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $condition;

    /**
     * The definition to perform
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $definition;

    /**
     * Returns the condition for the variable definition to take place
     *
     * @return string The condition under which we set the variable
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Returns the actual definition
     *
     * @return string The actual definition
     */
    public function getDefinition()
    {
        return $this->definition;
    }
}
