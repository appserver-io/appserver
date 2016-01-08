<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AuthNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a auth constraint.
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */
class AuthNode extends AbstractNode implements AuthNodeInterface
{

    /**
     * The authentication type information, one of Basic or Digest.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthTypeNode
     * @AS\Mapping(nodeName="auth-type", nodeType="AppserverIo\Appserver\Core\Api\Node\AuthTypeNode")
     */
    protected $authType;

    /**
     * The realm information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\RealmNode
     * @AS\Mapping(nodeName="realm", nodeType="AppserverIo\Appserver\Core\Api\Node\RealmNode")
     */
    protected $realm;

    /**
     * The authentication adapter type information, one of htpasswd or htdigest.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AdapterTypeNode
     * @AS\Mapping(nodeName="adapter-type", nodeType="AppserverIo\Appserver\Core\Api\Node\AdapterTypeNode")
     */
    protected $adapterType;

    /**
     * The authentication adapter type options, e. g. the filename.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\OptionsNode
     * @AS\Mapping(nodeName="options", nodeType="AppserverIo\Appserver\Core\Api\Node\OptionsNode")
     */
    protected $options;

    /**
     * Return's the authentication type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthTypeNode The authentication type information
     */
    public function getAuthType()
    {
        return $this->authType;
    }

    /**
     * Return's the realm information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RealmNode The realm information
     */
    public function getRealm()
    {
        return $this->realm;
    }

    /**
     * Return's the authentication adapter type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AdapterTypeNode The authentication adapter type information
     */
    public function getAdapterType()
    {
        return $this->adapterType;
    }

    /**
     * Return's the authentication adapter type options information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\OptionsNode The authentication adapter type options information
     */
    public function getOptions()
    {
        return $this->options;
    }
}
