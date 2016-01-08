<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AuthConfigsNodeTrait
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
 * Trait to handle auth config nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait AuthConfigsNodeTrait
{

    /**
     * The authentication configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="authConfigs/authConfig", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\AuthConfigNode")
     */
    protected $authConfigs = array();

    /**
     * Sets the authentication configuration.
     *
     * @param array $securityDomains The authentication configuration
     *
     * @return void
     */
    public function setAuthConfigs($authConfigs)
    {
        $this->authConfigs = $authConfigs;
    }

    /**
     * Returns the authentication configuration.
     *
     * @return array The authentication configuration
     */
    public function getAuthConfigs()
    {
        return $this->authConfigs;
    }
}
