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
     * The container node the deployment is for.
     *
     * @var string
     */
    protected $containerNode;

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
     * @param \TechDivision\ApplicationServer\Api\Node\ContainerNode $containerNode
     *            The container node the deployment is for
     * @param \TechDivision\ApplicationServer\Api\Node\DeploymentNode $deploymentNode
     *            The deployment node
     * @return void
     */
    public function __construct(InitialContext $initialContext, $containerNode, $deploymentNode)
    {
        $this->initialContext = $initialContext;
        $this->containerNode = $containerNode;
        $this->deploymentNode = $deploymentNode;
    }

    /**
     * Returns the initialContext instance
     *
     * @return \TechDivision\ApplicationServer\InitialContext The initial context instance
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }

    /**
     * Returns the container node the deployment is for.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The container node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the deployment node.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The deployment node
     */
    public function getDeploymentNode()
    {
        return $this->deploymentNode;
    }

    /**
     * Append the deployed application to the deployment instance
     * and registers it in the system configuration.
     *
     * @param ApplicationInterface $application
     *            The application to append
     * @return void
     */
    public function addApplication(ApplicationInterface $application)
    {

        // create a new API app service instance
        $appService = $this->newService('TechDivision\ApplicationServer\Api\AppService');
        $appNode = $appService->load($application->getWebappPath());

        // check if the application has already been attached to the container
        if ($appNode == null) {
            $application->newAppNode($this->getContainerNode());
        } else {
            $application->setAppNode($appNode);
        }

        // persist the application
        $appService->persist($application->getAppNode());

        // connect the application to the container
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
            $baseDirectory = $this->getBaseDirectory($this->getAppBase());
            $folderName = $baseDirectory . DIRECTORY_SEPARATOR . $archive->getBaseName('.phar');

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
        foreach (new \RegexIterator(new \FilesystemIterator($this->getBaseDirectory($this->getAppBase())), '/^.*\.phar$/') as $archive) {
            $this->deployArchive($archive);
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getBaseDirectory()
     */
    public function getBaseDirectory($directoryToAppend = null)
    {
        return $this->newService('TechDivision\ApplicationServer\Api\ContainerService')->getBaseDirectory($directoryToAppend);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \TechDivision\ApplicationServer\Api\ContainerService::getAppBase()
     */
    public function getAppBase()
    {
        return $this->getContainerNode()->getHost()->getAppBase();
    }
}