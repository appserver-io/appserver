<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\AnybodyPrincipal
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

use AppserverIo\Lang\String;

/**
 * An implementation of Principal and Comparable that represents any role.
 * Any Principal or name of a Principal when compared to an AnybodyPrincipal
 * using {@link #equals(PrincipleInterface) equals} will always be found
 * equals to the AnybodyPrincipal.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AnybodyPrincipal implements PrincipalInterface
{

    /**
     * The principal name.
     *
     * @var \AppserverIo\Lang\String
     */
    const ANYBODY = '<ANYBODY>';

    /**
     * Compare this AnybodyPrincipal's name against another Principal.
     *
     * @param PrincipalInterface $another The other principal to compare to
     *
     * @return boolean Will always return TRUE, because we're anybody
     */
    public function equals(PrincipalInterface $another)
    {
        return true;
    }

    /**
     * Returns the principals name as string.
     *
     * @return string The principal's name
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * Return's the principals name as String.
     *
     * @return \AppserverIo\Lang\String The principal's name
     */
    public function getName()
    {
        return new String(AnybodyPrincipal::ANYBODY);
    }
}
