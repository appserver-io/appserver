<?php 

/**
 * TechDivision\ApplicationServer\ServerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class ServerTest extends \PHPUnit_Framework_TestCase {
    
	/**
	 * The server instance to test.
	 * @var TechDivision\ApplicationServer\Server
	 */
	protected $server;

	/**
	 * Initializes the server instance to test.
	 *
	 * @return void
	 */
	public function setUp() {
	    $configuration = new Configuration();
	    $configuration->initFromFile(__DIR__ . '/_files/appserver.xml');
	    $configuration->addChildWithNameAndValue('baseDirectory', '/opt/appserver');
		$this->server = new Server($configuration);
	}
	
	/**
	 * Test if the initial context has successfully been initialized.
	 *
	 * @return void
	 */
	public function testGetInitialContext() {
	    $this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext', $this->server->getInitialContext());
	}
	
	/**
	 * Test if the configuration has been passed successfully.
	 *
	 * @return void
	 */
	public function testGetConfiguration() {
	    $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $this->server->getConfiguration());
	}
	
	/**
	 * Test if the base directory has been initialized.
	 *
	 * @return void
	 */
	public function testGetBaseDirectory() {
	    $this->assertEquals('/opt/appserver', $this->server->getBaseDirectory());
	}
	
	/**
	 * Test if the container configuration has been initialized.
	 *
	 * @return void
	 */
	public function testGetContainerConfiguration() {
	    $this->assertCount(3, $this->server->getContainerConfiguration());
	}
}