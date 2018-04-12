<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\PermissionHelper
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Helper utility which is used to manage permission related issues
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */
class PermissionHelper
{

    /**
     * Helper method which allows to execute a callable as the super user the server got started by.
     *
     * @param callable $callable  The callable to run
     * @param array    $arguments Arguments to pass to the callable
     *
     * @return mixed The callables result
     */
    public static function sudo(callable $callable, array $arguments = array())
    {
        // don't do anything under Windows
        if (FileSystem::getOsIdentifier() === FileSystem::OS_IDENTIFIER_WIN) {
            return call_user_func_array($callable, $arguments);
        }

        // get the current user user pair (super user and effective user)
        $currentUserId = (integer) posix_geteuid();
        $superUserId = (integer) posix_getuid();

        // temporarily switch to the super user
        posix_seteuid($superUserId);

        // execute the callable
        $result = call_user_func_array($callable, $arguments);

        // switch back to the effective user
        posix_seteuid($currentUserId);

        return $result;
    }
}
