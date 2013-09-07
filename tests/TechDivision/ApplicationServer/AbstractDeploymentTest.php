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

use TechDivision\ApplicationServer\MockDeployment;
use TechDivision\ApplicationServer\ContainerThread;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class AbstractDeploymentTest extends \PHPUnit_Framework_TestCase {

    /**
     * The deployment instance to test.
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
	    $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');
		$initialContext = new InitialContext($configuration);
		$containerThread = new ContainerThread($initialContext, $this->getContainerConfiguration());
		$this->deployment = new MockDeployment($initialContext, $containerThread);
	}
	
	/**
	 * Checks if the container thread passed to the constructor
	 * is returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetContainerThread()
	{
		$this->assertInstanceOf('TechDivision\ApplicationServer\ContainerThread', $this->deployment->getContainerThread());
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
		$this->deployment->setApplications($applications);
		$this->assertSame($applications, $this->deployment->getApplications());
	}
	
	/**
	 * Returns a dummy container configuration.
	 * 
	 * @return \TechDivision\ApplicationServer\Configuration The dummy configuration
	 */
	public function getContainerConfiguration()
	{
	    $configuration = new Configuration();
	    $configuration->initFromFile(__DIR__ . '/_files/appserver_container.xml');
	    $configuration->addChildWithNameAndValue('baseDirectory', '/opt/appserver');
	    return $configuration;
	}
	
	/**
	 * Returns a mock application instance.
	 * 
	 * @param string $applicationName The dummy application name
	 * @return \TechDivision\ApplicationServer\MockApplication The initialized mock application
	 */
	public function getMockApplication($applicationName)
	{
		$configuration = new Configuration();
		$configuration->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');
		$initialContext = new InitialContext($configuration);
		$application = new MockApplication($initialContext, $applicationName);
		$application->setConfiguration($this->getContainerConfiguration());
		return $application;
	}
	
	/**
	 * Returns an array with mock applications.
	 * 
	 * @param integer $size The number of mock applications to be returned
	 * @return array Array with mock applications
	 */
	public function getMockApplications($size = 1)
	{
		$applications = array();
		for ($i = 0; $i < $size; $i++) {
			$application = $this->getMockApplication("application-$i");
			$applications[$application->getName()] = $application;
		}
		return $applications;
	}
}