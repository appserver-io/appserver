<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SecurityConstraintNodeInterface
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

/**
 * Interface for a security constraint DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SecurityConstraintNodeInterface extends NodeInterface
{

    /**
     * Return's the display name of the security constraint.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The display name
     */
    public function getDisplayName();

    /**
     * Return's the web resource collections information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\WebResourceCollectionNode The web resource collections information
     */
    public function getWebResourceCollections();

    /**
     * Return's the auth constraint information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthConstraintNode The auth constraint information
     */
    public function getAuthConstraint();

    /**
     * Return's the realm name the security constraint is bound to.
     *
     * @return \AppserverIo\Configuration\Interfaces\NodeValueInterface The realm name
     */
    public function getRealmName();
}
