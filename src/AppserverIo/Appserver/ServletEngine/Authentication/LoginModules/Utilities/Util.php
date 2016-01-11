<?php

/**
 * AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities\Util
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

namespace AppserverIo\Appserver\ServletEngine\Authentication\LoginModules\Utilities;

use AppserverIo\Lang\String;

/**
 * Utility class for security purposes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Util
{

    /**
     * Key for base64 encoding.
     *
     * @var string
     */
    const BASE64_ENCODING = 'base64Encoding';

    /**
     * Creates and returns a hashed version of the passed password.
     *
     * @param string                   $hashAlgorithm The hash algorithm to use
     * @param string                   $hashEncoding  The hash encoding to use
     * @param string                   $hashCharset   The hash charset to use
     * @param \AppserverIo\Lang\String $name          The login name
     * @param \AppserverIo\Lang\String $password      The password credential
     * @param mixed                    $callback      The callback providing some additional hashing functionality
     *
     * @return \AppserverIo\Lang\String The hashed password
     */
    public static function createPasswordHash($hashAlgorithm, $hashEncoding, $hashCharset, String $name, String $password, $callback)
    {
        return $password->md5();
    }

    /**
     * This is a utility class, so protect it against direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }
}