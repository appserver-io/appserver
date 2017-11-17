<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\GenericPrincipal
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

namespace AppserverIo\Appserver\ServletEngine\Security;

use AppserverIo\Lang\String;
use AppserverIo\Collections\ArrayList;
use AppserverIo\Psr\Security\PrincipalInterface;

/**
 * A simple String based implementation of Principal. Typically a SimplePrincipal is
 * created given a userID which is used as the Principal name.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class GenericPrincipal extends SimplePrincipal
{

    /**
     * The principal's username.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $username;

    /**
     * The principal's password.
     *
     * @var \AppserverIo\Lang\String
     */
    protected $password;

    /**
     * The principal's roles.
     *
     * @var \AppserverIo\Collection\ArrayList
     */
    protected $roles;

    /**
     * The user principal instance that will be returned from the request.
     *
     * @var \AppserverIo\Psr\Security\PrincipalInterface
     */
    protected $userPrincipal;

    /**
     * Initializes the principal with the data from the passed objects.
     *
     * @param \AppserverIo\Lang\String                                   $username      The principal's username
     * @param \AppserverIo\Lang\String                                   $password      The principal's password
     * @param \AppserverIo\Collection\ArrayList                          $roles         The principal's roles
     * @param \AppserverIo\Psr\Security\PrincipalInterface               $userPrincipal The user principal instance that will be returned from the request
     */
    public function __construct(
        String $username = null,
        String $password = null,
        ArrayList $roles = null,
        PrincipalInterface $userPrincipal = null
    ) {

        // set the passed instances
        $this->username = $username;
        $this->password = $password;
        $this->userPrincipal = $userPrincipal;

        // set the roles or initialize an empty ArrayList
        if ($roles == null) {
            $this->roles = new ArrayList();
        } else {
            $this->roles = $roles;
        }
    }

    /**
     * Return's the principal's username.
     *
     * @return the \AppserverIo\Lang\String The username
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Return's the principal's password.
     *
     * @return the \AppserverIo\Lang\String The password
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Return's the principal's roles.
     *
     * @return the \AppserverIo\Collection\ArrayList The roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Return's the user principal instance that will be returned from the request.
     *
     * @return the \AppserverIo\Psr\Security\PrincipalInterface The user principal
     */
    public function getUserPrincipal()
    {
        return $this->userPrincipal;
    }
}
