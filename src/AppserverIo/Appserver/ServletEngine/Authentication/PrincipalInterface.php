<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface
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
 * Interface for all principals.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface PrincipalInterface
{

    /**
     * Compare this SimplePrincipal's name against another Principal.
     *
     * @param \xAppserverIo\Appserver\ServletEngine\Authentication\PrincipalInterface $another The other principal to compare to
     *
     * @return boolean TRUE If name equals $another->getName();
     */
    public function equals(PrincipalInterface $another);

    /**
     * Returns the principals name as string.
     *
     * @return string The principal's name
     */
    public function __toString();

    /**
     * Return's the principals name as String.
     *
     * @return \AppserverIo\Lang\String The principal's name
     */
    public function getName();
}
