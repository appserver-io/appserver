<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\FormAuthentication
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
 * A simple String based implementation of Principal. Typically a SimplePrincipal is
 * created given a userID which is used as the Principal name.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SimplePrincipal implements PrincipalInterface
{

    /**
     * The principal name.
     *
     * @var \AppserverIo\Lang\String
     */
    private $name;

    /**
     * Initialize the principal with the passed name.
     *
     * @param \AppserverIo\Lang\String $name The principal's name
     */
    public function __construct(String $name)
    {
        $this->name = $name;
    }

    /**
     * Compare this SimplePrincipal's name against another Principal.
     *
     * @param PrincipalInterface $another The other principal to compare to
     *
     * @return boolean TRUE if name equals $another->getName();
     */
    public function equals(PrincipalInterface $another)
    {

        // query whether or not another principal has been passed
        if ($another instanceof PrincipalInterface) {
            $anotherName = $another->getName();
            $equals = false;
            if ($this->name == null) {
                $equals = $anotherName == null;
            } else {
                $equals = $this->name->equals($anotherName);
            }

            // return the flag if the both are equal
            return $equals;
        }

        // return FALSE if they are not equal
        return false;
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

    /**
     * Return's the principals name as String.
     *
     * @return \AppserverIo\Lang\String The principal's name
     */
    public function getName()
    {
        return $this->name;
    }
}
