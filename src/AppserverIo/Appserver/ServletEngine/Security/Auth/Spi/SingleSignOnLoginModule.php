<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\SingleSignOnLoginModule
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Alexandros Weigl <a.weigl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Security\Auth\Spi;

use GuzzleHttp\Client;
use AppserverIo\Lang\String;
use AppserverIo\Collections\HashMap;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Login\FailedLoginException;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Psr\Security\Auth\Callback\AuthorizationCodeCallback;
use AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\UsernamePasswordLoginModule;
use AppserverIo\Appserver\ServletEngine\Security\SimpleGroup;
use AppserverIo\Appserver\ServletEngine\Security\Utils\Util;
use AppserverIo\Appserver\ServletEngine\Security\Utils\ParamKeys;
use AppserverIo\Appserver\ServletEngine\Security\Utils\SharedStateKeys;

/**
 * This value will check if the actual request needs authentication.
 *
 * @author    Alexandros Weigl <a.weigl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SingleSignOnLoginModule extends UsernamePasswordLoginModule
{

    /**
     * The URL for the identity provider.
     *
     * @var string
     */
    protected $identityUrl = null;

    /**
     * The URL path to load the user information from.
     *
     * @var string
     */
    protected $userInfoPath = '/userinfo';

    /**
     * The URL path to login with.
     *
     * @var string
     */
    protected $oauthTokenPath = '/oauth/token';

    /**
     * The used token type
     *
     * @var string
     */
    protected $tokenTypeKey = 'token_type';

    /**
     * The key to load the access token from the login return value
     *
     * @var string
     */
    protected $accessTokenKey = 'access_token';

    /**
     * The key to load the ID token from the login return value
     *
     * @var string
     */
    protected $idTokenKey = 'id_token';

    /**
     * The key to load the refresh token from the login return value
     *
     * @var string
     */
    protected $refreshTokenKey = 'refresh_token';

    /**
     * The time in seconds when the token expires
     *
     * @var string
     */
    protected $expiresInKey = 'expires_in';

    /**
     * A Hashmap storing the authenticating users roles
     *
     * @var mixed
     */
    protected $setsMap = null;

    /**
     * The user information.
     *
     * @var \stdClass
     */
    protected $userInfo = null;

    /**
     * The users access token.
     *
     * @var string
     */
    protected $accessToken;

    /**
     * The users ID token.
     *
     * @var string
     */
    protected $idToken;

    /**
     * The users refresh token.
     *
     * @var string
     */
    protected $refreshToken;

    /**
     * Initialize the login module. This stores the subject, callbackHandler and sharedState and options
     * for the login session. Subclasses should override if they need to process their own options. A call
     * to parent::initialize() must be made in the case of an override.
     *
     * The following parameters can by default be passed from the configuration.
     *
     * ldapUrl:             The LDAP server to connect
     * ldapPort:            The port which the LDAP server is running on
     * baseDN:              The LDAP servers base distinguished name
     * bindDN:              The administrator user DN with the permissions to search the LDAP directory.
     * bindCredential:      The credential of the administrator user
     * baseFilter:          A search filter used to locate the context of the user to authenticate
     * rolesDN:             The fixed DN of the context to search for user roles.
     * rolFilter:           A search filter used to locate the roles associated with the authenticated user.
     * ldapStartTls:        The LDAP start tls flag. Enables/disables tls requests to the LDAP server
     * allowEmptyPasswords: Allow/disallow anonymous Logins to OpenLDAP
     *
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

        // initialize the URL of the identity provider to use
        if ($params->exists(ParamKeys::IDENTITY_URL)) {
            $this->identityUrl = $params->get(ParamKeys::IDENTITY_URL);
        }

        // initialize the path to load the user information from
        if ($params->exists(ParamKeys::USER_INFO_PATH)) {
            $this->userInfoPath = $params->get(ParamKeys::USER_INFO_PATH);
        }

        // initialize the path to load the Oauth token from
        if ($params->exists(ParamKeys::OAUTH_TOKEN_PATH)) {
            $this->oauthTokenPath = $params->get(ParamKeys::OAUTH_TOKEN_PATH);
        }

        // initialize the key to load the access token from the login return value
        if ($params->exists(ParamKeys::ACCESS_TOKEN_KEY)) {
            $this->accessTokenKey = $params->get(ParamKeys::ACCESS_TOKEN_KEY);
        }

        // initialize the key to load the ID token from the login return value
        if ($params->exists(ParamKeys::ID_TOKEN_KEY)) {
            $this->idTokenKey = $params->get(ParamKeys::ID_TOKEN_KEY);
        }

        // initialize the key to load the refresh token from the login return value
        if ($params->exists(ParamKeys::REFRESH_TOKEN_KEY)) {
            $this->refreshTokenKey = $params->get(ParamKeys::REFRESH_TOKEN_KEY);
        }

        // initialize the key to load the token type from the login return value
        if ($params->exists(ParamKeys::TOKEN_TYPE_KEY)) {
            $this->tokenTypeKey = $params->get(ParamKeys::TOKEN_TYPE_KEY);
        }

        // initialize the key to load the expiration time from the login return value
        if ($params->exists(ParamKeys::EXPIRES_IN_KEY)) {
            $this->expiresInKey = $params->get(ParamKeys::EXPIRES_IN_KEY);
        }

        // initialialize the hash map for the roles
        $this->setsMap = new HashMap();

        // initialize the user info
        $this->userInfo = new \stdClass();
        $this->userInfo->roles = array();
    }

    /**
     * Perform the authentication of username and password.
     *
     * @return boolean TRUE when login has been successfull, else FALSE
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if an error during login occured
     */
    public function login()
    {

        // else, reset the login flag
        $this->loginOk = false;

        // array containing the username, password and authorization code from the user's input
        list ($name, $password, $authorizationCode) = $this->getUsernameAndPasswordAndAuthorizationCode();

        // if either a username AND a password has been specified
        // we have an unauthenticated user principal (anonymous)
        if ($name == null && $password == null) {
            $this->identity = $this->unauthenticatedIdentity;
        }

        // query whether or not we've a principal
        if ($this->identity == null) {
            // initialize the HTTP client to login with
            $client = new Client(array('base_uri' => $this->identityUrl));
            // intialize form values and invoke the login request
            $tokenResponse = $client->request(
                Protocol::METHOD_POST,
                $this->oauthTokenPath,
                [
                    'form_params' => [
                        'client_id'     => $name->stringValue(),
                        'client_secret' => $password->stringValue(),
                        'code'          => $authorizationCode->stringValue()
                    ]
                ]
            );

            // query whether or not we've been successfull
            if ($tokenResponse->getStatusCode() !== 200) {
                throw new FailedLoginException('Password Incorrect/Password Required');
            }

            try {
                // decode the response body
                $token = json_decode($tokenResponse->getBody(), true);

                // initialize the members with the token data
                $this->idToken = $token[$this->idTokenKey] ?: null;
                $this->accessToken = $token[$this->accessTokenKey] ?: null;
                $this->refreshToken = $token[$this->refreshTokenKey] ?: null;

                // initialize the user principal
                $this->identity = $this->createIdentity($name);

            } catch (\Exception $e) {
                throw new LoginException(sprintf('Failed to create principal: %s', $e->getMessage()));
            }
        }

        // query whether or not password stacking has been activated
        if ($this->getUseFirstPass()) {
            // add the username and password to the shared state map
            $this->sharedState->add(SharedStateKeys::LOGIN_NAME, $name);
            $this->sharedState->add(SharedStateKeys::LOGIN_PASSWORD, $password);
        }

        // mark login successfull
        $this->loginOk = true;
        return true;
    }

    /**
     * Returns the password for the user from the sharedMap data.
     *
     * @return \AppserverIo\Lang\String The user's password
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getUsersPassword()
    {
    }

    /**
     * Utility method to create a Principal for the given username. This
     * creates an instance of the principalClassName type if this option was
     * specified. If principalClassName was not specified, a SimplePrincipal
     * is created.
     *
     * @param \AppserverIo\Lang\String $name The name of the principal
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface The principal instance
     * @throws \Exception Is thrown if the custom principal type cannot be created
     */
    public function createIdentity(String $name)
    {

        // initialize the user principal
        $principal = parent::createIdentity($name);

        // extract the tokens from the response JSON
        $principal->setIdToken($this->idToken);
        $principal->setAccessToken($this->accessToken);
        $principal->setRefreshToken($this->refreshToken);

        // initialize the HTTP client to load the user information with
        $client = new Client(array('base_uri' => $this->identityUrl));
        $userInfoResponse = $client->request(
            Protocol::METHOD_GET,
            $this->userInfoPath,
            [
                'headers' => [
                    Protocol::HEADER_AUTHORIZATION => sprintf('Bearer %s', $this->accessToken)
                ]
            ]
        );

        // extract the user information from the request body
        $this->userInfo = json_decode($userInfoResponse->getBody());

        // load the object vars from the user information
        $objectVars = get_object_vars($this->userInfo);

        // append the scalar values as members in the principal
        foreach ($objectVars as $name => $value) {
            if (is_scalar($value)) {
                call_user_func(array($principal, sprintf('set%s', ucfirst($name))), $value);
            }
        }

        // return the user principal
        return $principal;
    }

    /**
     * Execute the rolesQuery against the lookupName to obtain the roles for the authenticated user.
     *
     * @return array Array containing the sets of roles
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getRoleSets()
    {

        // load the default group name and the LDAP connection
        $groupName = Util::DEFAULT_GROUP_NAME;

        // initialize the roles
        $roles = array();
        foreach ($this->userInfo->roles as $role) {
            $this->addRole($groupName, $role);
        }

        // return's the map with the roles
        return $this->setsMap->toArray();
    }

    /**
     * Adds a role to the hash map with the roles.
     *
     * @param string $groupName The name of the group
     * @param string $name      The name of the role to be added to the group
     *
     * @return void
     */
    protected function addRole($groupName, $name)
    {

        // query whether or not, the group already exists
        if ($this->setsMap->exists($groupName) === false) {
            $group = new SimpleGroup(new String($groupName));
            $this->setsMap->add($groupName, $group);
        } else {
            $group = $this->setsMap->get($groupName);
        }

        try {
            // finally add the identity to the group
            $group->addMember(parent::createIdentity(new String($name)));
        } catch (\Exception $e) {
            \error($e);
        }
    }

    /**
     * Called by login() to acquire the username, password and authorization code
     * strings for authentication. This method does no validation of either.
     *
     * @return array Array with username, password and authorization code, e. g. array(0 => $name, 1 => $password, 2 => $authorizationCode)
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if name and password can't be loaded
     */
    public function getUsernameAndPasswordAndAuthorizationCode()
    {

        // create and initialize an ArrayList for the callback handlers
        $list = new ArrayList();
        $list->add($authorizationCodeCallback = new AuthorizationCodeCallback());

        // handle the callbacks
        $this->callbackHandler->handle($list);

        // return an array with username, password and authorization code
        return array_merge($this->getUsernameAndPassword(), array($authorizationCodeCallback->getAuthorizationCode()));
    }
}
