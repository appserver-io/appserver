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
use TechDivision\ApplicationServer\MockReceiver;
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
	public function setUp()
	{
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
	    $this->markTestSkipped('Seems to be a pthread error.');
		$this->assertCount(1, $this->container->getApplications());
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
		$this->container->setApplications($applications);
		// assertSame() doesn't work here because the AbstractContainer extends a \Stackable
		$this->assertEquals($applications, $this->container->getApplications());
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
	 * Tests the if the receiver configuration specified in the
	 * configuration file is used by the container.
	 * 
	 * @return void
	 */
	public function testGetReceiverConfiguration()
	{
		$this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $this->container->getReceiverConfiguration());
	}
	
	/**
	 * Tests the if the receiver type specified in the configuration file 
	 * is used by the container.
	 * 
	 * @return void
	 */
	public function testGetReceiverType()
	{
		$this->assertEquals('TechDivision\ApplicationServer\MockReceiver', $this->container->getReceiverType());
	}
	
	/**
	 * Tests the if the worker type specified in the configuration file 
	 * is used by the container.
	 * 
	 * @return void
	 */
	public function testGetWorkerType()
	{
		$this->assertEquals('TechDivision\ApplicationServer\Socket\MockWorker', $this->container->getWorkerType());
	}
	
	/**
	 * Tests the if the thread type specified in the configuration file 
	 * is used by the container.
	 * 
	 * @return void
	 */
	public function testGetThreadType()
	{
		$this->assertEquals('TechDivision\ApplicationServer\Socket\MockRequest', $this->container->getThreadType());
	}
    
	/**
	 * Checks if the new instance method works as expected.
	 * 
	 * @return void
	 */
	public function testNewInstance()
	{
	    $className = 'TechDivision\ApplicationServer\Configuration';
	    $this->assertInstanceOf($className, $this->container->newInstance($className));
	}
	
	/**
	 * Checks if the container configuration passed with setter equals the one
	 * returned by the setter.
	 * 
	 * @return void
	 */
	public function testSetGetConfiguration()
	{
		$configuration = $this->getContainerConfiguration();
		$this->container->setConfiguration($configuration);
		// assertSame() doesn't work here because the AbstractContainer extends a \Stackable
		$this->assertEquals($configuration, $this->container->getConfiguration());
	}
	
	/**
	 * Test if the run method starts the receiver.
	 * 
	 * @return void
	 */
	public function testRun()
	{
	    $this->markTestSkipped('Seems to be a pthread error.');
		$this->container->run();
		$this->assertTrue($this->container->isStarted());
	}
	
	/**
	 * Checks if the context thread returns the initial context
	 * passed with the constructor.
	 * 
	 * @return void
	 */
	public function testGetInitialContext()
	{
		$this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext', $this->container->getInitialContext());
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