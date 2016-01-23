<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\AbstractLoginModule
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
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Collections\CollectionInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Acl\GroupInterface;
use AppserverIo\Psr\Security\Auth\Spi\LoginModuleInterface;
use AppserverIo\Psr\Security\Auth\Callback\NameCallback;
use AppserverIo\Psr\Security\Auth\Callback\PasswordCallback;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\Security\SimpleGroup;
use AppserverIo\Appserver\ServletEngine\Security\SimplePrincipal;
use AppserverIo\Appserver\ServletEngine\Security\Utils\ParamKeys;
use AppserverIo\Appserver\ServletEngine\Security\Utils\SharedStateKeys;
use AppserverIo\Psr\Security\Auth\Login\LoginException;

/**
 * An abstract login module implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractLoginModule implements LoginModuleInterface
{

    /**
     * The Subject to update after a successful login.
     *
     * @var \AppserverIo\Psr\Security\Auth\Subject
     */
    protected $subject;

    /**
     * The callback handler to obtain username and password.
     *
     * @var AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface
     */
    protected $callbackHandler;

    /**
     * Used the share the login state between multiple modules.
     *
     * @var \AppserverIo\Collections\MapInterface
     */
    protected $sharedState;

    /**
     * The login module parameters.
     *
     * @var \AppserverIo\Collections\MapInterface
     */
    protected $params;

    /**
     * Flag that the shared state credential should be used.
     *
     * @var boolean
     */
    protected $useFirstPass = false;

    /**
     * Flag indicating if the login phase succeeded. Subclasses that override the
     * login method must set this to true on successful completion of login.
     *
     * @var boolean
     */
    protected $loginOk = false;

    /**
     * The unauthenticated login identity.
     *
     * @var \AppserverIo\Psr\Security\PrincipalInterface
     */
    protected $unauthenticatedIdentity;

    /**
     * The class name used to create a principal.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $principalClassName;

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * passwordStacking:        If this is set to "useFirstPass", the login identity will be taken from the
     *                          appserver.security.auth.login.name value of the sharedState map, and the proof
     *                          of identity from the appserver.security.auth.login.password value of the sharedState map
     * principalClass:          A Principal implementation that support a constructor taking a string argument for the princpal name
     * unauthenticatedIdentity: The name of the principal to asssign and authenticate when a null username and password are seen
     *
     * @param \AppserverIo\Psr\Security\Auth\Subject                           $subject         The Subject to update after a successful login
     * @param \AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface $callbackHandler The callback handler that will be used to obtain the user identity and credentials
     * @param \AppserverIo\Collections\MapInterface                            $sharedState     A map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface                            $params          The parameters passed to the login module
     *
     * @return void
     */
    public function initialize(Subject $subject, CallbackHandlerInterface $callbackHandler, MapInterface $sharedState, MapInterface $params)
    {

        // initialize the passed parameters
        $this->params = $params;
        $this->subject = $subject;
        $this->sharedState = $sharedState;
        $this->callbackHandler = $callbackHandler;

        // query whether or not we have password stacking activated or not
        if ($params->exists(ParamKeys::PASSWORD_STACKING) && $params->get(ParamKeys::PASSWORD_STACKING) === 'useFirstPass') {
            $this->useFirstPass = true;
        }

        // check for a custom principal implementation
        if ($params->exists(ParamKeys::PRINCIPAL_CLASS)) {
            $this->principalClassName = new String($params->get(ParamKeys::PRINCIPAL_CLASS));
        }

        // check for unauthenticatedIdentity option.
        if ($params->exists(ParamKeys::UNAUTHENTICATED_IDENTITY)) {
            $this->unauthenticatedIdentity = $this->createIdentity($params->get(ParamKeys::UNAUTHENTICATED_IDENTITY));
        }
    }

    /**
     * Flag that the shared state credential should be used.
     *
     * @return boolean TRUE if the shared state credential should be used, else FALSE
     */
    public function getUseFirstPass()
    {
        return $this->useFirstPass;
    }

    /**
     * Looks for servlet_engine.authentication.login_module.login_name and servlet_engine.authentication.login_module.login_password
     * values in the sharedState map if the useFirstPass option was true and returns TRUE if they exist. If they do not or are NULL
     * this method returns FALSE.
     *
     * Note that subclasses that override the login method must set the loginOk var to TRUE if the login succeeds in order for the
     * commit phase to populate the Subject. This implementation sets loginOk to TRUE if the login() method returns TRUE, otherwise,
     * it sets loginOk to FALSE. Perform the authentication of username and password.
     *
     * @return boolean TRUE if the login credentials are available in the sharedMap, else FALSE
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if an error during login occured
     */
    public function login()
    {

        // initialize the login state
        $this->loginOk = false;

        // query whether or not we should use the shared state
        if ($this->useFirstPass) {
            $name = $this->sharedState->get(SharedStateKeys::LOGIN_NAME);
            $password = $this->sharedState->get(SharedStateKeys::LOGIN_PASSWORD);

            // if we've a username and a password login has been successful
            if ($name && $password) {
                $this->loginOk = true;
                return true;
            }
        }

        // return FALSE if login has not been successful
        return false;
    }

    /**
     * Remove the user identity and roles added to the Subject during commit.
     *
     * @return boolean Always TRUE
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if an error during login occured
     */
    public function logout()
    {

        // load the user identity and the subject's principals
        $identity = $this->getIdentity();
        $principals = $this->subject->getPrincipals();

        // remove the user identity from the subject
        foreach ($principals as $key => $principal) {
            if ($identity->equals($principal)) {
                $principals->remove($key);
            }
        }

        // return TRUE on success
        return true;
    }

    /**
     * Method to commit the authentication process (phase 2). If the login
     * method completed successfully as indicated by loginOk == true, this
     * method adds the getIdentity() value to the subject getPrincipals() Set.
     * It also adds the members of each Group returned by getRoleSets()
     * to the subject getPrincipals() Set.
     *
     * @see javax.security.auth.Subject;
     * @see java.security.acl.Group;
     * @return true always.
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException If login can't be committed'
     */
    public function commit()
    {

        // we can only commit if the login has been successful
        if ($this->loginOk === false) {
            return false;
        }

        // add the identity to the subject's principals
        $principals = $this->subject->getPrincipals();
        $principals->add($this->getIdentity());

        // load the groups
        $roleSets = $this->getRoleSets();

        // iterate over the groups and add them to the subject
        for ($g = 0; $g < sizeof($roleSets); $g++) {
            // initialize group, name and subject group
            $group = $roleSets[$g];
            $name = $group->getName();
            $subjectGroup = $this->createGroup($name, $principals);

            /* if ($subjectGroup instanceof NestableGroup) {
                // a NestableGroup only allows Groups to be added to it so we need to add a SimpleGroup to subjectRoles to contain the roles
                $tmp = new SimpleGroup('Roles');
                $subjectGroup->addMember($tmp);
                $subjectGroup = $tmp;
            } */

            // copy the group members to the Subject group
            foreach ($group->getMembers() as $member) {
                $subjectGroup->addMember($member);
            }
        }

        // return TRUE if we succeed
        return true;
    }

    /**
     * Method to abort the authentication process (phase 2).
     *
     * @return boolean Alaways TRUE
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if abort has not been successfully
     */
    public function abort()
    {
        return true;
    }

    /**
     * Called by login() to acquire the username and password strings for
     * authentication. This method does no validation of either.
     *
     * @return array Array with name and password, e. g. array(0 => $name, 1 => $password)
     * @throws \AppserverIo\Appserver\Psr\Security\Auth\Login\LoginException Is thrown if name and password can't be loaded
     */
    public function getUsernameAndPassword()
    {

        // create and initialize an ArrayList for the callback handlers
        $list = new ArrayList();
        $list->add($nameCallback = new NameCallback());
        $list->add($passwordCallback = new PasswordCallback());

        // handle the callbacks
        $this->callbackHandler->handle($list);

        // return an array with the username and callback
        return array($nameCallback->getName(), $passwordCallback->getPassword());
    }

    /**
     * Utility method to create a Principal for the given username. This
     * creates an instance of the principalClassName type if this option was
     * specified. If principalClassName was not specified, a SimplePrincipal
     * is created.
     *
     * @param \AppserverIo\Lang\String $name The name of the principal
     *
     * @return Principal The principal instance
     * @throws \Exception Is thrown if the custom principal type cannot be created
     */
    public function createIdentity(String $name)
    {

        //initialize the principal
        $principal = null;

        // query whether or not a principal class name has been specified
        if ($this->principalClassName == null) {
            $principal = new SimplePrincipal($name);
        } else {
            $reflectionClass = new ReflectionClass($this->principalClassName->__toString());
            $principal = $reflectionClass->newInstanceArgs(array($name));
        }

        // return the principal instance
        return $principal;
    }

    /**
     * Find or create a Group with the given name. Subclasses should use this
     * method to locate the 'Roles' group or create additional types of groups.
     *
     * @param \AppserverIo\Lang\String                     $name       The name of the group to create
     * @param \AppserverIo\Collections\CollectionInterface $principals The list of principals
     *
     * @return \AppserverIo\Psr\Security\Acl\GroupInterface A named group from the principals set
     */
    protected function createGroup(String $name, CollectionInterface $principals)
    {

        // initialize the group
        /** \AppserverIo\Psr\Security\Acl\GroupInterface $roles */
        $roles = null;

        // iterate over the passed principals
        foreach ($principals as $principal) {
            // query whether we found a group or not, proceed if not
            if (($principal instanceof GroupInterface) == false) {
                continue;
            }

            // the principal is a group
            $grp = $principal;

            // if the group already exists, stop searching
            if ($grp->getName()->equals($name)) {
                $roles = $grp;
                break;
            }
        }

        // if we did not find a group create one
        if ($roles == null) {
            $roles = new SimpleGroup($name);
            $principals->add($roles);
        }

        // return the group
        return $roles;
    }

    /**
     * Return's the unauthenticated identity.
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface The identity instance
     */
    public function getUnauthenticatedIdentity()
    {
        return $this->unauthenticatedIdentity;
    }

    /**
     * Overriden by subclasses to return the Principal that corresponds to
     * the user primary identity.
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface The user identity
     */
    abstract protected function getIdentity();

    /**
     * Overriden by subclasses to return the Groups that correspond to the
     * to the role sets assigned to the user. Subclasses should create at
     * least a Group named "Roles" that contains the roles assigned to the user.
     * A second common group is "CallerPrincipal" that provides the application
     * identity of the user rather than the security domain identity.
     *
     * @return array Array containing the sets of roles
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    abstract protected function getRoleSets();
}
