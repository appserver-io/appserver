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
     * The container's configuration
     *
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;

    /**
     * The mutex to prevent parallel deployment of PHAR files.
     *
     * @var \Mutex
     */
    protected $mutex;

    /**
     * Set's the configuration with the container information to be started in the thread.
     *
     * @param \TechDivision\ApplicationServer\Configuration $configuration
     *            The container's configuration
     */
    public function init($configuration, $mutex)
    {
        $this->configuration = $configuration;
        $this->mutex = $mutex;
    }

    /**
     *
     * @see AbstractContextThread::run()
     */
    public function main()
    {

        // load the container configuration
        $configuration = $this->getConfiguration();

        // load the container type and deploy the applications
        $containerType = $configuration->getType();

        // lock the mutex to prevent other containers to parallel deploy PHAR files
        \Mutex::lock($this->mutex);

        // deploy the applications and return them in an array
        $applications = $this->getDeployment()
            ->deployWebapps()
            ->deploy()
            ->getApplications();

        // create configuration nodes for applications
        $applicationConfiguration = $this->newInstance('TechDivision\ApplicationServer\Configuration');
        $applicationConfiguration->setNodeName('applications');
        foreach ($applications as $application) {
            $applicationConfiguration->addChild($application->newConfiguration());
        }

        // add applications to container/system configuration
        $configuration->addChild($applicationConfiguration);
        $this->mergeInSystemConfiguration($applicationConfiguration);

        // unlock the mutex to allow other containers own deployment
        \Mutex::unlock($this->mutex);

        // create and start the container instance
        $containerInstance = $this->newInstance($containerType, array(
            $this->getInitialContext(),
            $configuration,
            $applications
        ));

        $containerInstance->run();
    }

    /**
     * Merge the passed application configurations into the system configuration
     * and refreshes it in the initial context, to make it available to the API.
     *
     * @param \TechDivision\ApplicationServer\Configuration $applicationConfigurations
     *            The application configurations to merge into the system configuration
     */
    public function mergeInSystemConfiguration(Configuration $applicationConfiguration)
    {
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();
        foreach ($systemConfiguration->getChilds(self::XPATH_CONTAINERS) as $containerConfiguration) {
            $containerConfiguration->addChild($applicationConfiguration);
        }
        $this->getInitialContext()->setSystemConfiguration($systemConfiguration);
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
        return $this->getInitialContext()->newInstance($className, $args);
    }

    /**
     * The configuration found in the cfg/appserver.xml file.
     *
     * @return \TechDivision\ApplicationServer\Configuration The configuration instance
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     *
     * @return object
     */
    public function getDeployment()
    {
        $deploymentType = $this->getConfiguration()
            ->getChild(self::XPATH_CONTAINER_DEPLOYMENT)
            ->getType();
        return $this->newInstance($deploymentType, array(
            $this->getInitialContext(),
            $this
        ));
    }
}