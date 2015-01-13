<?php

/**
 * AppserverIo\Appserver\Core\Utilities\DirectoryKeys
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Utility class that contains keys for directories necessary to
 * run the appserver.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
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
     * Returns the application servers directory keys.
     *
     * @return array The application server directory keys
     */
    public static function getServerDirectoryKeys()
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
