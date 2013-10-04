<?php

/**
 * TechDivision\ApplicationServer\AbstractDeploymentTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Mock\MockDeployment;
use TechDivision\ApplicationServer\ContainerThread;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;
use TechDivision\ApplicationServer\Api\Node\DeploymentNode;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractDeploymentTest extends AbstractTest
{

    /**
     * The deployment instance to test.
     *
     * @var \TechDivision\ApplicationServer\MockDeployment
     */
    protected $deployment;

    /**
     * Initializes the container instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $initialContext = $this->getMockInitialContext();
        $containerThread = new ContainerThread($initialContext, $this->getContainerConfiguration(), \Mutex::create(false));
        $configuration = new Configuration();
        $configuration->initFromFile('_files/appserver_container.xml');
        $containerNode = new ContainerNode();
        $containerNode->initFromConfiguration($configuration);
        $deploymentNode = new DeploymentNode();
        $deploymentNode->initFromConfiguration($containerNode->getDeployment());
        $this->deployment = new MockDeployment($initialContext, $containerNode, $deploymentNode);
    }

    /**
     * Checks if the new instance method works as expected.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\ApplicationServer\Configuration';
        $this->assertInstanceOf($className, $this->deployment->newInstance($className));
    }

    /**
     * Checks if the number of applications equals to the number that has been
     * passed to the setter.
     *
     * @return void
     */
    public function testSetGetApplications()
    {
        $applications = $this->getMockApplications($size = 3);
        foreach ($applications as $application) {
            $this->deployment->addApplication($application);
        }
        $this->assertSame($applications, $this->deployment->getApplications());
    }
}