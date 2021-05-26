<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\BearerLoginModule
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
use AppserverIo\Collections\MapInterface;
use AppserverIo\Psr\HttpMessage\Protocol;
use AppserverIo\Psr\Security\PrincipalInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Login\FailedLoginException;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
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
class BearerLoginModule extends AbstractLoginModule
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
     * The URL path to validate the access token with.
     *
     * @var string
     */
    protected $validatePath = '/validate';

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

        // initialize the path used to validate the access token with
        if ($params->exists(ParamKeys::VALIDATE_PATH)) {
            $this->validatePath = $params->get(ParamKeys::VALIDATE_PATH);
        }

        // initialize the path to load the user information from
        if ($params->exists(ParamKeys::USER_INFO_PATH)) {
            $this->userInfoPath = $params->get(ParamKeys::USER_INFO_PATH);
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

        // array containing the username (which IS the bearer token)
        list ($name, ) = $this->getUsernameAndPassword();

        // if NO username has been specified we have an
        // unauthenticated user principal (anonymous)
        if ($name == null) {
            $this->identity = $this->unauthenticatedIdentity;
        }

        if ($this->identity == null) {
            // initialize the HTTP client to login with
            $client = new Client(array('base_uri' => $this->identityUrl));
            // intialize the body and invoke the validation request
            $tokenResponse = $client->request(
                Protocol::METHOD_POST,
                $this->validatePath,
                [
                    'body' => $name
                ]
            );

            // query whether or not we've been successfull
            if ($tokenResponse->getStatusCode() !== 200) {
                throw new FailedLoginException('Invalid bearer token');
            }

            try {
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
        }

        // mark login successfull
        $this->loginOk = true;
        return true;
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

        // append the name as access token
        $principal->setAccessToken($name);

        // initialize the HTTP client to load the user information with
        $client = new Client(array('base_uri' => $this->identityUrl));
        $userInfoResponse = $client->request(
            Protocol::METHOD_GET,
            $this->userInfoPath,
            array(
                'headers' => array(
                    Protocol::HEADER_AUTHORIZATION => sprintf('Bearer %s', $name->stringValue())
                )
            )
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
     * Return's the authenticated user identity.
     *
     * @return \AppserverIo\Psr\Security\PrincipalInterface The user identity
     */
    protected function getIdentity()
    {
        return $this->identity;
    }
}
