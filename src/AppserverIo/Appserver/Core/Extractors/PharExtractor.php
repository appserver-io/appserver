<?php
/**
 * \AppserverIo\Appserver\Core\Extractors\PharExtractor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Extractors;

use AppserverIo\Appserver\Core\AbstractExtractor;
use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface;
use PDepend\Util\FileUtil;
use AppserverIo\Appserver\Core\Utilities\FileSystem;

/**
 * An extractor implementation for phar files.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PharExtractor extends AbstractExtractor
{

    /**
     * The PHAR identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'phar';

    /**
     * (non-PHPdoc)
     *
     * @return string the archive extension suffix
     */
    public function getExtensionSuffix()
    {
        return '.' . PharExtractor::IDENTIFIER;
    }

    /**
     * Returns the URL for the passed pathname.
     *
     * @param string $fileName The pathname to return the URL for
     *
     * @return string The URL itself
     */
    public function createUrl($fileName)
    {
        return PharExtractor::IDENTIFIER . '://' . $fileName;
    }

    /**
     * (non-PHPdoc)
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode The container the archive belongs to
     * @param \SplFileInfo                                                $archive       The archive file to be deployed
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractExtractor::deployArchive()
     */
    public function deployArchive(ContainerNodeInterface $containerNode, \SplFileInfo $archive)
    {

        try {
            // create folder names based on the archive's basename
            $tmpFolderName = new \SplFileInfo($this->getTmpDir($containerNode) . DIRECTORY_SEPARATOR . $archive->getFilename());
            $webappFolderName = new \SplFileInfo($this->getWebappsDir($containerNode) . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix()));

            // check if archive has not been deployed yet or failed sometime
            if ($this->isDeployable($archive)) {
                // flag webapp as deploying
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYING);

                // backup actual webapp folder, if available
                if ($webappFolderName->isDir()) {
                    // backup files that are NOT part of the archive
                    $this->backupArchive($containerNode, $archive);

                    // delete directories previously backed up
                    $this->removeDir($webappFolderName);
                }

                // remove old temporary directory
                $this->removeDir($tmpFolderName);

                // initialize a \Phar instance
                $p = new \Phar($archive);

                // create a recursive directory iterator
                $iterator = new \RecursiveIteratorIterator($p);

                // unify the archive filename, because Windows uses a \ instead of /
                $archiveFilename = sprintf('phar://%s', str_replace(DIRECTORY_SEPARATOR, '/', $archive->getPathname()));

                // iterate over all files
                foreach ($iterator as $file) {
                    // prepare the temporary filename
                    $target = $tmpFolderName . str_replace($archiveFilename, '', $file->getPathname());

                    // create the directory if necessary
                    if (file_exists($directory = dirname($target)) === false) {
                        if (mkdir($directory, 0755, true) === false) {
                            throw new \Exception(sprintf('Can\'t create directory %s', $directory));
                        }
                    }

                    // finally copy the file
                    if (copy($file, $target) === false) {
                        throw new \Exception(sprintf('Can\'t copy %s file to %s', $file, $target));
                    }
                }

                // move extracted content to webapps folder and remove temporary directory
                FileSystem::copyDir($tmpFolderName->getPathname(), $webappFolderName->getPathname());
                FileSystem::removeDir($tmpFolderName->getPathname());

                // we need to set the user/rights for the extracted folder
                $this->setUserRights($webappFolderName);

                // restore backup if available
                $this->restoreBackup($containerNode, $archive);

                // flag webapp as deployed
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYED);

                // log a message that the application has successfully been deployed
                $this->getInitialContext()->getSystemLogger()->info(
                    sprintf('Application archive %s has succussfully been deployed', $archive->getBasename($this->getExtensionSuffix()))
                );
            }

        } catch (\Exception $e) {
            // log error
            $this->getInitialContext()->getSystemLogger()->error($e->__toString());

            // flag webapp as failed
            $this->flagArchive($archive, ExtractorInterface::FLAG_FAILED);
        }
    }

    /**
     * Creates a backup of files that are NOT part of the
     * passed archive.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode The container the archive belongs to
     * @param \SplFileInfo                                                $archive       Backup files that are NOT part of this archive
     *
     * @return void
     */
    public function backupArchive(ContainerNodeInterface $containerNode, \SplFileInfo $archive)
    {

        // if we don't want to create backups, to nothing
        if ($this->getExtractorNode()->isCreateBackups() === false) {
            return;
        }

        // load the PHAR archive's pathname
        $pharPathname = $archive->getPathname();

        // create tmp & webapp folder name based on the archive's basename
        $webappFolderName = $this->getWebappsDir($containerNode) . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
        $tmpFolderName = $this->getTmpDir($containerNode) . DIRECTORY_SEPARATOR . md5(basename($archive->getFilename(), $this->getExtensionSuffix()));

        // initialize PHAR archive
        $p = new \Phar($archive);

        // iterate over the PHAR content to backup files that are NOT part of the archive
        foreach (new \RecursiveIteratorIterator($p) as $file) {
            unlink(str_replace($this->createUrl($pharPathname), $webappFolderName, $file->getPathName()));
        }

        // delete empty directories but LEAVE files created by app
        $this->removeDir($webappFolderName, false);

        // copy backup to tmp directory
        $this->copyDir($webappFolderName, $tmpFolderName);

        // we have to set the user rights to the user:group configured within the system configuration
        $this->setUserRights(new \SplFileInfo($tmpFolderName));

        // log a message that the application has successfully been deployed
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf('Application archive %s has succussfully been backed up', $archive->getBasename($this->getExtensionSuffix()))
        );
    }
}
