<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\UsernamePasswordLoginModule
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
use AppserverIo\Lang\Boolean;
use AppserverIo\Collections\HashMap;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Psr\Security\SecurityException;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Login\FailedLoginException;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\Security\Utils\Util;
use AppserverIo\Appserver\ServletEngine\Security\Utils\ParamKeys;
use AppserverIo\Appserver\ServletEngine\Security\Utils\SharedStateKeys;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class UsernamePasswordLoginModule extends AbstractLoginModule
{

    /**
     * The authenticated login identity.
     *
     * @var \AppserverIo\Psr\Security\PrincipalInterface
     */
    protected $identity;

    /**
     * The unauthentacted login identity.
     *
     * @var \AppserverIo\Psr\Security\PrincipalInterface
     */
    protected $unauthenticatedIdentity;

    /**
     * The message digest algorithm used to hash passwords. If null then plain passwords will be used.
     *
     * @var string
     */
    protected $hashAlgorithm = null;

   /**
    * The name of the charset/encoding to use when converting the password String to a byte array. Default is the platform's default encoding.
    *
    * @var string
    */
    protected $hashCharset = null;

    /**
     * The string encoding format to use. Defaults to base64.
     *
     * @var string
     */
    protected $hashEncoding = null;

    /**
     * A flag indicating if the password comparison should ignore case
     *
     * @var boolean
     */
    protected $ignorePasswordCase;

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * lookupName:      The datasource name used to lookup in the naming directory
     * rolesQuery:      The database query used to load the user's roles
     * principalsQuery: The database query used to load the user
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

        // call the parent method
        parent::initialize($subject, $callbackHandler, $sharedState, $params);

        // check to see if password hashing has been enabled, if an algorithm is set, check for a format and charset
        $this->hashAlgorithm = new String($params->get(ParamKeys::HASH_ALGORITHM));

        // query whether or not a hash algorithm has been specified
        if ($this->hashAlgorithm != null) {
            // initialize the hash encoding to use
            if ($params->exists(ParamKeys::HASH_ENCODING)) {
                $this->hashEncoding = new String($params->get(ParamKeys::HASH_ENCODING));
            } else {
                $this->hashEncoding = new String(Util::BASE64_ENCODING);
            }
            // initialize the hash charset if specified
            if ($params->exists(ParamKeys::HASH_CHARSET)) {
                $this->hashCharset = new String($params->get(ParamKeys::HASH_CHARSET));
            }
        }

        // query whether or not we should ignor case when comparing passwords
        if ($params->exists(ParamKeys::IGNORE_PASSWORD_CASE)) {
            $flag = new String($params->get(ParamKeys::IGNORE_PASSWORD_CASE));
            $this->ignorePasswordCase = Boolean::valueOf($flag)->booleanValue();
        } else {
            $this->ignorePasswordCase = false;
        }
    }

    /**
     * Returns the password for the user from the sharedMap data.
     *
     * @return \AppserverIo\Lang\String The user's password
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    abstract protected function getUsersPassword();

    /**
     * Perform the authentication of username and password.
     *
     * @return boolean TRUE when login has been successfull, else FALSE
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if an error during login occured
     */
    public function login()
    {

        // invoke the parent method
        if (parent::login()) {
            // if login has been successfully, setup our view of the user
            $name = new String($this->sharedState->get(SharedStateKeys::LOGIN_NAME));
            // query whether or not we alredy hava a principal
            if ($name instanceof PrincipalInterface) {
                $this->identity = name;
            } else {
                $name = $name->__toString();
                try {
                    $this->identity = $this->createIdentity($name);
                } catch (\Exception $e) {
                    throw new LoginException(sprintf('Failed to create principal: %s', $e->getMessage()));
                }
            }

            // return immediately
            return true;
        }

        // else, reset the login flag
        $this->loginOk = false;

        // array containing the username and password from the user's input
        list ($name, $password) = $this->getUsernameAndPassword();

        if ($name == null && $password == null) {
            $this->identity = $this->unauthenticatedIdentity;
        }

        if ($this->identity == null) {
            try {
                $this->identity = $this->createIdentity($name);
            } catch (\Exception $e) {
                throw new LoginException(sprintf('Failed to create principal: %s', $e->getMessage()));
            }

            // hash the user entered password if password hashing is in use
            if ($this->hashAlgorithm != null) {
                $password = $this->createPasswordHash($name, $password);
                // validate the password supplied by the subclass
                $expectedPassword = $this->getUsersPassword();
            }

            // validate the password
            if ($this->validatePassword($password, $expectedPassword) === false) {
                throw new FailedLoginException('Password Incorrect/Password Required');
            }
        }

        // query whether or not password stacking has been activated
        if ($this->getUseFirstPass()) {
            // add the username and password to the shared state map
            $this->sharedState->add(SharedStateKeys::LOGIN_NAME, $name);
            $this->sharedState->add(SharedStateKeys::LOGIN_PASSWORD, $password);
        }

        $this->loginOk = true;
        return true;
    }

    /**
     * If hashing is enabled, this method is called from login() prior to password validation.
     *
     * Subclasses may override it to provide customized password hashing, for example by adding
     * user-specific information or salting.
     *
     * The default version calculates the hash based on the following options:
     *
     * hashAlgorithm: The digest algorithm to use.
     * hashEncoding: The format used to store the hashes (base64 or hex)
     * hashCharset: The encoding used to convert the password to bytes
     *
     * for hashing.
     *
     * digestCallback: The class name of the digest callback implementation that includes
     * pre/post digest content like salts.
     *
     * It will return null if the hash fails for any reason, which will in turn
     * cause validatePassword() to fail.
     *
     * @param \AppserverIo\Lang\String $name     Ignored in default version
     * @param \AppserverIo\Lang\String $password The password string to be hashed
     *
     * @return \AppserverIo\Lang\String The hashed password
     * @throws \AppserverIo\Psr\Security\SecurityException Is thrown if there is a failure to load the digestCallback
     */
    protected function createPasswordHash(String $name, String $password)
    {

        // initialize the callback
        $callback = null;

        // query whether or not we've a callback configured
        if ($this->params->exists(ParamKeys::DIGEST_CALLBACK)) {
            try {
                // load the callback class name and create a new callback instance
                $callbackClassName = $this->params->get(ParamKeys::DIGEST_CALLBACK);
                $callback = new $callbackClassName();

                // initialize the callback
                $tmp = new HashMap($this->params->toIndexedArray());
                $tmp->add(SharedStateKeys::LOGIN_NAME, $name);
                $tmp->add(SharedStateKeys::LOGIN_PASSWORD, $password);
                $callback->init($tmp);

            } catch (\Exception $e) {
                throw new SecurityException("Failed to load DigestCallback");
            }
        }

        // hash and return the password
        return Util::createPasswordHash($this->hashAlgorithm, $this->hashEncoding, $this->hashCharset, $name, $password, $callback);
    }

    /**
     * A hook that allows subclasses to change the validation of the input
     * password against the expected password. This version checks that
     * neither inputPassword or expectedPassword are null that that
     * inputPassword.equals(expectedPassword) is true;
     *
     * @param \AppserverIo\Lang\String $inputPassword    The specified password
     * @param \AppserverIo\Lang\String $expectedPassword The expected password
     *
     * @return boolean TRUE if the inputPassword is valid, FALSE otherwise
     */
    protected function validatePassword(String $inputPassword, String $expectedPassword)
    {

        // if username or password is NULL, return immediately
        if ($inputPassword == null || $expectedPassword == null) {
            return false;
        }

        // initialize the valid login flag
        $valid = false;

        // query whether or not we've to ignore the case
        if ($this->ignorePasswordCase === true) {
            $valid = $inputPassword->equalsIgnoreCase($expectedPassword);
        } else {
            $valid = $inputPassword->equals($expectedPassword);
        }

        // return the flag
        return $valid;
    }

    /**
     * Return's the authenticated user identity.
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface The user identity
     */
    protected function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Return's the principal's username.
     *
     * @return \AppserverIo\Lang\String The username
     */
    protected function getUsername()
    {

        // initialize the name
        $name = null;

        // query whether or not we've an principal
        if ($identity = $this->getIdentity()) {
            $name = $identity->getName();
        }

        // return the name
        return $name;
    }
}
