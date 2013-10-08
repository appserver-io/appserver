<?php

/**
 * TechDivision\ApplicationServer\ContainerThread
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\SplClassLoader;
use TechDivision\ApplicationServer\AbstractContextThread;
use TechDivision\ApplicationServer\Configuration;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 * @author Johann Zelger <jz@techdivision.com>
 */
class ContainerThread extends AbstractContextThread
{

    /**
     * The container's to be deployed.
     *
     * @var \TechDivision\ApplicationServer\Api\Node\ContainerNode
     */
    protected $containerNode;

    /**
     * The mutex to prevent parallel deployment of PHAR files.
     *
     * @var \Mutex
     */
    protected $mutex;

    /**
     * Set's the unique container name to be started by this thread.
     *
     * @param \Mutex $mutex
     *            The mutex that locks related threads when deployment starts
     * @param string $containerNode
     *            The container node
     * @return void
     */
    public function init($mutex, $containerNode)
    {
        $this->mutex = $mutex;
        $this->containerNode = $containerNode;
    }

    /**
     *
     * @see AbstractContextThread::run()
     */
    public function main()
    {

        // lock the mutex to prevent other containers to parallel deploy PHAR files
        \Mutex::lock($this->mutex);

        // deploy the applications and return them as array
        $applications = $this->getDeployment()
            ->deployWebapps()
            ->deploy()
            ->getApplications();

        // unlock the mutex to allow other containers own deployment
        \Mutex::unlock($this->mutex);

        // load the container node
        $containerNode = $this->getContainerNode();

        // create the container instance
        $containerInstance = $this->newInstance($containerNode->getType(), array(
            $this->getInitialContext(),
            $containerNode,
            $applications
        ));

        // finally start the container instance
        $containerInstance->run();
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
     * @see \TechDivision\ApplicationServer\InitialContext::newInstance()
     */
    public function newInstance($className, array $args = array())
    {
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * Return's the container node.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The container node
     */
    public function getContainerNode()
    {
        return $this->containerNode;
    }

    /**
     * Returns the deployment interface for the container for
     * this container thread.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance for this container thread
     */
    public function getDeployment()
    {
        $deploymentNode = $this->getContainerNode()->getDeployment();
        return $this->newInstance($deploymentNode->getType(), array(
            $this->getInitialContext(),
            $this->getContainerNode(),
            $deploymentNode
        ));
    }
}