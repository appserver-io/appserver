<?php
/**
 * TechDivision\ApplicationServer\Interfaces\ExtractorInterface
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Interfaces
 * @author     Johann Zelger <j.zelger@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Interfaces;

/**
 * An extractor interface
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Interfaces
 * @author     Johann Zelger <j.zelger@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
interface ExtractorInterface
{

    /**
     * Defines flags for extraction logic
     *
     * @var string
     */
    const FLAG_DEPLOYED = '.deployed';
    const FLAG_FAILED = '.failed';
    const FLAG_DEPLOYING = '.deploying';
    const FLAG_DODEPLOY = '.dodeploy';
    const FLAG_UNDEPLOYING = '.undeploying';
    const FLAG_UNDEPLOYED = '.undeployed';

    /**
     * Filename of the hidden file that contains timestamp with last succuessfull deployment.
     * 
     * @var string
     */
    const FILE_DEPLOYMENT_SUCCESSFULL = '.appserver-last-successfull-deployment';

    /**
     * Gathers all available archived webapps and extract them for usage.
     *
     * @return void
     */
    public function deployWebapps();

    /**
     * Soaks the passed archive from a location in the filesystem
     * into the deploy directory and prepares it for the next
     * restart by setting the apropriate flag.
     *
     * @param \SplFileInfo $archive The archive to be soaked
     *
     * @return void
     */
    public function soakArchive(\SplFileInfo $archive);

    /**
     * Extracts the passed archive to a folder with the
     * basename of the archive file.
     *
     * @param \SplFileInfo $archive The archive file to be deployed
     *
     * @throws \Exception
     * @return void
     */
    public function deployArchive(\SplFileInfo $archive);

    /**
     * Undeployes the passed archive after backing up
     * files that are NOT part of the archive.
     *
     * @param \SplFileInfo $archive The archive file to be undeployed
     *
     * @throws \Exception
     * @return void
     */
    public function undeployArchive(\SplFileInfo $archive);

    /**
     * Checks if archive is deployable.
     *
     * @param \SplFileInfo $archive The archive object
     *
     * @return bool
     */
    public function isDeployable(\SplFileInfo $archive);

    /**
     * Check if archive is undeployable.
     *
     * @param \SplFileInfo $archive The archive object
     *
     * @return bool
     */
    public function isUndeployable(\SplFileInfo $archive);

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive The archive file
     * @param string       $flag    The flag to set
     *
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag);

    /**
     * Returns the archive extension suffix e.g. .phar
     *
     * @return string the archive extension suffix
     */
    public function getExtensionSuffix();
}
