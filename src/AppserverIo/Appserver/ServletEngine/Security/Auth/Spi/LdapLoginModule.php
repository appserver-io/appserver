<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\Auth\Spi\LdapLoginModule.php
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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Collections\HashMap;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Psr\Security\Auth\Subject;
use AppserverIo\Psr\Security\Auth\Login\LoginException;
use AppserverIo\Psr\Security\Auth\Login\FailedLoginException;
use AppserverIo\Psr\Security\Auth\Callback\CallbackHandlerInterface;
use AppserverIo\Appserver\ServletEngine\Security\SecurityException;
use AppserverIo\Appserver\ServletEngine\Security\Utils\Util;
use AppserverIo\Appserver\ServletEngine\Security\Utils\ParamKeys;
use AppserverIo\Appserver\ServletEngine\Security\Utils\SharedStateKeys;
use AppserverIo\Appserver\ServletEngine\RequestHandler;
use AppserverIo\Appserver\ServletEngine\Security\SimpleGroup;

/**
 * This class provides LDAP login functionality to an openldap server.
 *
 * @author    Alexandros Weigl <a.weigl@techdivision.com>
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LdapLoginmodule extends UsernamePasswordLoginModule
{

    /**
     * The LDAP url of the LDAP server
     *
     * @var string
     */
    protected $ldapUrl = null;

    /**
     * The LDAP port of the LDAP server
     *
     * @var string
     */
    protected $ldapPort = 389;

    /**
     * The LDAP start tls flag. Enables/disables tls requests to the LDAP server
     *
     * @var boolean
     */
    protected $ldapStartTls = null;

    /**
     * The LDAP servers base distinguished name
     *
     * @var string
     */
    protected $baseDN = null;

    /**
     * The administrator user DN with the permissions to search the LDAP directory.
     *
     * @var string
     */
    protected $bindDN = null;

    /**
     * The credential of the administrator user
     *
     * @var string
     */
    protected $bindCredential = null;

    /**
     * A search filter used to locate the context of the user to authenticate
     * The input username/userDN as obtained from the login module
     * callback will be substituted into the filter anywhere a "{0}" expression is seen.
     *  A common example search filter is "(uid={0})".
     *
     * @var string
     */
    protected $baseFilter = null;

    /**
     * The fixed DN of the context to search for user roles.
     *
     * @var string
     */
    protected $rolesDN = null;

    /**
     * A search filter used to locate the roles associated with the authenticated user.
     * The input username/userDN as obtained from the login module callback
     * will be substituted into the filter anywhere a "{0}" expression is
     * seen. The authenticated userDN will be substituted into the filter anywhere a
     * "{1}" is seen.  An example search filter that matches on the input username is:
     * "(memberUid={0})". An alternative that matches on the authenticated userDN is:
     * "(member={1})".
     *
     * @var string
     */
    protected $roleFilter = null;

    /**
     * Allow Anonymous Logins to OpenLDAP
     *
     * @var boolean
     */
    protected $allowEmptyPasswords = null;

    /**
     * A Hashmap storing the authenticating users roles
     *
     * @var mixed
     */
    protected $setsMap = null;

    /**
     * The username of the user
     *
     * @var string
     */
    protected $username = null;

    /**
     * The distinguished name of the user
     *
     * @var string
     */
    protected $userDN = null;

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

        // initialize the hash encoding to use
        if ($params->exists(ParamKeys::URL)) {
            $this->ldapUrl = $params->get(ParamKeys::URL);
        }
        if ($params->exists(ParamKeys::PORT)) {
            $this->ldapPort = $params->get(ParamKeys::PORT);
        }
        if ($params->exists(ParamKeys::BASE_DN)) {
            $this->baseDN = $params->get(ParamKeys::BASE_DN);
        }
        if ($params->exists(ParamKeys::BIND_DN)) {
            $this->bindDN= $params->get(ParamKeys::BIND_DN);
        }
        if ($params->exists(ParamKeys::BIND_CREDENTIAL)) {
            $this->bindCredential = $params->get(ParamKeys::BIND_CREDENTIAL);
        }
        if ($params->exists(ParamKeys::BASE_FILTER)) {
            $this->baseFilter = $params->get(ParamKeys::BASE_FILTER);
        }
        if ($params->exists(ParamKeys::ROLES_DN)) {
            $this->rolesDN = $params->get(ParamKeys::ROLES_DN);
        }
        if ($params->exists(ParamKeys::ROLE_FILTER)) {
            $this->roleFilter = $params->get(ParamKeys::ROLE_FILTER);
        }
        if ($params->exists(ParamKeys::START_TLS)) {
            $this->ldapStartTls = $params->get(ParamKeys::START_TLS);
        }
        if ($params->exists(ParamKeys::ALLOW_EMPTY_PASSWORDS)) {
            $this->allowEmptyPasswords = $params->get(ParamKeys::ALLOW_EMPTY_PASSWORDS);
        }
        $this->setsMap = new HashMap();
    }

    /**
     * Perform the authentication of username and password through LDAP.
     *
     * @return boolean TRUE when login has been successfull, else FALSE
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if an error during login occured
     */
    public function login()
    {
        $this->loginOk = false;

        // array containing the username and password from the user's input
        list ($this->username, $password) = $this->getUsernameAndPassword();

        if ($this->username === null && $password === null) {
            $this->identity = $this->unauthenticatedIdentity;
        }

        if ($this->identity === null) {
            try {
                $this->identity = $this->createIdentity($this->username);
            } catch (\Exception $e) {
                throw new LoginException(sprintf('Failed to create principal: %s', $e->getMessage()));
            }
        }

        $ldapConnection = $this->ldapConnect();
        if ($ldapConnection) {
            // Replace the placeholder  with the actual username of the user
            $this->baseFilter = preg_replace('/\{0\}/', "$this->username", $this->baseFilter);

            var_dump($this->username);
            $search = ldap_search($ldapConnection, $this->baseDN, $this->baseFilter);
            $entry = ldap_first_entry($ldapConnection, $search);
            $this->userDN = ldap_get_dn($ldapConnection, $entry);

            if (!(isset($this->userDN))) {
                throw new LoginException(sprintf('User not found in LDAP directory'));
            }
        } else {
            throw new LoginException(sprintf('Couldn\'t connect to LDAP server'));
        }

        // bind the authenticating user to the LDAP directory
        $bind = ldap_bind($ldapConnection, $this->userDN, $password);
        if ($bind === false) {
            throw new LoginException(sprintf('Username or password wrong'));
        }

        // query whether or not password stacking has been activated
        if ($this->getUseFirstPass()) {
            // add the username and password to the shared state map
            $this->sharedState->add(SharedStateKeys::LOGIN_NAME, $this->username);
            $this->sharedState->add(SharedStateKeys::LOGIN_PASSWORD, $this->credential);
        }

        $this->loginOk = true;
        return true;
    }

    /**
     * Returns the password for the user from the sharedMap data.
     *
     * @return void
     */
    public function getUsersPassword()
    {
        return null;
    }

    /**
     * Overridden by subclasses to return the Groups that correspond to the to the
     * role sets assigned to the user. Subclasses should create at least a Group
     * named "Roles" that contains the roles assigned to the user.
     *
     * @return array Array containing the sets of roles
     * @throws \AppserverIo\Psr\Security\Auth\Login\LoginException Is thrown if password can't be loaded
     */
    protected function getRoleSets()
    {
        // search and add the roles of the current user
        $this->rolesSearch($this->username, $this->userDN);
        return $this->setsMap->toArray();
    }

    /**
     * Adds a role to the setsMap
     *
     * @param string $groupName The name of the group
     * @param string $name      The name of the role to be added to the group
     * @return void
     */
    protected function addRole($groupName, $name)
    {
        if ($this->setsMap->exists($groupName) === false) {
            $group = new SimpleGroup(new String($groupName));
            $this->setsMap->add($groupName, $group);
        } else {
            $group = $this->setsMap->get($groupName);
        }
        try {
            $group->addMember($this->createIdentity(new String($name)));
        } catch (\Exception $e) {
            $application
                ->getNamingDirectory()
                ->search(NamingDirectoryKeys::SYSTEM_LOGGER)
                ->error($e->__toString());
        }
    }

    /**
     * Extracts the common name from a Distinguished name
     *
     * @param string $dn The distinguished name of the authenticating user
     * @return array
     *
     */
    protected function extractCNFromDN($dn)
    {
        $splitArray = explode(',', $dn);
        $keyValue = array();
        foreach ($splitArray as $value) {
            $tempArray  = explode('=', $value);
            $keyValue[$tempArray[0]] = array();
            $keyValue[$tempArray[0]][] = $tempArray[1];
        }

        return $keyValue['cn'];
    }

    /**
    * return's the authenticated user identity.
    *
    * @return \appserverio\psr\security\principalinterface the user identity
    */
    protected function getIdentity()
    {
        return $this->identity;
    }

    /**
     * Creates a new connection to the ldap server, binds to the ldap server and returns the connection
     *
     * @return resource|false
     */
    protected function ldapConnect()
    {

        $ldapConnection = ldap_connect($this->ldapUrl, $this->ldapPort);

        if ($ldapConnection) {
            if ($this->ldapStartTls === 'true') {
                ldap_start_tls($ldapConnection);
            }
            ldap_set_option($ldapConnection, LDAP_OPT_PROTOCOL_VERSION, 3);

            //anonymous login
            if ($this->allowEmptyPasswords === 'true') {
                $bind = ldap_bind($ldapConnection);
            } else {
                $bind = ldap_bind($ldapConnection, $this->bindDN, $this->bindCredential);
            }
            if (!$bind) {
                throw new LoginException('Bind to server failed');
            }
        } else {
            return false;
        }
        return $ldapConnection;
    }

    /**
     * Search the authenticated user for his user groups/roles
     * The found roles are then added to the setsMap hashmap
     *
     * @param string $user   the authenticated user
     * @param string $userDN the DN of the authenticated user
     * @return void
     */
    protected function rolesSearch($user, $userDN)
    {
        if ($this->rolesDN === null || $this->roleFilter === null) {
            return;
        }

        $groupName = Util::DEFAULT_GROUP_NAME;
        $ldapConnection = $this->ldapConnect();

        // replace the {0} placeholder with the username of the user
        $this->roleFilter = preg_replace("/\{0\}/", "$user", $this->roleFilter);
        // replace the {1} placeholder with the distiniguished name of the user
        $this->roleFilter = preg_replace("/\{1\}/", "$userDN", $this->roleFilter);

        // search for the roles using the roleFilter and get the first entry
        $search = ldap_search($ldapConnection, $this->rolesDN, $this->roleFilter);
        $entry = ldap_first_entry($ldapConnection, $search);

        do {
            // get the distinguished name of the entry and extract the common names out of it
            $dn = ldap_get_dn($ldapConnection, $entry);
            $roleArray = $this->extractCNFromDN($dn);
            // add every returned CN to the roles
            foreach ($roleArray as $role) {
                $this->addRole($groupName, $role);
            }
            // continue as long as there are entries still left from the search
        } while ($entry = ldap_next_entry($ldapConnection, $entry));
    }
}
