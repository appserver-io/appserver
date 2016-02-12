<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SessionHandlersNodeTrait
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
 * Trait to handle session handler nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait SessionHandlersNodeTrait
{

    /**
     * The session handler configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="sessionHandlers/sessionHandler", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\SessionHandlerNode")
     */
    protected $sessionHandlers = array();

    /**
     * Sets the session handler configuration.
     *
     * @param array $sessionHandlers The session handler configuration
     *
     * @return void
     */
    public function setSessionHandlers($sessionHandlers)
    {
        $this->sessionHandlers = $sessionHandlers;
    }

    /**
     * Returns the session handler configuration.
     *
     * @return array The session handler configuration
     */
    public function getSessionHandlers()
    {
        return $this->sessionHandlers;
    }
}
