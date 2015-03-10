<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AbstractArgsNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Abstract node that serves nodes having a args/arg child.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractArgsNode extends AbstractNode
{

    /**
     * The script args to use.
     *
     * @var array
     * @AS\Mapping(nodeName="args/arg", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ArgNode")
     */
    protected $args = array();

    /**
     * Attaches the passed arg to the list.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ArgNode $arg The arg to attach
     *
     * @return void
     */
    public function attachArg(ArgNode $arg)
    {
        $this->args[] = $arg;
    }

    /**
     * Array with the args to use.
     *
     * @return array
     */
    public function getArgs()
    {
        return $this->args;
    }

    /**
     * Returns the arg with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the arg to be returned
     *
     * @return mixed The requested arg casted to the specified type
     */
    public function getArg($name)
    {
        $args = $this->getArgsAsArray();
        if (array_key_exists($name, $args)) {
            return $args[$name];
        }
    }

    /**
     * Returns the args casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted args
     */
    public function getArgsAsArray()
    {
        $args = array();
        /**
         * @var \AppserverIo\Appserver\Core\Api\Node\ArgNode $arg
         */
        foreach ($this->getArgs() as $arg) {
            $args[$arg->getName()] = $arg->castToType();
        }
        return $args;
    }
}
