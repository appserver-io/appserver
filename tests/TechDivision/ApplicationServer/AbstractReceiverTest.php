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

use TechDivision\ApplicationServer\MockApplication;
use TechDivision\ApplicationServer\MockContainer;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class AbstractReceiverTest extends \PHPUnit_Framework_TestCase {

    /**
     * The receiver instance to test.
     * @var \TechDivision\ApplicationServer\MockReceiver
     */
    protected $receiver;
    
    /**
     * The initial context instance passed to the receiver.
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;
    
    /**
     * The container instance passed to the receiver.
     * @var \TechDivision\ApplicationServer\MockContainer
     */
    protected $container;
    
	/**
	 * Initializes the application instance to test.
	 *
	 * @return void
	 */
	public function setUp()
	{
	    $configuration = new Configuration();
	    $configuration->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');
	    $this->initialContext = new InitialContext($configuration);
	    $this->container = new MockContainer($this->initialContext, $this->getContainerConfiguration(), $this->getMockApplications());
	    $this->receiver = new MockReceiver($this->initialContext, $this->container);
	}
	
	/**
	 * Test's if the container instance passed with the constructor
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetContainer()
	{
	    $this->assertSame($this->container, $this->receiver->getContainer());
	}
	
	/**
	 * Test's that the thread type specified in the configuration file  
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetThreadType()
	{
	    $this->assertSame('TechDivision\ApplicationServer\Socket\MockRequest', $this->receiver->getThreadType());
	}
	
	/**
	 * Test's that the worker type specified in the configuration file 
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetWorkerType()
	{
	    $this->assertSame('TechDivision\ApplicationServer\Socket\MockWorker', $this->receiver->getWorkerType());
	}
	
	/**
	 * Test's that the worker number specified in the configuration file
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetWorkerNumber()
	{
	    $this->assertSame(16, $this->receiver->getWorkerNumber());
	}
	
	/**
	 * Test's that the worker number specified in the configuration file
	 * returns null if not specified.
	 * 
	 * @return void
	 */
	public function testGetWorkerNumberWithMissingConfiguration()
	{
	    $this->receiver->getConfiguration()->removeChilds(MockReceiver::XPATH_CONFIGURATION_PARAMETERS);
	    $this->assertNull($this->receiver->getWorkerNumber());
	}
	
	/**
	 * Test's that the address specified in the configuration file
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetAddress()
	{
	    $this->assertSame('0.0.0.0', $this->receiver->getAddress());
	}
	
	/**
	 * Test's that the address specified in the configuration file
	 * returns null if not specified.
	 * 
	 * @return void
	 */
	public function testGetAddressWithMissingConfiguration()
	{
	    $this->receiver->getConfiguration()->removeChilds(MockReceiver::XPATH_CONFIGURATION_PARAMETERS);
	    $this->assertNull($this->receiver->getAddress());
	}
	
	/**
	 * Test's that the port specified in the configuration file
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetPort()
	{
	    $this->assertSame(8586, $this->receiver->getPort());
	}
	
	/**
	 * Test's that the port specified in the configuration file
	 * returns null if not specified.
	 * 
	 * @return void
	 */
	public function testGetPortWithMissingConfiguration()
	{
	    $this->receiver->getConfiguration()->removeChilds(MockReceiver::XPATH_CONFIGURATION_PARAMETERS);
	    $this->assertNull($this->receiver->getPort());
	}
	
	/**
	 * Test's that garbage collection returns the number of
	 * collected cycles.
	 * 
	 * @return void
	 */
	public function testGc()
	{
	    gc_collect_cycles(); // to make sure that garbage collection returns zero cycles
	    $this->assertSame(0, $this->receiver->gc());
	}
	
	/**
	 * Test's that the method to enable the garbage collection
	 * works as expected.
	 * 
	 * @return void
	 */
	public function testGcEnable()
	{
	    gc_disable();
	    $this->assertFalse(gc_enabled());
	    $this->receiver->gcEnable();
	    $this->assertTrue(gc_enabled());
	}
	
	/**
	 * Test's that the method to disable the garbage collection
	 * works as expected.
	 * 
	 * @return void
	 */
	public function testGcDisable()
	{
	    gc_enable();
	    $this->assertTrue(gc_enabled());
	    $this->receiver->gcDisable();
	    $this->assertFalse(gc_enabled());
	}
	
	/**
	 * Test's that the method to check if the garbage collection
	 * is enabled works as expected.
	 * 
	 * @return void
	 */
	public function testGcEnabled()
	{
	    $this->receiver->gcDisable();
	    $this->assertFalse($this->receiver->gcEnabled());
	    $this->receiver->gcEnable();
	    $this->assertTrue($this->receiver->gcEnabled());
	}
	
	/**
	 * Test's that the receiver creates an instance of the
	 * worker class defined in the configuration file.
	 * 
	 * @return void
	 */
	public function testNewWorker()
	{
	    list ($client, $server) = $this->getSocketPair();
	    $this->assertInstanceOf('TechDivision\ApplicationServer\Socket\MockWorker', $this->receiver->newWorker($server));
	}
    
	/**
	 * Checks if the new instance method works correctly.
	 * 
	 * @return void
	 */
	public function testNewInstance() {
	    $className = 'TechDivision\ApplicationServer\Configuration';
	    $this->assertInstanceOf($className, $this->receiver->newInstance($className));
	}
	
	/**
	 * Check's if the initial context instance passed with the constructor
	 * is the same as returned by the getter.
	 * 
	 * @return void
	 */
	public function testGetInitialContext()
	{
	    $this->assertSame($this->initialContext, $this->receiver->getInitialContext());
	}
	
	/**
	 * Test's the start method.
	 * 
	 * @return void
	 */
	public function testStart()
	{
	    $this->markTestSkipped('Seems to be a pthread error.');
	    $this->assertTrue($this->receiver->start());
	}
	
	/**
	 * Test's the start method when socket can't be created.
	 * 
	 * @return void
	 */
	public function testStartWhenSocketCantBeCreated()
	{
	    $this->markTestSkipped('Seems to be a pthread error.');
	    $this->receiver->setResourceClass('TechDivision\ApplicationServer\Socket\MockServerThatCantCreateSocket');
	    $this->assertFalse($this->receiver->start());
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