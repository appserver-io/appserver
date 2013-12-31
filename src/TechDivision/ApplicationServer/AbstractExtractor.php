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

/**
 * Abstract extractor functionality
 *
 * @package    TechDivision\ApplicationServer
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    Open Software License (OSL 3.0) http://opensource.org/licenses/osl-3.0.php
 * @author     Johann Zelger <j.zelger@techdivision.com>
 */
class AbstractExtractor
{

    /**
     * Contructor
     */
    public function __construct()
    {
        // prepare filesystem
        $this->prepareFileSystem();
    }

    /**
     * Prepares filesystem to be sure that everything is on place as expected
     *
     * @return void
     */
    public function prepareFileSystem()
    {
        // check if directories exists
        if (!is_dir($this->getDeployDir())) {
            mkdir($this->getDeployDir());
        }
        if (!is_dir($this->getTmpDir())) {
            mkdir($this->getTmpDir());
        }
    }

    /**
     * Returns the servers base directory
     *
     * @return string
     */
    protected function getBaseDir()
    {
        return APPSERVER_BP;
    }

    /**
     * Returns the servers tmp directory
     *
     * @return string
     */
    protected function getTmpDir()
    {
        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'tmp';
    }

    /**
     * Returns the servers deploy directory
     *
     * @return string
     */
    protected function getDeployDir()
    {
        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'deploy';
    }

    /**
     * Returns the servers webapps directory
     *
     * @return string
     */
    protected function getWebappsDir()
    {
        return $this->getBaseDir() . DIRECTORY_SEPARATOR . 'webapps';
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
            ExtractorInterface::FLAG_FAILED
        );
    }

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive
     * @param string $flag The flag to set
     *
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag)
    {
        // delete old flags
        foreach ($this->getFlags() as $flagString) {
            if (file_exists($archive->getRealPath() . $flagString)) {
                unlink($archive->getRealPath() . $flagString);
            }
        }
        // get archives folder name from deploy dir
        $deployFolderName = $this->getDeployDir() . DIRECTORY_SEPARATOR . $archive->getFilename();
        // flag archive
        return file_put_contents($deployFolderName . $flag, $archive->getFilename());
    }

    /**
     * Removes a directory recursively
     *
     * @param string $dir The directory to remove
     *
     * @return void
     */
    protected function removeDir($dir)
    {
        // remove old archive from webapps folder recursively
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($files as $file) {
            // skip . and .. dirs
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()){
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
     * Gathers all available archived webapps and extract them for usage.
     *
     * @return void
     */
    public function extractWebapps()
    {
        // init file iterator on deployment directory
        $fileIterator = new \FilesystemIterator($this->getDeployDir());
        // Iterate through all phar files and extract them to tmp dir
        foreach (new \RegexIterator($fileIterator, '/^.*\.phar$/') as $archive) {
            $this->extractArchive($archive);
        }
    }
}
