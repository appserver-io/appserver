<?php

/**
 * \AppserverIo\Appserver\Core\Interfaces\ExtractorInterface
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

namespace AppserverIo\Appserver\Core\Interfaces;

use AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface;

/**
 * Interface for extractor implementations.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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
    const FLAG_UNDEPLOYING = '.undeploying';
    const FLAG_UNDEPLOYED = '.undeployed';

    /**
     * Filename of the hidden file that contains timestamp with last successful deployment.
     *
     * @var string
     */
    const FILE_DEPLOYMENT_SUCCESSFULL = '.appserver-last-successfull-deployment';

    /**
     * Gathers all available archived webapps and extract them for usage.
     *
     * @return void
     */
    public function deployWebapps();

    /**
     * Soaks the passed archive from a location in the filesystem
     * into the deploy directory and prepares it for the next
     * restart by setting the appropriate flag.
     *
     * @param \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode The container the archive belongs to
     * @param \SplFileInfo                                                                     $archive       The archive to be soaked
     *
     * @return void
     */
    public function soakArchive(ContainerConfigurationInterface $containerNode, \SplFileInfo $archive);

    /**
     * Extracts the passed archive to a folder with the
     * basename of the archive file.
     *
     * @param \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode The container the archive belongs to
     * @param \SplFileInfo                                                                     $archive       The archive file to be deployed
     *
     * @throws \Exception
     * @return void
     */
    public function deployArchive(ContainerConfigurationInterface $containerNode, \SplFileInfo $archive);

    /**
     * Un-deploys the passed archive after backing up
     * files that are NOT part of the archive.
     *
     * @param \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode The container the archive belongs to
     * @param \SplFileInfo                                                                     $archive       The archive file to be un-deployed
     *
     * @throws \Exception
     * @return void
     */
    public function undeployArchive(ContainerConfigurationInterface $containerNode, \SplFileInfo $archive);

    /**
     * Checks if archive is deployable.
     *
     * @param \SplFileInfo $archive The archive object
     *
     * @return bool
     */
    public function isDeployable(\SplFileInfo $archive);

    /**
     * Check if archive is undeployable.
     *
     * @param \SplFileInfo $archive The archive object
     *
     * @return bool
     */
    public function isUndeployable(\SplFileInfo $archive);

    /**
     * Flags the archive in specific states of extraction
     *
     * @param \SplFileInfo $archive The archive file
     * @param string       $flag    The flag to set
     *
     * @return void
     */
    public function flagArchive(\SplFileInfo $archive, $flag);

    /**
     * Deletes all old flags, so the app will be un-deployed with
     * the next appserver restart.
     *
     * @param \SplFileInfo $archive The archive to un-flag
     *
     * @return void
     */
    public function unflagArchive(\SplFileInfo $archive);

    /**
     * Returns the archive extension suffix e.g. .phar
     *
     * @return string the archive extension suffix
     */
    public function getExtensionSuffix();

    /**
     * Returns the extractor node configuration data.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ExtractorNodeInterface The extractor node configuration data
     */
    public function getExtractorNode();
}
