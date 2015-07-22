<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\FileKeys
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

/**
 * Utility class that contains keys for files necessary to
 * run the appserver.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FileKeys
{

    /**
     * Key for the base directory.
     *
     * @var string
     */
    const APPSERVER_ERRORS_LOG = 'appserver.errors';

    /**
     * Key for the base directory.
     *
     * @var string
     */
    const APPSERVER_ACCESS_LOG = 'appserver.access';

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
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

    /**
     * Returns the application servers directory keys for the directories that
     * has to be created on startup.
     *
     * @return array The keys for the directories to be created on startup
     */
    public static function getServerFileKeysToBeCreated()
    {
        return array(
            self::APPSERVER_ERRORS_LOG,
            self::APPSERVER_ACCESS_LOG
        );
    }

    /**
     * Returns the all application servers directory keys.
     *
     * @return array All application server directory keys
     */
    public static function getServerFileKeys()
    {
        return array(
            self::APPSERVER_ERRORS_LOG,
            self::APPSERVER_ACCESS_LOG
        );
    }

    /**
     * Returns the application specific directory keys.
     *
     * @return array The application specific directory keys
     */
    public static function getApplicationFileKeys()
    {
        return array();
    }

    /**
     * Returns to the passed file with OS specific directory separators.
     *
     * @param string $file The file to prepare
     *
     * @return string The OS specific path of the passed file
     */
    public static function realpath($file)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $file);
    }
}
