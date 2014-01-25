<?php

/**
 * TechDivision\ApplicationServer\Extractors\PharExtractor
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Extractors;

use TechDivision\ApplicationServer\AbstractExtractor;
use TechDivision\ApplicationServer\Api\Node\AppNode;
use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;

/**
 * An extractor implementation for phar files
 *
 * @package TechDivision\ApplicationServer
 * @subpackage Extractors
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license Open Software License (OSL 3.0) http://opensource.org/licenses/osl-3.0.php
 * @author Johann Zelger <j.zelger@techdivision.com>
 */
class PharExtractor extends AbstractExtractor
{

    /**
     * The archive suffix.
     *
     * @var string
     */
    const EXTENSION_SUFFIX = '.phar';
    
    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\AbstractExtractor::getExtensionSuffix()
     */
    public function getExtensionSuffix()
    {
        return PharExtractor::EXTENSION_SUFFIX;
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\AbstractExtractor::deployArchive()
     */
    public function deployArchive(\SplFileInfo $archive)
    {
        try {
            
            // create folder names based on the archive's basename
            $tmpFolderName = $this->getTmpDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
            $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
            
            // check if archive has not been deployed yet or failed sometime
            if ($this->isDeployable($archive)) {
                
                // flag webapp as deploying
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYING);
                
                // extract phar to tmp directory
                $p = new \Phar($archive);
                $p->extractTo($tmpFolderName);
                
                // move extracted content to webapps folder
                rename($tmpFolderName, $webappFolderName);
                
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
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\Interfaces\ExtractorInterface::redeployArchive()
     */
    public function redeployArchive(\SplFileInfo $archive)
    {
        if ($this->isRedeployable($archive)) {
            $this->undeployArchive($archive);
            $this->deployArchive($archive);
        }
    }
    
    /**
     * (non-PHPdoc)
     * 
     * @see \TechDivision\ApplicationServer\Interfaces\ExtractorInterface::undeployArchive()
     */
    public function undeployArchive(\SplFileInfo $archive)
    {
        
        try {

            // create webapp folder name based on the archive's basename
            $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
            
            // check if app has to be undeployed
            if ($this->isUndeployable($archive) && is_dir($webappFolderName)) {
                
                // backup files that are NOT part of the archive
                $this->backupArchive($archive);
                
                // remove the webapp folder
                $this->removeDir($webappFolderName);  
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
     * @return void 
     */
    public function backupArchive(\SplFileInfo $archive)
    {

        // load the PHAR archive's basename
        $pharBasename = $archive->getBasename();
        
        $this->getInitialContext()->getSystemLogger()->error("Found PHAR basename: $pharBasename");
        
        // create webapp folder name based on the archive's basename
        $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
        
        $this->getInitialContext()->getSystemLogger()->error("Found webapp folder: $webappFolderName");
        
        // initialize PHAR archive
        $p = new \Phar($archive);
        
        // iterate over the PHAR content to backup files that are NOT part of the archive
        foreach (new \RecursiveIteratorIterator($p) as $file) {
            $this->getInitialContext()->getSystemLogger()->error(str_replace($pharBasename, $webappsDir, $file->getPathName()));
            // unlink(str_replace($pharBasename, $webappsDir, file->getPathName()));
        }
    }
    


    /**
     * Removes a directory recursively.
     *
     * @param string $dir
     *            The directory to remove
     * @return void
     */
    protected function cleanDirByPhar($dir)
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
}