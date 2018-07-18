<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ListenersNodeTrait
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

use AppserverIo\Description\Annotations as DI;

/**
 *
 * Trait handling listeners nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ListenersNodeTrait
{

    /**
     * The contexts class loader configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="listeners/listener", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ListenerNode")
     */
    protected $listeners = array();

    /**
     * Sets the listener configuration for the bootstrap process.
     *
     * @param array $listener The listener configuration
     *
     * @return void
     */
    public function setListeners($listener)
    {
        $this->listener = $listener;
    }

    /**
     * Returns the listener configuration for the bootstrap process.
     *
     * @return array The listener configuration
     */
    public function getListeners()
    {
        return $this->listeners;
    }
}
