<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface
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

use AppserverIo\Configuration\Interfaces\NodeInterface;
use AppserverIo\Psr\Application\ManagerConfigurationInterface;

/**
 * Interface for the manager configuration node.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ManagerNodeInterface extends ManagerConfigurationInterface, NodeInterface
{

    /**
     * Sets the security domain configuration.
     *
     * @param array $securityDomains The security domain configuration
     *
     * @return void
     */
    public function setSecurityDomains($securityDomains);

    /**
     * Returns the security domain configuration.
     *
     * @return array The security domain configuration
     */
    public function getSecurityDomains();

    /**
     * Sets the authenticator configuration.
     *
     * @param array $authenticators The authenticator configuration
     *
     * @return void
     */
    public function setAuthenticators($authenticators);

    /**
     * Returns the authenticator configuration.
     *
     * @return array The authenticator configuration
     */
    public function getAuthenticators();

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType();

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory();

    /**
     * Returns the context factory class name.
     *
     * @return string The context factory class name
     */
    public function getContextFactory();

    /**
     * Returns the manager's object description configuration.
     *
     * @return array|\AppserverIo\Appserver\Core\Api\Node\ObjectDescriptionNode The object description configuration
     */
    public function getObjectDescription();

    /**
     * Array with the handler params to use.
     *
     * @return array
     */
    public function getParams();

    /**
     * Array with the handler params to use.
     *
     * @param array $params The handler params
     *
     * @return void
     */
    public function setParams(array $params);

    /**
     * Sets the param with the passed name, type and value.
     *
     * @param string $name  The param name
     * @param string $type  The param type
     * @param mixed  $value The param value
     *
     * @return void
     */
    public function setParam($name, $type, $value);

    /**
     * Returns the param with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the param to be returned
     *
     * @return mixed The requested param casted to the specified type
     */
    public function getParam($name);

    /**
     * Returns the params casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted params
     */
    public function getParamsAsArray();
}
