<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\AuthNodeInterface
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

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a security DTO implementation.
 *
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @deprecated Since 1.2.0
 */
interface AuthNodeInterface extends NodeInterface
{

    /**
     * Return's the authentication type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthTypeNode The authentication type information
     */
    public function getAuthType();

    /**
     * Return's the realm information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RealmNode The realm information
     */
    public function getRealm();

    /**
     * Return's the authentication adapter type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AdapterTypeNode The authentication adapter type information
     */
    public function getAdapterType();

    /**
     * Return's the authentication adapter type options information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\OptionsNode The authentication adapter type options information
     */
    public function getOptions();
}
