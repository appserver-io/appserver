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
     * @see \TechDivision\ApplicationServer\Interfaces\ExtractorInterface::undeployArchive()
     */
    public function undeployArchive(\SplFileInfo $archive)
    {
        
        try {

            // create webapp folder name based on the archive's basename
            $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), $this->getExtensionSuffix());
            
            // check if app has to be undeployed
            if ($this->isUndeployable($archive) && is_dir($webappFolderName)) {
                $this->removeDir($webappFolderName);  
            }
            
        } catch (\Exception $e) {
            // log error
            $this->getInitialContext()->getSystemLogger()->error($e->__toString());
            // flag webapp as failed
            $this->flagArchive($archive, ExtractorInterface::FLAG_FAILED);
        }
    }
}