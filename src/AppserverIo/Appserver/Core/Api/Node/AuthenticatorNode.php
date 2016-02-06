<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AuthenticatorNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer authenticator information.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AuthenticatorNode extends AbstractNode implements AuthenticatorNodeInterface
{

    /**
     * The authenticator name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The authenticator type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * Return's the authenticator name.
     *
     * @return string The authenticator name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Return's the authenticator type.
     *
     * @return string The authenticator type
     */
    public function getType()
    {
        return $this->type;
    }
}
