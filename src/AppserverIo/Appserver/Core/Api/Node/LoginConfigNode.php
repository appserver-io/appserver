<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\LoginConfigNode
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

/**
 * DTO to transfer a login configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoginConfigNode extends AbstractNode implements LoginConfigNodeInterface
{

    /**
     * The authentication method information, one of Basic, Digest, Form.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthMethodNode
     * @DI\Mapping(nodeName="auth-method", nodeType="AppserverIo\Appserver\Core\Api\Node\AuthMethodNode")
     */
    protected $authMethod;

    /**
     * The realm name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\RealmNameNode
     * @DI\Mapping(nodeName="realm-name", nodeType="AppserverIo\Appserver\Core\Api\Node\RealmNameNode")
     */
    protected $realmName;

    /**
     * The flag to query whether or not it is the default authenticator.
     *
     * @var \AppserverIo\Configuration\Interfaces\NodeValueInterface
     * @DI\Mapping(nodeName="default-authenticator", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $defaultAuthenticator;

    /**
     * The login form configuration information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FormLoginConfigNode
     * @DI\Mapping(nodeName="form-login-config", nodeType="AppserverIo\Appserver\Core\Api\Node\FormLoginConfigNode")
     */
    protected $formLoginConfig;

    /**
     * Return's the authentication method information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthMethodNode The authentication method information
     */
    public function getAuthMethod()
    {
        return $this->authMethod;
    }

    /**
     * Return's the realm name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RealmNameNode The realm name information
     */
    public function getRealmName()
    {
        return $this->realmName;
    }

    /**
     * Return's the login form configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormLoginConfigNode The login form configuration information
     */
    public function getFormLoginConfig()
    {
        return $this->formLoginConfig;
    }

    /**
     * Return's the flag to query whether or not this is the default authenticator.
     *
     * @return \AppserverIo\Configuration\Interfaces\NodeValueInterface The flag
     */
    public function getDefaultAuthenticator()
    {
        return $this->defaultAuthenticator;
    }
}
