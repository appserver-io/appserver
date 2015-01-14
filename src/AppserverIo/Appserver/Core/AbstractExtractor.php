<?php

/**
 * AppserverIo\Appserver\Core\AbstractExtractor
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Application\Interfaces\ContextInterface;
use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface;

/**
 * Abstract extractor functionality
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractExtractor implements ExtractorInterface
{

    /**
     * The container's base directory.
     *
     * @var string
     */
    protected $service;

    /**
     * The initial context instance.
     *
     * @var \AppserverIo\Appserver\Application\Interfaces\ContextInterface
     */
    protected $initialContext;

    /**
     * The extractor node configuration data.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface
     */
    protected $extractorNode;

    /**
     * Contructor to initialize the extractor instance with the initial context
     * and the extractor node configuration data.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext The initial context instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface    $extractorNode  The extractor node configuration data
     */
    public function __construct(ContextInterface $initialContext, ExtractorNodeInterface $extractorNode)
    {
        // add initial context and extractor node configuration data
        $this->initialContext = $initialContext;
        $this->extractorNode = $extractorNode;
        // init API service to use
        $this->service = $this->newService('AppserverIo\Appserver\Core\Api\ContainerService');
    }

    /**
     * Returns all flags in array.
     *
     * @return array
     */
    protected function getFlags()
    {
        return array(
            ExtractorInterface::FLAG_DEPLOYED,
            ExtractorInterface::FLAG_DEPLOYING,
            ExtractorInterface::FLAG_DODEPLOY,
            ExtractorInterface::FLAG_FAILED,
            ExtractorInterface::FLAG_UNDEPLOYED,
            ExtractorInterface::FLAG_UNDEPLOYING
        );
    }

    /**
     * Will actually deploy all webapps.
     *
     * @return void
     */
    public function deployWebapps()
    {
        // check if deploy dir exists
        if (is_dir($this->getDeployDir())) {
            // init file iterator on deployment directory
            $fileIterator = new \FilesystemIterator($this->getDeployDir());
            // Iterate through all phar files and extract them to tmp dir
            foreach (new \RegexIterator($fileIterator, '/^.*\\' . $this->getExtensionSuffix() . '$/') as $archive) {
                $this->undeployArchive($archive);
                $this->deployArchive($archive);
            }
        }

        // prepare the filename for the file with the last succesfull deployment timestamp
        $successFile = $this->getService()->getDeployDir(ExtractorInterface::FILE_DEPLOYMENT_SUCCESSFULL);

        // create a flag file with date of the last successfull deployment
        touch($successFile);
    }

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive The archive to flag
     * @param string       $flag    The flag to set
     *
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag)
    {
        // delete all old flags
        $this->unflagArchive($archive);
        // get archives folder name from deploy dir
        $deployFolderName = $this->getDeployDir($archive->getFilename());
        // flag archive
        file_put_contents($deployFolderName . $flag, $archive->getFilename());
        // set correct user/group for the flag file
        $this->setUserRight(new \SplFileInfo($deployFolderName . $flag));
    }

    /**
     * Deletes all old flags, so the app will be undeployed with
     * the next appserver restart.
     *
     * @param \SplFileInfo $archive The archive to unflag
     *
     * @return void
     */
    public function unflagArchive(\SplFileInfo $archive)
    {
        foreach ($this->getFlags() as $flagString) {
            if (file_exists($archive->getRealPath() . $flagString)) {
                unlink($archive->getRealPath() . $flagString);
            }
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @param \SplFileInfo $archive The archive object
     *
     * @return bool
     * @see \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface::isDeployable()
     */
    public function isDeployable(\SplFileInfo $archive)
    {

        // prepare the deploy folder
        $deployFolderName = $this->getDeployDir($archive->getFilename());

        // check if the .dodeploy flag file exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_DODEPLOY)) {
            return true;
        }

        // by default it's NOT deployable
        return false;
    }

    /**
     * (non-PHPdoc)
     *
     * @param \SplFileInfo $archive The archive object
     *
     * @return bool
     * @see \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface::isUndeployable()
     */
    public function isUndeployable(\SplFileInfo $archive)
    {

        // prepare the deploy folder
        $deployFolderName = $this->getDeployDir($archive->getFilename());

        // make sure that NO flag for the archive is available
        foreach ($this->getFlags() as $flag) {
            if (file_exists($deployFolderName . $flag)) {
                return false;
            }
        }

        // it's undeployable if NOT marker file exists
        return true;
    }

    /**
     * (non-PHPdoc)
     *
     * @param \SplFileInfo $archive The archive to be soaked
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface::soakArchive()
     */
    public function soakArchive(\SplFileInfo $archive)
    {

        // prepare the upload target in the deploy directory
        $target = $this->getDeployDir($archive->getFilename());

        // move the uploaded file from the tmp to the deploy directory
        rename($archive->getPathname(), $target);

        // makr the file to be deployed with the next restart
        $this->flagArchive(new \SplFileInfo($target), ExtractorInterface::FLAG_DODEPLOY);
    }

    /**
     * (non-PHPdoc)
     *
     * @param \SplFileInfo $archive The archive file to be undeployed
     *
     * @throws \Exception
     * @return void
     * @see \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface::undeployArchive()
     */
    public function undeployArchive(\SplFileInfo $archive)
    {
        try {

            // create webapp folder name based on the archive's basename
            $webappFolderName = new \SplFileInfo(
                $this->getWebappsDir(basename($archive->getFilename(), $this->getExtensionSuffix()))
            );

            // check if app has to be undeployed
            if ($this->isUndeployable($archive) && $webappFolderName->isDir()) {

                // flag webapp as undeploing
                $this->flagArchive($archive, ExtractorInterface::FLAG_UNDEPLOYING);

                // backup files that are NOT part of the archive
                $this->backupArchive($archive);

                // delete directories previously backed up
                $this->removeDir($webappFolderName);

                // flag webapp as undeployed
                $this->flagArchive($archive, ExtractorInterface::FLAG_UNDEPLOYED);
            }

        } catch (\Exception $e) {

            // log error
            $this->getInitialContext()->getSystemLogger()->error($e->__toString());

            // flag webapp as failed
            $this->flagArchive($archive, ExtractorInterface::FLAG_FAILED);
        }
    }

    /**
     * Restores the backup files from the backup directory.
     *
     * @param \SplFileInfo $archive To restore the files for
     *
     * @return void
     */
    public function restoreBackup(\SplFileInfo $archive)
    {

        // if we don't want create backups we can't restore them, so do nothing
        if ($this->getExtractorNode()->isCreateBackups() === false || $this->getExtractorNode()->isRestoreBackups() === false) {
            return;
        }

        // create tmp & webapp folder name based on the archive's basename
        $webappFolderName = $this->getWebappsDir(basename($archive->getFilename(), $this->getExtensionSuffix()));
        $tmpFolderName = $this->getTmpDir(md5(basename($archive->getFilename(), $this->getExtensionSuffix())));

        // copy backup to webapp directory
        $this->copyDir($tmpFolderName, $webappFolderName);
    }

    /**
     * Removes a directory recursively.
     *
     * @param \SplFileInfo $dir             The directory to remove
     * @param bool         $alsoRemoveFiles The flag for removing files also
     *
     * @return void
     */
    protected function removeDir(\SplFileInfo $dir, $alsoRemoveFiles = true)
    {

        // clean up the directory
        $this->getService()->cleanUpDir($dir, $alsoRemoveFiles);

        // check if the directory exists, if not return immediately
        if ($dir->isDir() === false) {
            return;
        }

        // delete the directory itself if empty
        @rmdir($dir->getPathname());
    }

    /**
     * (non-PHPdoc)
     *
     * @param string $className The API service class name to return the instance for
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface The service instance
     * @see \AppserverIo\Appserver\Core\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ContextInterface The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the extractor node configuration data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface The extractor node configuration data
     */
    public function getExtractorNode()
    {
        return $this->extractorNode;
    }

    /**
     * Returns the service instance to use.
     *
     * @return \AppserverIo\Appserver\Core\Api\ServiceInterface $service The service to use
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Returns the servers tmp directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getTmpDir($relativePathToAppend = '')
    {
        return $this->getService()->getTmpDir($relativePathToAppend);
    }

    /**
     * Returns the servers deploy directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string
     */
    public function getDeployDir($relativePathToAppend = '')
    {
        return $this->getService()->getDeployDir($relativePathToAppend);
    }

    /**
     * Returns the servers webapps directory.
     *
     * @param string $relativePathToAppend A relative path to append
     *
     * @return string The web application directory
     */
    public function getWebappsDir($relativePathToAppend = '')
    {
        return $this->getService()->getWebappsDir($relativePathToAppend);
    }

    /**
     * Sets the configured user/group settings on the passed file.
     *
     * @param \SplFileInfo $fileInfo The file to set user/group for
     *
     * @return void
     */
    protected function setUserRight(\SplFileInfo $fileInfo)
    {
        $this->getService()->setUserRight($fileInfo);
    }

    /**
     * Will set the owner and group on the passed directory.
     *
     * @param \SplFileInfo $targetDir The directory to set the rights for
     *
     * @return void
     */
    protected function setUserRights(\SplFileInfo $targetDir)
    {
        $this->getService()->setUserRights($targetDir);
    }

    /**
     * Copies a directory recursively.
     *
     * @param string $src The source directory to copy
     * @param string $dst The target directory
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\Api\AbstractService::copyDir()
     */
    public function copyDir($src, $dst)
    {
        $this->getService()->copyDir($src, $dst);
    }
}
