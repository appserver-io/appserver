<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\LoginModulesNodeTrait
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
 * Trait to handle login modules nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait LoginModulesNodeTrait
{

    /**
     * The login modules configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="loginModules/loginModule", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\LoginModuleNode")
     */
    protected $loginModules = array();

    /**
     * Sets the login modules configuration.
     *
     * @param array $loginModules The login modules configuration
     *
     * @return void
     */
    public function setLoginModules($loginModules)
    {
        $this->loginModules = $loginModules;
    }

    /**
     * Returns the login modules configuration.
     *
     * @return array The login modules configuration
     */
    public function getLoginModules()
    {
        return $this->loginModules;
    }
}
