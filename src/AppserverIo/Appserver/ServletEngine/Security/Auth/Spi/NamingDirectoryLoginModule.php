<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\NamingDirectoryLoginModule
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

namespace AppserverIo\Appserver\ServletEngine\Security\Auth\Spi;

use AppserverIo\Lang\String;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\ServletEngine\Security\Utils\ParamKeys;

/**
 * Login module that uses the naming directory to load a user and his roles from.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class NamingDirectoryLoginModule extends UsernamePasswordLoginModule
{

    /**
     * The naming directory name prefix used to load the user's roles.
     *
     * @var string
     */
    protected $rolesPathPrefix;

    /**
     * The naming directroy name prefix used to load the user.
     *
     * @var string
     */
    protected $userPathPrefix;

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * rolesPathPrefix: The naming directory prefix used to load the user's roles
     * userPathPrefix:  The naming directory prefix used to load the user
     *
     * @param \AppserverIo\Psr\Security\Auth\Subject                           $subject         The Subject to update after a successful login
     * @param \AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface $callbackHandler The callback handler that will be used to obtain the user identity and credentials
     * @param \AppserverIo\Collections\MapInterface                            $sharedState     A map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface                            $params          The parameters passed to the login module
     */
    public function initialize(Subject $subject, CallbackHandlerInterface $callbackHandler, MapInterface $sharedState, MapInterface $params)
    {

        // call the parent method
        parent::initialize($subject, $callbackHandler, $sharedState, $params);

        // load the parameters from the map
        $this->userPathPrefix = $params->get(ParamKeys::USER_PATH_PREFIX);
        $this->rolesPathPrefix = $params->get(ParamKeys::ROLES_PATH_PREFIX);
    }

    /**
     * Returns the password for the user from the naming directory.
     *
     * @return \AppserverIo\Lang\String The user's password
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getUsersPassword()
    {

        try {
            // load the application context
            $application = RequestHandler::getApplicationContext();

            // load and return the user's password or throw an exception
            return new String($application->search(sprintf('%s/%s', $this->userPathPrefix, $this->getUsername())));

        } catch (\Exception $e) {
            throw new LoginException('No matching username found in naming directory');
        }
    }

    /**
     * Get the roles the current user belongs to by querying the
     * rolesPathPrefix + '/' + super.getUsername() JNDI location.
     *
     * @return array The roles the user is assigned to
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getRoleSets()
    {

        try {

            return array();

        } catch(NamingException $ne) {

            // log.error("Failed to obtain groups for user="+super.getUsername(), e);

            throw new LoginException($ne->__toString());

        }
    }

    /**
     * Performs the user logout.
     *
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if an error during logout occured
     */
    public function logout()
    {
        // @TODO Still to implement
    }
}
