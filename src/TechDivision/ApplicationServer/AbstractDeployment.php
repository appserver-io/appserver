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
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The deployment node.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\DeploymentNode
     */
    protected $deploymentNode;

    /**
     * Array with the initialized applications.
     *
     * @var array
     */
    protected $applications = array();
    
    /**
     * The initial context instance.
     * 
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

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
     * @return \TechDivision\ApplicationServer\Api\Node\DeploymentNode The deployment node
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
        $appNode = $appService->loadByWebappPath($application->getWebappPath());

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

        // log a message that the app has been started
        $this->getInitialContext()->getSystemLogger()->debug(
            sprintf(
                'Successfully started app %s in container %s',
                $application->getName(),
                $application->getWebappPath(),
                $application->getContainerNode()->getName()
            )
        );

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
