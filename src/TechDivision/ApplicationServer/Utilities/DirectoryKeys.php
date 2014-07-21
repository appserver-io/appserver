<?php
/**
 * TechDivision\ApplicationServer\Utilities\DirectoryKeys
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Utilities
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Utilities;

/**
 * Utility class that contains keys for directories necessary to
 * run the appserver.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Utilities
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
            DirectoryKeys::RUN
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
