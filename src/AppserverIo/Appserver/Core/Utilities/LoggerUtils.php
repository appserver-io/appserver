<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\LoggerKeys
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
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Utilities;

use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Psr\Naming\NamingException;

/**
 * Utility class that contains logger keys as well as a wrapper method for generic logging purposes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2017 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LoggerUtils extends \AppserverIo\Logger\LoggerUtils
{

    /**
     * Key for the system logger instance.
     *
     * @var string
     */
    const SYSTEM_LOGGER = 'SystemLogger';

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level   The log level
     * @param string $message The message to log
     * @param array  $context The context for log
     *
     * @return void
     */
    public static function log($level, $message, array $context = array())
    {

        // query whether or not the application has been registered in the environment
        if (Environment::singleton()->hasAttribute(EnvironmentKeys::APPLICATION)) {
            try {
                // try to load the application system logger
                Environment::singleton()->getAttribute(EnvironmentKeys::APPLICATION)->search(LoggerUtils::SYSTEM_LOGGER)->log($level, $message, $context);
            } catch (NamingException $ne) {
                // load the general system logger and log the message
                Environment::singleton()->getAttribute(EnvironmentKeys::APPLICATION)->search(LoggerUtils::SYSTEM)->log($level, $message, $context);
            }

        } else {
            // log the message with error_log() method as fallback
            error_log('Can\'t find a system logger in runtime environment, using error_log() message as fallback');
            error_log($message);
        }
    }
}
