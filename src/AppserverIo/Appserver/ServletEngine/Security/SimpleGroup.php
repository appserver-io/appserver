<?php

/**
 * AppserverIo\Appserver\ServletEngine\Security\SimpleGroup
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
use AppserverIo\Collections\HashMap;
use AppserverIo\Psr\Security\Acl\GroupInterface;
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
class SimpleGroup extends SimplePrincipal implements GroupInterface
{

    /**
     * The group members.
     *
     * @var \AppserverIo\Collections\HashMap
     */
    private $members;

    /**
     * Initialize the principal with the passed name.
     *
     * @param \AppserverIo\Lang\String $name The principal's name
     */
    public function __construct(String $name)
    {

        // invoke the parent constructor
        parent::__construct($name);

        // initialize the members
        $this->members = new HashMap();
    }

    /**
     * Adds the passed principal to the group.
     *
     * @param \AppserverIo\Psr\Security\PrincipalInterface $pricipal The principal to add
     *
     * @return boolean TRUE if the member was successfully added, FALSE if the principal was already a member
     */
    public function addMember(PrincipalInterface $pricipal)
    {

        // query whether or not the passed prinicpal is already a member
        $isMember = $this->members->exists($pricipal->getName());

        // if the principal is not a member, add it
        if ($isMember === false) {
            $this->members->add($pricipal->getName(), $pricipal);
        }

        // return if the principal has successfully been added
        return $isMember === false;
    }

    /**
     * Removes the passed principal from the group.
     *
     * @param \AppserverIo\Psr\Security\PrincipalInterface $pricipal The principal to remove
     *
     * @return boolean TRUE if the member was successfully removed, FALSE if the principal was not a member
     */
    public function removeMember(PrincipalInterface $principal)
    {

        // query whether or not the passed principal is a member of this group
        if ($this->members->exists($principal->getName())) {
            // remove the princial and return TRUE
            $this->members->remove($principal->getName());
            return true;
        }

        // return FALSE, if not
        return false;
    }

    /**
     * Returns TRUE if the passed principal is a member of the group.
     * This method does a recursive search, so if a principal belongs
     * to a group which is a member of this group, true is returned.
     *
     * A special check is made to see if the member is an instance of
     * AnybodyPrincipal or NobodyPrincipal since these classes do not
     * hash to meaningful values.
     *
     * @param \AppserverIo\Psr\Security\PrincipalInterface $pricipal The principal to query membership for
     *
     * @return boolean TRUE if the principal is a member of this group, FALSE otherwise
     */
    public function isMember(PrincipalInterface $principal)
    {

        // first see if there is a key with the member name
        $isMember = $this->members->exists($principal->getName());

        if ($isMember === false) {
            // check the AnybodyPrincipal & NobodyPrincipal special cases
            $isMember = ($principal instanceof AnybodyPrincipal);
            if ($isMember === false ) {
                if ($principal instanceof NobodyPrincipal )
                    return false;
            }
        }

        if ($isMember === false) {
            // check any groups for membership
            foreach ($this->members as $group) {
                if ($group instanceof GroupInterface) {
                    $isMember = $group->isMember($principal);
                }
            }
        }

        return $isMember;
    }

    /**
     * Return's the group's members.
     *
     * @return \AppserverIo\Collections\HashMap The group members
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * Returns the principals name as string.
     *
     * @return string The principal's name
     */
    public function __toString()
    {
        return $this->name->__toString();
    }
}
