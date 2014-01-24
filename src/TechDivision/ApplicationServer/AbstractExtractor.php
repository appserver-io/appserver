<?php

/**
 * TechDivision\ApplicationServer\AbstractExtractor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;
use TechDivision\ApplicationServer\Utilities\DirectoryKeys;
use TechDivision\ApplicationServer\Api\ServiceInterface;

/**
 * Abstract extractor functionality
 *
 * @package TechDivision\ApplicationServer
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license Open Software License (OSL 3.0) http://opensource.org/licenses/osl-3.0.php
 * @author Johann Zelger <j.zelger@techdivision.com>
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
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * Contructor
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     */
    public function __construct($initialContext)
    {
        // add initialContext
        $this->initialContext = $initialContext;
        // init API service to use
        $this->service = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
    }

    /**
     * Returns the servers tmp directory
     *
     * @return string
     */
    protected function getTmpDir()
    {
        return $this->getService()->getTmpDir();
    }

    /**
     * Returns the servers deploy directory
     *
     * @return string
     */
    protected function getDeployDir()
    {
        return $this->getService()->getDeployDir();
    }

    /**
     * Returns the servers webapps directory
     *
     * @return string
     */
    protected function getWebappsDir()
    {
        return $this->getService()->getWebappsDir();
    }

    /**
     * Returns all flags in array
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
        );
    }

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive
     *            The archive to flag
     * @param string $flag
     *            The flag to set
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag)
    {
        // delete all old flags
        $this->unflagArchive($archive);
        // get archives folder name from deploy dir
        $deployFolderName = $this->getDeployDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
        // flag archive
        return file_put_contents($deployFolderName . $flag, $archive->getFilename());
    }

    /**
     * Deletes all old flags, so the app will be undeployed with
     * the next appserver restart.
     *
     * @param \SplFileInfo $archive
     *            The archive to unflag
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
     * @see \TechDivision\ApplicationServer\Interfaces\ExtractorInterface::isDeployable()
     */
    public function isDeployable(\SplFileInfo $archive)
    {

        // prepare the deploy folder
        $deployFolderName = $this->getDeployDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
        
        // check if .deployed flag exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_DEPLOYED)) {
            return false;
        }
        // check if .failed flag exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_FAILED)) {
            return false;
        }
        // check if the .dodeploy flag file exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_DODEPLOY) === false) {
            return false;
        }
        
        // by default it's deployable
        return true;
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\Interfaces\ExtractorInterface::isRedeployable()
     */
    public function isRedeployable(\SplFileInfo $archive)
    {

        // prepare the deploy and webapp folder name
        $deployFolderName = $this->getDeployDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
        $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());

        // check if the .redeploy flag file exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_REDEPLOY) && is_dir($webappFolderName)) {
            return true;
        }
        
        // by default it's NOT redeployable
        return false;
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Interfaces\ExtractorInterface::isUndeployable()
     */
    public function isUndeployable(\SplFileInfo $archive)
    {

        // prepare the deploy folder
        $deployFolderName = $this->getDeployDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
        
        foreach ($this->getFlags() as $flag) {
            if (file_exists($deployFolderName . $flag)) {
                return false;
            }
        }
        
        // by default its extractable
        return true;
    }

    /**
     * Removes a directory recursively.
     *
     * @param string $dir
     *            The directory to remove
     * @return void
     */
    protected function removeDir($dir)
    {
        // remove old archive from webapps folder recursively
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $file) {
            // skip . and .. dirs
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                // remove dir if is dir
                rmdir($file->getRealPath());
            } else {
                // remove file
                unlink($file->getRealPath());
            }
        }
        // delete empty webapp folder
        rmdir($dir);
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::deployWebapps()
     */
    public function deployWebapps()
    {
        // check if deploy dir exists
        if (is_dir($this->getDeployDir())) {
            // init file iterator on deployment directory
            $fileIterator = new \FilesystemIterator($this->getDeployDir());
            // Iterate through all phar files and extract them to tmp dir
            foreach (new \RegexIterator($fileIterator, '/^.*\\' . $this->getExtensionSuffix() . '$/') as $archive) {
                $this->deployArchive($archive);
                $this->undeployArchive($archive);
            }
        }
    }
    
    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newService()
     */
    public function newService($className)
    {
        return $this->getInitialContext()->newService($className);
    }

    /**
     * Returns the inital context instance.
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the service instance to use.
     *
     * @return \TechDivision\ApplicationServer\Api\ServiceInterface $service The service to use
     */
    public function getService()
    {
        return $this->service;
    }
}