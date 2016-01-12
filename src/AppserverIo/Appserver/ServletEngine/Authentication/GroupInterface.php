<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\GroupInterface
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

namespace AppserverIo\Appserver\ServletEngine\Authentication;

/**
 * Interface for all group implementations.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface GroupInterface
{

    /**
     * Adds the passed principal to the group.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface $pricipal The principal to add
     *
     * @return boolean TRUE if the member was successfully added, FALSE if the principal was already a member
     */
    public function addMember(PrincipalInterface $pricipal);

    /**
     * Removes the passed principal from the group.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface $pricipal The principal to remove
     *
     * @return boolean TRUE if the member was successfully removed, FALSE if the principal was not a member
     */
    public function removeMember(PrincipalInterface $principal);

    /**
     * Returns TRUE if the passed principal is a member of the group.
     * This method does a recursive search, so if a principal belongs
     * to a group which is a member of this group, true is returned.
     *
     * A special check is made to see if the member is an instance of
     * AnybodyPrincipal or NobodyPrincipal since these classes do not
     * hash to meaningful values.
     *
     * @param \AppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface $pricipal The principal to query membership for
     *
     * @return boolean TRUE if the principal is a member of this group, FALSE otherwise
     */
    public function isMember(PrincipalInterface $principal);

    /**
     * Returns the principals name as string.
     *
     * @return string The principal's name
     */
    public function __toString();
}
