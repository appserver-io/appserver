<?php

/**
 * functions/application/emergency.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <ts@appserver.io>
 * @copyright 2018 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

use Psr\Log\LogLevel;
use AppserverIo\Appserver\Core\Utilities\LoggerUtils;

// make sure the function has not already been registered
if (!function_exists('emergency')) {
    /**
     * System is unusable.
     *
     * @param string $message The message to log
     * @param array  $context The context for log
     *
     * @return void
     */
    function emergency($message, array $context = array())
    {
        LoggerUtils::log(LogLevel::EMERGENCY, $message, $context);
    }
}
