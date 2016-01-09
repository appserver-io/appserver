<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\UsernamePasswordLoginModule
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

use Doctrine\DBAL\DriverManager;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\Doctrine\Utils\ConnectionUtil;
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\ParamKeys;
use AppserverIo\Appserver\ServletEngine\Authentication\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\SharedStateKeys;
use AppserverIo\Lang\Boolean;

/**
 * This valve will check if the actual request needs authentication.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class UsernamePasswordLoginModule extends AbstractLoginModule
{

    /** The login identity */
    private Principal identity;
    /** The proof of login identity */
    private char[] credential;
    /** the message digest algorithm used to hash passwords. If null then
     plain passwords will be used. */
    private String hashAlgorithm = null;
   /** the name of the charset/encoding to use when converting the password
    String to a byte array. Default is the platform's default encoding.
    */
    private String hashCharset = null;
    /** the string encoding format to use. Defaults to base64. */
    private String hashEncoding = null;
    /** A flag indicating if the password comparison should ignore case */
    private boolean ignorePasswordCase;

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
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\Callback\CallbackHandlerInterface $callbackHandler The callback handler that will be used to obtain the user identity and credentials
     * @param \AppserverIo\Collections\MapInterface                                                 $sharedState     A map shared between all configured login module instances
     * @param \AppserverIo\Collections\MapInterface                                                 $params          The parameters passed to the login module
     */
    public function initialize(CallbackHandlerInterface $callbackHandler, MapInterface $sharedState, MapInterface $params)
    {

        // call the parent method
        parent::initialize($callbackHandler, $sharedState, $params);

        // Check to see if password hashing has been enabled.
        // If an algorithm is set, check for a format and charset.
        $hashAlgorithm = new String($params->get(ParamKeys::HASH_ALGORITHM));

        if ($hashAlgorithm != null ) {
            $hashEncoding = new String($params->get(ParamKeys::HASH_ENCODING));

            if ($hashEncoding == null) {
                $hashEncoding = Util::BASE64_ENCODING;
                $hashCharset = new String($params->get(ParamKeys::HASH_CHARSET));

                /* if (log.isTraceEnabled()) {
                    log.trace("Password hashing activated: algorithm = " + hashAlgorithm
                        + ", encoding = " + hashEncoding
                        + ", charset = " + (hashCharset == null ? "{default}" : hashCharset)
                        + ", callback = " + options.get("digestCallback")
                    );
                }*/
            }
        }

        $flag = new String($params->get(ParamKeys::IGNORE_PASSWORD_CASE));
        $this->ignorePasswordCase = Boolean::valueOf($flag)->booleanValue();
    }

    /**
     * Returns the password for the user from the sharedMap data.
     *
     * @return array Array with username and password, e. g. array(0 => $username, 1 => $password)
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if password can't be loaded
     */
    abstract public function getUsersPassword();

    /**
     * Perform the authentication of username and password.
     *
     * @throws \AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\LoginException Is thrown if an error during login occured
     */
    public function login()
    {

        if (parent::login()) {

            // Setup our view of the user
            $username = new String($this->sharedState->get(SharedStateKeys::LOGIN_NAME));

            if ($username instanceof Principal) {
                $identity = username;
            } else {
                $name = $username->__toString();
                try {
                    $identity = $this->createIdentity($name);
                } catch(\Exception $e) {
                    // log.debug("Failed to create principal", e);
                    throw new LoginException(sprintf('Failed to create principal: %s', $e->getMessage()));
                }
            }

            $password = new String($this->sharedState->get(SharedStateKeys::LOGIN_PASSWORD));

            /* if ($password instanceof char[] ) {
                credential = (char[]) password;
            } elseif (password != null) {
                String tmp = password.toString();
                credential = tmp.toCharArray();
            } */

            return true;
        }

        $this->loginOk = false;

        // array containing the username and password from the user's input
        $list ($username, $password) = extract($this->getUsernameAndPassword());

        if ($username == null && $password == null) {
            $this->identity = $this->unauthenticatedIdentity;
            // super.log.trace("Authenticating as unauthenticatedIdentity="+identity);
        }

        if ($this->identity == null) {
            try {
                $this->identity = $this->createIdentity($username);

            } catch(\Exception $e) {
                // log.debug("Failed to create principal", e);
                throw new LoginException(sprintf('Failed to create principal: %s', $e->getMessage()));
            }

            // hash the user entered password if password hashing is in use
            if ($this->hashAlgorithm != null)
                $password = $this->createPasswordHash($username, $password);
                // validate the password supplied by the subclass
                $expectedPassword = $this->getUsersPassword();

                if ($this->validatePassword($password, $expectedPassword) === false) {
                    // super.log.debug("Bad password for username="+username);
                    throw new FailedLoginException('Password Incorrect/Password Required');
                }
        }

        if ($this->getUseFirstPass()) {
            // add the username and password to the shared state map
            $this->sharedState->add(SharedStateKeys::LOGIN_NAME, $username);
            $this->sharedState->add(SharedStateKeys::LOGIN_PASSWORD, $credential);
        }

        $this->loginOk = true;
        // super.log.trace("User '" + identity + "' authenticated, loginOk="+loginOk);
        return true;
    }

    /**
     * If hashing is enabled, this method is called from <code>login()</code>
     * prior to password validation.
     * <p>
     * Subclasses may override it to provide customized password hashing,
     * for example by adding user-specific information or salting.
     * <p>
     * The default version calculates the hash based on the following options:
     * <ul>
     * <li><em>hashAlgorithm</em>: The digest algorithm to use.
     * <li><em>hashEncoding</em>: The format used to store the hashes (base64 or hex)
     * <li><em>hashCharset</em>: The encoding used to convert the password to bytes
     * for hashing.
     * <li><em>digestCallback</em>: The class name of the
     * org.jboss.security.auth.spi.DigestCallback implementation that includes
     * pre/post digest content like salts.
     * </ul>
     * It will return null if the hash fails for any reason, which will in turn
     * cause <code>validatePassword()</code> to fail.
     *
     * @param username ignored in default version
     * @param password the password string to be hashed
     * @throws SecurityException - thrown if there is a failure to load the digestCallback
     */
    protected function createPasswordHash($username, $password)
    {

        $callback = null;

        $callbackClassName = $this->params->get("digestCallback");

        if ($callbackClassName != null) {
            try {
                $callback = new $callbackClassName();
                /* if (log.isTraceEnabled()) {
                    log.trace("Created DigestCallback: "+callback);
                } */

            } catch (\Exception $e) {
                /* if (log.isTraceEnabled()) {
                    log.trace("Failed to load DigestCallback", e);
                } */

                throw new SecurityException("Failed to load DigestCallback");
            }

            $tmp = new HashMap($this->params->toIndexedArray());
            $tmp->add(SharedStateKeys::LOGIN_NAME, $username);
            $tmp->add(SharedStateKeys::LOGIN_PASSWORD, $password);
            $callback->init($tmp);
        }

        return Util::createPasswordHash($hashAlgorithm, $hashEncoding, $hashCharset, $username, $password, $callback);
    }

    /**
     * A hook that allows subclasses to change the validation of the input
     * password against the expected password. This version checks that
     * neither inputPassword or expectedPassword are null that that
     * inputPassword.equals(expectedPassword) is true;
     *
     * @param \AppserverIo\Lang\String $inputPassword
     * @param \AppserverIo\Lang\String $expectedPassword
     *
     * @return boolean TRUE if the inputPassword is valid, FALSE otherwise
     */
    protected function validatePassword($inputPassword, $expectedPassword)
    {

       if ($inputPassword == null || $expectedPassword == null) {
           return false;
       }

       $valid = false;

       if ($this->ignorePasswordCase == true) {
           $valid = $inputPassword->equalsIgnoreCase($expectedPassword);
       } else {
           $valid = $inputPassword->equals($expectedPassword);
       }

       return $valid;
    }

    /**
     * @return Principal The user identity
     */
    protected function getIdentity()
    {
        return $this->identity;
    }

    protected function getUnauthenticatedIdentity()
    {
        return $this->unauthenticatedIdentity;
    }

    protected function getUsername()
    {
        $username = null;
        if ($identity $this->getIdentity()) {
            $username = $identity->getUsername();
        }
        return $username;
    }

    protected function getCredentials()
    {
        return $this->credential;
    }
}
