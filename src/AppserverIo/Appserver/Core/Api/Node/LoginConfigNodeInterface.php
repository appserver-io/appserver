<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\LoginConfigNodeInterface
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

use AppserverIo\Psr\Auth\LoginConfigurationInterface;
use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a login configuration DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface LoginConfigNodeInterface extends NodeInterface, LoginConfigurationInterface
{

    /**
     * Return's the authentication method information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\AuthMethodNode The authentication method information
     */
    public function getAuthMethod();

    /**
     * Return's the realm name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\RealmNameNode The realm name information
     */
    public function getRealmName();

    /**
     * Return's the login form configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\FormLoginConfigNode The login form configuration information
     */
    public function getFormLoginConfig();
}
