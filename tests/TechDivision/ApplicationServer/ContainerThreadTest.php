<?php

/**
 * TechDivision\ApplicationServer\ContainerThreadTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\MockContainerThread;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ContainerThreadTest extends \PHPUnit_Framework_TestCase {

    /**
     * The application instance to test.
     * @var \TechDivision\ApplicationServer\ContainerThread
     */
    protected $containerThread;
    
    /**
     * The initial context instance passed to the receiver.
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;
    
    /**
     * The configuration instance passed to the receiver.
     * @var \TechDivision\ApplicationServer\Configuration
     */
    protected $configuration;
    
	/**
	 * Initializes the application instance to test.
	 *
	 * @return void
	 */
	public function setUp() {
	    $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/appserver_initial_context.xml');
		$this->initialContext = new InitialContext($configuration);
		$this->configuration = $this->getContainerConfiguration();
		$this->containerThread = new ContainerThread($this->initialContext, $this->configuration);
	}
	
	/**
	 * Test's if the configuration instance passed to the constructor is returned by
	 * the getter method.
	 * 
	 * @return void
	 */
	public function testGetConfiguration()
	{
		// assertSame() doesn't work here because the ContainerThread extends from \Thread
	    $this->assertEquals($this->configuration, $this->containerThread->getConfiguration());
	}
	
	/**
	 * Test's if the configuration instance passed to the constructor is returned by
	 * the getter method.
	 * 
	 * @return void
	 */
	public function testGetDeployment()
	{
	    $this->assertInstanceOf('TechDivision\ApplicationServer\MockDeployment', $this->containerThread->getDeployment());
	}
    
	/**
	 * Checks if the new instance method works correctly.
	 * 
	 * @return void
	 */
	public function testNewInstance()
	{
	    $className = 'TechDivision\ApplicationServer\Configuration';
	    $this->assertInstanceOf($className, $this->containerThread->newInstance($className));
	}
	
	/**
	 * Test's the container thread's start method.
	 * 
	 * @return void
	 */
	public function testStart()
	{
	    $this->markTestSkipped('Seems to be a pthread error.');
	    $this->containerThread->start();
	    $this->containerThread->join();
	}
	
	/**
	 * Returns a dummy container configuration.
	 * 
	 * @return \TechDivision\ApplicationServer\Configuration The dummy configuration
	 */
	public function getContainerConfiguration() {
	    $configuration = new Configuration();
	    $configuration->initFromFile(__DIR__ . '/_files/appserver_container.xml');
	    $configuration->addChildWithNameAndValue('baseDirectory', '/opt/appserver');
	    return $configuration;
	}
}