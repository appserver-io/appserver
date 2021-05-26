<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNode
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

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Appserver\ServletEngine\Security\Realm;

/**
 * DTO to transfer a security domain configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityDomainNode extends AbstractNode implements SecurityDomainNodeInterface
{

    /**
     * The security domain name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The manager class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The managers factory class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The display name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthConfigNode
     * @DI\Mapping(nodeName="authConfig", nodeType="AppserverIo\Appserver\Core\Api\Node\AuthConfigNode")
     */
    protected $authConfig;

    /**
     * Initializes the configuration with the passed values.
     *
     * @param string $name    The realm name
     * @param string $type    The realm type to use
     * @param string $factory The factory that has to be used to create the realm instance
     */
    public function __construct($name = '', $type = '', $factory = '')
    {
        $this->type = $type ? $type : Realm::class;
        $this->name = $name;
        $this->factory = $factory;
    }

    /**
     * Return's the security domain name.
     *
     * @return string The security domain name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Return's the authentication configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthConfigNode The authentication configuration
     */
    public function getAuthConfig()
    {
        return $this->authConfig;
    }
}
