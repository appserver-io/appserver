<?php
/**
 * AppserverIo\Appserver\Core\Extractors\PharExtractor
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
     * @param \SplFileInfo $archive The archive file to be deployed
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractExtractor::deployArchive()
     */
    public function deployArchive(\SplFileInfo $archive)
    {

        try {
            // create folder names based on the archive's basename
            $tmpFolderName = new \SplFileInfo($this->getTmpDir() . DIRECTORY_SEPARATOR . $archive->getFilename());
            $webappFolderName = new \SplFileInfo($this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix()));

            // check if archive has not been deployed yet or failed sometime
            if ($this->isDeployable($archive)) {
                // flag webapp as deploying
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYING);

                // backup actual webapp folder, if available
                if ($webappFolderName->isDir()) {
                    // backup files that are NOT part of the archive
                    $this->backupArchive($archive);

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

                // move extracted content to webapps folder
                rename($tmpFolderName->getPathname(), $webappFolderName->getPathname());

                // restore backup if available
                $this->restoreBackup($archive);

                // We have to set the user rights to the user:group configured within the system configuration
                $this->setUserRights($webappFolderName);

                // flag webapp as deployed
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYED);
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
     * @param \SplFileInfo $archive Backup files that are NOT part of this archive
     *
     * @return void
     */
    public function backupArchive(\SplFileInfo $archive)
    {

        // if we don't want to create backups, to nothing
        if ($this->getExtractorNode()->isCreateBackups() === false) {
            return;
        }

        // load the PHAR archive's pathname
        $pharPathname = $archive->getPathname();

        // create tmp & webapp folder name based on the archive's basename
        $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
        $tmpFolderName = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(basename($archive->getFilename(), $this->getExtensionSuffix()));

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
    }
}
