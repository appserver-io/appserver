<?php

/**
 * AppserverIo\Appserver\Core\Utilities\Algorithms
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

namespace AppserverIo\Appserver\Core\Utilities;

/*                                                                        *
 * This script belongs to the TYPO3 Flow framework.                       *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU Lesser General Public License, either version 3   *
 * of the License, or (at your option) any later version.                 *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

require_once 'PHP/iSecurity/SecurityRandomizer.php';

/**
 * A utility class for various algorithms.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Algorithms
{

    /**
     * Generates a universally unique identifier (UUID) according to RFC 4122.
     * The algorithm used here, might not be completely random.
     *
     * @return string The universally unique id
     * @todo check for randomness, optionally generate type 1 and type 5 UUIDs, use php5-uuid extension if available
     */
    public static function generateUUID()
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Returns a string of random bytes.
     *
     * @param integer $count Number of bytes to generate
     *
     * @return string Random bytes
     */
    public static function generateRandomBytes($count)
    {
        return \SecurityRandomizer::getRandomBytes($count);
    }

    /**
     * Returns a random token in hex format.
     *
     * @param integer $count Token length
     *
     * @return string A random token
     */
    public static function generateRandomToken($count)
    {
        return \SecurityRandomizer::getRandomToken($count);
    }

    /**
     * Returns a random string with alpha-numeric characters.
     *
     * @param integer $count      Number of characters to generate
     * @param string  $characters Allowed characters, defaults to alpha-numeric (a-zA-Z0-9)
     *
     * @return string A random string
     */
    public static function generateRandomString($count, $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        return \SecurityRandomizer::getRandomString($count, $characters);
    }
}
