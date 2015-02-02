<?php

/**
 * AppserverIo\Appserver\Core\Utilities\DirectoryKeys
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
 * Utility class that contains keys for directories necessary to
 * run the appserver.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DirectoryKeys
{

    /**
     * Key for the base directory.
     *
     * @var string
     */
    const BASE = 'base.dir';

    /**
     * Key for the webapps directory.
     *
     * @var string
     */
    const WEBAPPS = 'webapps.dir';

    /**
     * Key for the temporary directory.
     *
     * @var string
     */
    const TMP = 'tmp.dir';

    /**
     * Key for the log directory.
     *
     * @var string
     */
    const LOG = 'log.dir';

    /**
     * Key for the run directory.
     *
     * @var string
     */
    const RUN = 'run.dir';

    /**
     * Key for the deployment directory.
     *
     * @var string
     */
    const DEPLOY = 'deploy.dir';

    /**
     * Path the to main configuration directory.
     *
     * @var string
     */
    const CONF = 'conf.dir';

    /**
     * Path the to the configurations subdirectory.
     *
     * @var string
     */
    const CONFD = 'confd.dir';

    /**
     * Path the to the application specific cache directory.
     *
     * @var string
     */
    const CACHE = 'cache.dir';

    /**
     * Path the to the application specific session directory.
     *
     * @var string
     */
    const SESSION = 'session.dir';

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
    public static function getServerDirectoryKeysToBeCreated()
    {
        return array(
            DirectoryKeys::WEBAPPS,
            DirectoryKeys::TMP,
            DirectoryKeys::DEPLOY,
            DirectoryKeys::LOG,
            DirectoryKeys::RUN,
            DirectoryKeys::CONF,
            DirectoryKeys::CONFD
        );
    }

    /**
     * Returns the all application servers directory keys.
     *
     * @return array All application server directory keys
     */
    public static function getServerDirectoryKeys()
    {
        return array(
            DirectoryKeys::BASE,
            DirectoryKeys::WEBAPPS,
            DirectoryKeys::TMP,
            DirectoryKeys::DEPLOY,
            DirectoryKeys::LOG,
            DirectoryKeys::RUN,
            DirectoryKeys::CONF,
            DirectoryKeys::CONFD
        );
    }

    /**
     * Returns the application specific directory keys.
     *
     * @return array The application specific directory keys
     */
    public static function getApplicationDirectoryKeys()
    {
        return array(
            DirectoryKeys::CACHE,
            DirectoryKeys::SESSION
        );
    }

    /**
     * Returns to the passed directory with OS specific directory separators.
     *
     * @param string $directory The directory to prepare
     *
     * @return string The OS specific path of the passed directory
     */
    public static function realpath($directory)
    {
        return str_replace('/', DIRECTORY_SEPARATOR, $directory);
    }
}
