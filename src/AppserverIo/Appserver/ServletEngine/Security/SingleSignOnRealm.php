<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\SingleSignOnRealm
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
 * @copyright 2021 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Lang\String;
use AppserverIo\Appserver\ServletEngine\Security\Auth\Callback\SingleSignOnAssociationHandler;

/**
 * SSO security domain implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2021 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SingleSignOnRealm extends Realm
{

    /**
     * Finally tries to authenticate the user with the passed name.
     *
     * @param \AppserverIo\Lang\String $username          The name of the user to authenticate
     * @param \AppserverIo\Lang\String $password          The password used for authentication
     * @param \AppserverIo\Lang\String $authorizationCode The authorization code for the login attempt
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface|null The authenticated user principal
     */
    public function authenticate(String $username, String $password, String $authorizationCode = null)
    {

        // prepare the callback handler
        $callbackHandler = new SingleSignOnAssociationHandler(new SimplePrincipal($username), $password, $authorizationCode);

        // authenticate the passed username/password combination
        return $this->authenticateByUsernameAndCallbackHandler($username, $callbackHandler);
    }
}
