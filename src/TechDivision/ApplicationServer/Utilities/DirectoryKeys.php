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
    const TMP = 'tmp';

    /**
     * Key for the log directory.
     *
     * @var string
     */
    const LOG = 'var/log';

    /**
     * Key for the deployment directory.
     *
     * @var string
     */
    const DEPLOY = 'deploy';

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     *
     * @return void
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
            DirectoryKeys::LOG
        );
    }
}
