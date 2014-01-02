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
class PharExtractor extends AbstractExtractor implements ExtractorInterface
{

    /**
     * Check if archive is extractable
     *
     * @param $archive \SplFileInfo
     *            The archive object
     *            
     * @return bool
     */
    public function isExtractable(\SplFileInfo $archive)
    {
        $deployFolderName = $this->getDeployDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
        // check if deployed flag exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_DEPLOYED)) {
            return false;
        }
        // check if failed flag exists
        if (file_exists($deployFolderName . ExtractorInterface::FLAG_FAILED)) {
            return false;
        }
        // by default its extractable
        return true;
    }

    /**
     * Extracts the passed PHAR archive to a folder with the
     * basename of the archive file.
     *
     * @param \SplFileInfo $archive
     *            The PHAR file to be deployed
     * @throws \Exception
     * @return void
     */
    protected function extractArchive(\SplFileInfo $archive)
    {
        try {
            // create folder names based on the archive's basename
            $tmpFolderName = $this->getTmpDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
            $webappFolderName = $this->getWebappsDir() . DIRECTORY_SEPARATOR . basename($archive->getFilename(), '.phar');
            
            // check if archive has not been deployed yet or failed sometime
            if ($this->isExtractable($archive)) {
                
                // flag webapp as deploying
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYING);
                
                // extract phar to tmp directory
                $p = new \Phar($archive);
                $p->extractTo($tmpFolderName);
                
                // check if archive was deployed before to do replace deployment
                if (is_dir($webappFolderName)) {
                    
                    // remove folder from webapps dir
                    $this->removeDir($webappFolderName);
                }
                
                // move extracted content to webapps folder
                rename($tmpFolderName, $webappFolderName);
                
                // flag webapp as deployed
                $this->flagArchive($archive, ExtractorInterface::FLAG_DEPLOYED);
            }
        } catch (\Exception $e) {
            // log error
            error_log($e->__toString());
            // flag webapp as failed
            $this->flagArchive($archive, ExtractorInterface::FLAG_FAILED);
        }
    }
}