<?php

/**
 * TechDivision\ApplicationServer\AbstractContainerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\MockApplication;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class AbstractContainerTest extends \PHPUnit_Framework_TestCase {

    /**
     * The application instance to test.
     * @var \TechDivision\ApplicationServer\MockContainer
     */
    protected $container;
    
	/**
	 * Initializes the container instance to test.
	 *
	 * @return void
	 */
	public function setUp() {
	    $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');
		$initialContext = new InitialContext($configuration);
		$this->container = new MockContainer($initialContext, $this->getContainerConfiguration(), $this->getMockApplications());
	}
	
	/**
	 * Checks if the number of applications equals the number that has been 
	 * passed to the constructor.
	 * 
	 * @return void
	 */
	public function testGetApplicationsFromConstructor()
	{
		$this->assertCount(1, $this->container->getApplications());
	}
	
	/**
	 * Checks if the number of applications equals to the number that has been 
	 * passed to the setter.
	 * 
	 * @return void
	 */
	public function testGetSetApplications()
	{
		$applications = $this->getMockApplications($size = 3);
		$this->container->setApplications($applications);
		$this->assertCount($size, $this->container->getApplications());
	}
	
	/**
	 * Checks if the receiver instance specified in the configuration has 
	 * been returned.
	 * 
	 * @return void
	 */
	public function testGetReceiver()
	{
		$this->assertInstanceOf('TechDivision\ApplicationServer\Interfaces\ReceiverInterface', $this->container->getReceiver());
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