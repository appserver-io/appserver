<?php

/**
 * TechDivision\ApplicationServer\AbstractReceiverTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Mock\MockApplication;
use TechDivision\ApplicationServer\Mock\MockContainer;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\AppserverNode;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;
use TechDivision\ApplicationServer\Api\Node\DeploymentNode;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Returns the system configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration The system configuration
     */
    public function getAppserverConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile('_files/appserver.xml');
        return $configuration;
    }

    /**
     * Returns a dummy container configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node The dummy configuration
     */
    public function getContainerConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile('_files/appserver_container.xml');
        return $configuration;
    }

    /**
     * Returns a dummy deployment configuration.
     *
     * @return \TechDivision\ApplicationServer\Configuration A dummy deployment configuration
     */
    public function getDeploymentConfiguration()
    {
        $configuration = new Configuration();
        $configuration->initFromFile('_files/appserver_container_deployment.xml');
        return $configuration;
    }

    /**
     * Returns a appserver node initialized with a mock system configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\AppserverNode The requested appserver node
     */
    public function getAppserverNode()
    {
        $appserverNode = new AppserverNode();
        $appserverNode->initFromConfiguration($this->getAppserverConfiguration());
        return $appserverNode;
    }

    /**
     * Returns a container node initialized with a mock container configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\ContainerNode The requested container node
     */
    public function getContainerNode()
    {
        $containerNode = new ContainerNode();
        $containerNode->initFromConfiguration($this->getContainerConfiguration());
        return $containerNode;
    }

    /**
     * Returns a deployment node initialized with a mock deployment configuration.
     *
     * @return \TechDivision\ApplicationServer\Api\Node\DeplyomentNode The requested deployment node
     */
    public function getDeploymentNode()
    {
        $deploymentNode = new DeploymentNode();
        $deploymentNode->initFromConfiguration($this->getDeploymentConfiguration());
        return $deploymentNode;
    }

    /**
     * Returns a mock container.
     *
     * @return \TechDivision\ApplicationServer\Mock\MockContainer The mock container
     */
    public function getMockContainer()
    {
        return new MockContainer($this->getMockInitialContext(), $this->getContainerNode(), $this->getMockApplications());
    }

    /**
     * Returns a mock application instance.
     *
     * @param string $applicationName
     *            The dummy application name
     * @return \TechDivision\ApplicationServer\MockApplication The initialized mock application
     */
    public function getMockApplication($applicationName)
    {
        return new MockApplication($this->getMockInitialContext(), $this->getContainerNode(), $applicationName);
    }

    /**
     * Returns an array with mock applications.
     *
     * @param integer $size
     *            The number of mock applications to be returned
     * @return array Array with mock applications
     */
    public function getMockApplications($size = 1)
    {
        $applications = array();
        for ($i = 0; $i < $size; $i ++) {
            $application = $this->getMockApplication("application-$i");
            $applications[$application->getName()] = $application;
        }
        return $applications;
    }

    /**
     * Returns a initial context instance with a mock configuration.
     *
     * @return \TechDivision\ApplicationServer\InitialContext Initial context with mock configuration
     */
    public function getMockInitialContext()
    {
        return new InitialContext($this->getAppserverNode());
    }

    /**
     * Returns a new socket pair to simulate a real socket implementation.
     *
     * @throws \Exception Is thrown if the socket pair can't be craeted
     * @return array The socket pair
     */
    public function getSocketPair()
    {

        // initialize the array for the socket pair
        $sockets = array();

        // on Windows we need to use AF_INET
        $domain = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? AF_INET : AF_UNIX);

        // setup and return a new socket pair
        if (socket_create_pair($domain, SOCK_STREAM, 0, $sockets) === false) {
            throw new \Exception("socket_create_pair failed. Reason: " . socket_strerror(socket_last_error()));
        }

        // return the array with the socket pair
        return $sockets;
    }
}