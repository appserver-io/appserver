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
use TechDivision\ApplicationServer\Interfaces\ApplicationInterface;

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
     * The container's unique ID.
     *
     * @var string
     */
    protected $id;

    /**
     * Array with the initialized applications.
     *
     * @var array
     */
    protected $applications;

    /**
     * Initializes the deployment with the container thread.
     *
     * @param \TechDivision\ApplicationServer\InitialContext $initialContext
     *            The initial context instance
     * @param string $id
     *            The container's unique ID
     * @return void
     */
    public function __construct(InitialContext $initialContext, $id)
    {
        $this->initialContext = $initialContext;
        $this->id = $id;
    }

    /**
     * Returns the initialContext object
     *
     * @return \Stackable
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Append the deployed application to the deployment instance
     * and registers it in the system configuration.
     *
     * @param ApplicationInterface $application The application to append
     * @return void
     */
    public function addApplication(ApplicationInterface $application)
    {
        // create a new API application service instance
        $applicationService = $this->newService('TechDivision\ApplicationServer\Api\ApplicationService');

        // append the application in the system configuration first and connect it to the container
        $applicationService->create($application->toStdClass());
        $newApplication = $applicationService->loadByName($application->getName())->app;
        $application->setId($newApplication->id);
        $application->connect();

        // register the application in this instance
        $this->applications[$application->getName()] = $application;
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
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
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
     * Extracts the passed PHAR archive to a folder with the
     * basename of the archive file.
     *
     * @param \SplFileInfo $archive
     *            The PHAR file to be deployed
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
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ContainerService')->getAppBase($this->getId());
    }

    /**
     * The unique container ID.
     *
     * @return string The unique container ID
     */
    public function getId()
    {
        return $this->id;
    }
}