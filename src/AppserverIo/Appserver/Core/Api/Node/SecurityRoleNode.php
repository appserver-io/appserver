<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityRoleNode
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

/**
 * DTO to transfer a security constraint.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityRoleNode extends AbstractNode implements SecurityRoleNodeInterface
{

    /**
     * The role name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\RoleNameNode
     * @AS\Mapping(nodeName="web-resource-name", nodeType="AppserverIo\Appserver\Core\Api\Node\RoleNameNode")
     */
    protected $roleName;

    /**
     * The description information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * Return's the role name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RoleNameNode The web resource name information
     */
    public function getRoleName()
    {
        return $this->roleName;
    }

    /**
     * Return's the description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description information
     */
    public function getDescription()
    {
        return $this->description;
    }
}
