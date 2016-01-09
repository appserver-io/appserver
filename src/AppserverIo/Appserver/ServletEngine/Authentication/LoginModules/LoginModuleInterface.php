<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginModuleInterface
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\LoginModules;

use AppserverIo\Collections\MapInterface;

/**
 * Interface for all login module implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface LoginModuleInterface
{

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * password-stacking:       If this is set to "useFirstPass", the login identity will be taken from the
     *                          servlet_engine.authentication.login_module.login_name value of the sharedState map, and the proof of
     *                          identity from the servlet_engine.authentication.login_module.login_password value of the sharedState map
     * principalClass:          A Principal implementation that support a constructor taking a string argument for the princpal name
     * unauthenticatedIdentity: The name of the principal to asssign and authenticate when a null username and password are seen
     *
     * @param \AppserverIo\Collections\MapInterface $sharedState A Map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface $params      The parameters passed to the login module
     */
    public function initialize(MapInterface $sharedState, MapInterface $params);

    /**
     * Perform the authentication of username and password.
     *
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if an error during login occured
     */
    public function login();

    /**
     * Performs the user logout.
     *
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if an error during logout occured
     */
    public function logout();

    /**
     * Called by login() to acquire the username and password strings for
     * authentication. This method does no validation of either.
     *
     * @return array Array with username and password, e. g. array(0 => $username, 1 => $password)
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if username and password can't be loaded
     */
    public function getUsernameAndPassword();
}
