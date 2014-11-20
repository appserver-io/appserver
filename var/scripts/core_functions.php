<?php

/**
 * var/scripts/core_functions.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

/**
 * Override getenv
 *
 * @param string $key The key string
 *
 * @return mixed
 * @link http://de3.php.net/manual/de/function.getenv.php
 */

function getenv($key) {
    // first look up env key in global $_ENV
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    // fallback look up env key in global $_SERVER
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
}

/**
 * Override putenv
 *
 * @param string $keyvalue The keyvalue combination 'key=value' as string.
 *
 * @return void
 * @link http://de3.php.net/manual/de/function.putenv.php
 */
function putenv($keyvalue) {
    // split up keyvalue combination
    @list ($key, $value) = explode('=', $keyvalue);
    // check if key was not null
    if (!is_null($key)) {
        // check if value is not null
        if (!is_null($value)) {
            $_ENV[$key] = $value;
        } else {
            // if no value exists unset entry
            unset($_ENV[$key]);
        }
    }
}
