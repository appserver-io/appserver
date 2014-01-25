<?php

/**
 * TechDivision\ApplicationServer\Interfaces\ExtractorInterface
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Interfaces;

/**
 * An extractor interface
 *
 * @package TechDivision\ApplicationServer
 * @subpackage Extractors
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license Open Software License (OSL 3.0) http://opensource.org/licenses/osl-3.0.php
 * @author Johann Zelger <j.zelger@techdivision.com>
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

    /**
     * Gathers all available archived webapps and extract them for usage.
     *
     * @return void
     */
    public function deployWebapps();

    /**
     * Extracts the passed archive to a folder with the
     * basename of the archive file.
     *
     * @param \SplFileInfo $archive
     *            The archive file to be deployed
     * @throws \Exception
     * @return void
     */
    public function deployArchive(\SplFileInfo $archive);

    /**
     * Undeployes the passed archive after backing up
     * files that are NOT part of the archive.
     *
     * @param \SplFileInfo $archive
     *            The archive file to be undeployed
     * @throws \Exception
     * @return void
     */
    public function undeployArchive(\SplFileInfo $archive);

    /**
     * Redeploys the passed archive after backing up
     * files that are NOT part of the archive.
     *
     * @param \SplFileInfo $archive
     *            The archive file to be deployed
     * @throws \Exception
     * @return void
     */
    public function redeployArchive(\SplFileInfo $archive);

    /**
     * Checks if archive is deployable.
     *
     * @param $archive \SplFileInfo
     *            The archive object
     * @return bool
     */
    public function isDeployable(\SplFileInfo $archive);
    
    /**
     * Check if archive is redeployable.
     *
     * @param $archive \SplFileInfo
     *            The archive object
     * @return bool
     */
    public function isRedeployable(\SplFileInfo $archive);

    /**
     * Check if archive is undeployable.
     *
     * @param $archive \SplFileInfo
     *            The archive object
     * @return bool
     */
    public function isUndeployable(\SplFileInfo $archive);

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive
     *            The archive file
     * @param string $flag
     *            The flag to set
     *            
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag);

    /**
     * Returns the archive extension suffix e.
     * g. .phar
     *
     * @return string the archive extension suffix
     */
    public function getExtensionSuffix();
}