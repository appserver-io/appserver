<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AuthenticatorsNodeTrait
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
 * Trait to handle authenticator nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait AuthenticatorsNodeTrait
{

    /**
     * The authenticator configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="authenticators/authenticator", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AuthenticatorNode")
     */
    protected $authenticators = array();

    /**
     * Sets the authenticator configuration.
     *
     * @param array $authenticators The authenticator configuration
     *
     * @return void
     */
    public function setAuthenticators($authenticators)
    {
        $this->authenticators = $authenticators;
    }

    /**
     * Returns the authenticator configuration.
     *
     * @return array The authenticator configuration
     */
    public function getAuthenticators()
    {
        return $this->authenticators;
    }
}
