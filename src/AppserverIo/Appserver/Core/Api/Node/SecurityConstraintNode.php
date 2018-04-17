<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityConstraintNode
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

use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Configuration\Interfaces\NodeValueInterface;

/**
 * DTO to transfer a security constraint.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SecurityConstraintNode extends AbstractNode implements SecurityConstraintNodeInterface
{

    /**
     * The display name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode
     * @AS\Mapping(nodeName="display-name", nodeType="AppserverIo\Appserver\Core\Api\Node\DisplayNameNode")
     */
    protected $displayName;

    /**
     * The web resource collection information.
     *
     * @var array
     * @AS\Mapping(nodeName="web-resource-collection", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\WebResourceCollectionNode")
     */
    protected $webResourceCollections;

    /**
     * The auth constraint information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AuthConstraintNode
     * @AS\Mapping(nodeName="auth-constraint", nodeType="AppserverIo\Appserver\Core\Api\Node\AuthConstraintNode")
     */
    protected $authConstraint;

    /**
     * Initializes the node with the passed values.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeValueInterface         $displayName            The display name information
     * @param array                                                            $webResourceCollections The array with the web resource collection information
     * @param \AppserverIo\Appserver\Core\Api\Node\AuthConstraintNodeInterface $authConstraint         The auth constraint information
     */
    public function __construct(
        NodeValueInterface $displayName = null,
        array $webResourceCollections = array(),
        AuthConstraintNodeInterface $authConstraint = null
    ) {
        $this->displayName = $displayName;
        $this->authConstraint = $authConstraint;
        $this->webResourceCollections = $webResourceCollections;
    }

    /**
     * Return's the display name of the security constraint.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Return's the web resource collections information.
     *
     * @return array The web resource collections information
     */
    public function getWebResourceCollections()
    {
        return $this->webResourceCollections;
    }

    /**
     * Return's the auth constraint information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthConstraintNode The auth constraint information
     */
    public function getAuthConstraint()
    {
        return $this->authConstraint;
    }
}
