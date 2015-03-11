<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ContextsNodeTrait
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 *
 * Abstract node that serves a hosts context nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ContextsNodeTrait
{

    /**
     * The servers context configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="contexts/context", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ContextNode")
     */
    protected $contexts = array();

    /**
     * Returns the servers context configuration.
     *
     * @return array servers context configuration
     */
    public function getContexts()
    {
        return $this->contexts;
    }

    /**
     * Returns the context with the passed name.
     *
     * @param string $name The name of the requested context
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ContextNode|null The requested context node
     */
    public function getContext($name)
    {
        /** @var \AppserverIo\Appserver\Core\Api\Node\ContextNode $context */
        foreach ($this->getContexts() as $context) {
            if ($context->getName() === $name) {
                return $context;
            }
        }
    }
}
