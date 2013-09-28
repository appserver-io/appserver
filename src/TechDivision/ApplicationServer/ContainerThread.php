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
     * Path to the container's deployment configuration.
     *
     * @var string
     */
    const XPATH_CONTAINER_DEPLOYMENT = '/container/deployment';

    /**
     * XPath expression for the container configurations.
     *
     * @var string
     */
    const XPATH_CONTAINERS = '/appserver/containers/container';

    /**
     * The container's unique ID.
     *
     * @var string
     */
    protected $id;

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
     * @param string $id
     *            The unique container ID
     * @return void
     */
    public function init($mutex, $id)
    {
        $this->mutex = $mutex;
        $this->id = $id;
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

        // load the container configuration
        $containerService = $this->newService('TechDivision\ApplicationServer\Api\ContainerService');
        $container = $containerService->load($this->getId());

        // unlock the mutex to allow other containers own deployment
        \Mutex::unlock($this->mutex);

        // create the container instance
        $containerInstance = $this->newInstance($container->type, array(
            $this->getInitialContext(),
            $this->getId(),
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
     * The unique container ID.
     *
     * @return string The unique container ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the deployment class name from the system configuration.
     *
     * @return string The deployment class name
     */
    public function getDeploymentType()
    {
        $deploymentService = $this->newService('TechDivision\ApplicationServer\Api\DeploymentService');
        return $deploymentService->loadByContainerId($this->getId())->deployment->type;
    }

    /**
     * Returns the deployment interface for the container for
     * this container thread.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\DeploymentInterface The deployment instance for this container thread
     */
    public function getDeployment()
    {
        return $this->newInstance($this->getDeploymentType(), array(
            $this->getInitialContext(),
            $this->getId()
        ));
    }
}