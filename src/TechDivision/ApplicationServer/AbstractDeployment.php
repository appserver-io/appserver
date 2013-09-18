<?php

/**
 * TechDivision\ApplicationServer\Deployment
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Interfaces\DeploymentInterface;

/**
 *
 * @package TechDivision\MessageQueue
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractDeployment implements DeploymentInterface
{

    /**
     * Path to the container's base directory.
     *
     * @var string
     */
    const XPATH_CONTAINER_BASE_DIRECTORY = '/container/baseDirectory';

    /**
     * Path to the container's host configuration.
     *
     * @var string
     */
    const XPATH_CONTAINER_HOST = '/container/host';

    /**
     * The container thread
     *
     * @var \TechDivision\ApplicationServer\ContainerThread
     */
    protected $containerThread;

    /**
     * Array with the initialized applications.
     *
     * @var array
     */
    protected $applications;

    /**
     * Initializes the deployment with the container thread.
     *
     * @param \TechDivision\ApplicationServer\ContainerThread $containerThread
     */
    public function __construct($initialContext, $containerThread)
    {
        $this->initialContext = $initialContext;
        $this->containerThread = $containerThread;
    }

    /**
     * Returns the container thread.
     *
     * @return \TechDivision\ApplicationServer\ContainerThread The container thread
     */
    public function getContainerThread()
    {
        return $this->containerThread;
    }

    /**
     * Set's the deployed applications.
     *
     * @param array $applications
     *            The deployed applications
     * @return void
     */
    public function setApplications(array $applications)
    {
        $this->applications = $applications;
    }

    /**
     * Return's the deployed applications.
     *
     * @return array The deployed applications
     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Creates a new instance of the passed class name and passes the
     * args to the instance constructor.
     *
     * @param string $className
     *            The class name to create the instance of
     * @param array $args
     *            The parameters to pass to the constructor
     * @return object The created instance
     */
    public function newInstance($className, array $args = array())
    {
        return $this->initialContext->newInstance($className, $args);
    }

    /**
     * Extracts the passed PHAR archive to a folder with the
     * basename of the archive file.
     *
     * @param \SplFileInfo $archive The PHAR file to be deployed
     * @return void
     */
    protected function deployArchive(\SplFileInfo $archive)
    {
        try {

            // create folder name based on the archive's basename
            $folderName = $this->getAppBase() . DIRECTORY_SEPARATOR . $archive->getBaseName('.phar');

            // check if application has already been deployed
            if (is_dir($folderName) === false) {
                $p = new \Phar($archive);
                $p->extractTo($folderName, null, true);
            }

        } catch (\Exception $e) {
            error_log($e->__toString());
        }
    }

    /**
     * Gathers all available archived webapps and deploys them for usage.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance itself
     */
    public function deployWebapps()
    {
        foreach (new \RegexIterator(new \FilesystemIterator($this->getAppBase()), '/^.*\.phar$/') as $archive) {
            $this->deployArchive($archive);
        }
        return $this;
    }

    /**
     * Returns the path to the appserver webapp base directory.
     *
     * @return string The path to the appserver webapp base directory
     */
    public function getAppBase()
    {
        $configuration = $this->getContainerThread()->getConfiguration();
        $baseDir = $configuration->getChild(self::XPATH_CONTAINER_BASE_DIRECTORY)->getValue();
        $appBase = $configuration->getChild(self::XPATH_CONTAINER_HOST)->getAppBase();
        return $baseDir . $appBase;
    }
}