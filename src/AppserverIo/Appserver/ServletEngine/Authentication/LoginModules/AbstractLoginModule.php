<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\AbstractLoginModule
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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\SimplePrincipal;
use AppserverIo\Appserver\ServletEngine\Authentication\Callback\NameCallback;
use AppserverIo\Appserver\ServletEngine\Authentication\Callback\PasswordCallback;
use AppserverIo\Appserver\ServletEngine\Authentication\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\ParamKeys;
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\SharedStateKeys;

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
     * The callback handler to obtain username and password.
     *
     * @var AppserverIo\Appserver\ServletEngine\Authentication\Callback\CallbackHandlerInterface
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
     * @var \AppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface
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
     * password-stacking:       If this is set to "useFirstPass", the login identity will be taken from the
     *                          appserver.security.auth.login.name value of the sharedState map, and the proof
     *                          of identity from the appserver.security.auth.login.password value of the sharedState map
     * principalClass:          A Principal implementation that support a constructor taking a string argument for the princpal name
     * unauthenticatedIdentity: The name of the principal to asssign and authenticate when a null username and password are seen
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\Callback\CallbackHandlerInterface $callbackHandler The callback handler that will be used to obtain the user identity and credentials
     * @param \AppserverIo\Collections\MapInterface                                                 $sharedState     A map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface                                                 $params          The parameters passed to the login module
     */
    public function initialize(CallbackHandlerInterface $callbackHandler, MapInterface $sharedState, MapInterface $params)
    {

        // initialize the passed parameters
        $this->params = $params;
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
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if an error during login occured
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
     * Called by login() to acquire the username and password strings for
     * authentication. This method does no validation of either.
     *
     * @return array Array with name and password, e. g. array(0 => $name, 1 => $password)
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if name and password can't be loaded
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
            $reflectionClass = new ReflectionClass($this->principalClassName);
            $principal = $reflectionClass->newInstanceArgs(array($name));
        }

        // return the principal instance
        return $principal;
    }
}
