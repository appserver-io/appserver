<?php

/**
 * AppserverIo\Ldap\Symfony\LdapClient
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
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Ldap\Symfony;

use Symfony\Component\Ldap\LdapInterface;
use AppserverIo\Ldap\LdapClientInterface;
use Symfony\Component\Ldap\Entry;

/**
 * Symfony based LDAP client implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2019 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LdapClient implements LdapClientInterface, LdapInterface
{

    /**
     * The Symfony LDAP client instance.
     *
     * @var \Symfony\Component\Ldap\LdapClientInterface
     */
    protected $ldapClient;

    /**
     * Initializes the Entity Manager with the Symfony LDAP client instance.
     *
     * @param \Symfony\Component\Ldap\LdapInterface $ldapClient The LDAP client instance
     */
    public function __construct(LdapInterface $ldapClient)
    {
        $this->ldapClient = $ldapClient;
    }

    /**
     * Return a connection bound to the ldap.
     *
     * @param string $dn       A LDAP dn
     * @param string $password A password
     *
     * @return void
     * @throws \Symfony\Component\Ldap\Exception\ConnectionException if dn / password could not be bound
     */
    public function bind($dn = null, $password = null)
    {
        return $this->ldapClient->bind($dn, $password);
    }

    /**
     * Queries a ldap server for entries matching the given criteria.
     *
     * @param string $dn      The DN to query
     * @param string $query   The query itself
     * @param array  $options The query options
     *
     * @return \Symfony\Component\Ldap\Adapter\CollectionInterface|\Symfony\Component\Ldap\Entry[] The query result
     * @throws \Symfony\Component\Ldap\Exception\ConnectionException
     */
    public function query($dn, $query, array $options = array())
    {
        return $this->ldapClient->query($dn, $query, $options);
    }

    /**
     * @return \Symfony\Component\Ldap\Adapter\EntryManagerInterface
     */
    public function getEntryManager()
    {
        return $this->ldapClient->getEntryManager();
    }

    /**
     * Escape a string for use in an LDAP filter or DN.
     *
     * @param string $subject The subject to escape
     * @param string $ignore  Do not ignore
     * @param int    $flags   The flags
     *
     * @return string The escaped string
     */
    public function escape($subject, $ignore = '', $flags = 0)
    {
        return $this->ldapClient->escape($subject, $ignore, $flags);
    }
}
