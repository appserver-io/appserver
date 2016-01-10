<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AuthConstraintNode
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
 * DTO to transfer a auth constraint.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AuthConstraintNode extends AbstractNode implements AuthConstraintNodeInterface
{

    /**
     * The role name information.
     *
     * @var array
     * @AS\Mapping(nodeName="role-name", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\RoleNameNode")
     */
    protected $roleNames;

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
     * @return array The role names information
     */
    public function getRoleNames()
    {
        return $this->roleNames;
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