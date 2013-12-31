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
 * @package    TechDivision\ApplicationServer
 * @subpackage Extractors
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    Open Software License (OSL 3.0) http://opensource.org/licenses/osl-3.0.php
 * @author     Johann Zelger <j.zelger@techdivision.com>
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

    /**
     * Checks if archive is extractable
     *
     * @param $archive \SplFileInfo The archive object
     *
     * @return bool
     */
    public function isExtractable(\SplFileInfo $archive);

    /**
     * Gathers all available archived webapps and extract them for usage.
     *
     * @return void
     */
    public function extractWebapps();

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive The archive file
     * @param string $flag The flag to set
     *
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag);
}

