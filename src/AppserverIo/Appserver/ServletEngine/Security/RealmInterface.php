<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\RealmInterface
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Lang\String;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;

/**
 * Interface for a realm implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface RealmInterface
{

    /**
     * Return's the name of the realm.
     *
     * @return string The realm's name
     */
    public function getName();

    /**
     * Return's the realm's configuration.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SecurityDomainNodeInterface The realm's configuration
     */
    public function getConfiguration();

    /**
     * Tries to authenticate the user with the passed username and password.
     *
     * @param \AppserverIo\Lang\String $username The name of the user to authenticate
     * @param \AppserverIo\Lang\String $password The password used for authentication
     *
     * @return \AppserverIo\Security\PrincipalInterface|null The authenticated user principal
     */
    public function authenticate(String $username, String $password);

    /**
     * Tries to authenticate the user with the passed username and callback handler.
     *
     * @param \AppserverIo\Lang\String                                         $username        The name of the user to authenticate
     * @param \AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface $callbackHandler The callback handler used to load the credentials
     *
     * @return \AppserverIo\Security\PrincipalInterface|null The authenticated user principal
     */
    public function authenticateByUsernameAndCallbackHandler(String $username, CallbackHandlerInterface $callbackHandler);
}
