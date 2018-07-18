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
     * The display name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthConfigNode
     * @DI\Mapping(nodeName="authConfig", nodeType="AppserverIo\Appserver\Core\Api\Node\AuthConfigNode")
     */
    protected $authConfig;

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
     * Return's the authentication configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthConfigNode The authentication configuration
     */
    public function getAuthConfig()
    {
        return $this->authConfig;
    }
}
