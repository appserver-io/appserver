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
     * Key for the webapps directory.
     *
     * @var string
     */
    const WEBAPPS = 'webapps';

    /**
     * Key for the temporary directory.
     *
     * @var string
     */
    const TMP = 'var/tmp';

    /**
     * Key for the log directory.
     *
     * @var string
     */
    const LOG = 'var/log';

    /**
     * Key for the run directory.
     *
     * @var string
     */
    const RUN = 'var/run';

    /**
     * Key for the deployment directory.
     *
     * @var string
     */
    const DEPLOY = 'deploy';

    /**
     * Path the to main configuration directory.
     *
     * @var string
     */
    const CONF = 'etc/appserver';

    /**
     * Path the to the configurations subdirectory.
     *
     * @var string
     */
    const CONFD = 'etc/appserver/conf.d';

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
     * Returns the application server's directory structure,
     * all directories has to be relative to the base path.
     *
     * @return array The directory structure
     * @todo Has to be extended for all necessary directories
     */
    public static function getDirectories()
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
