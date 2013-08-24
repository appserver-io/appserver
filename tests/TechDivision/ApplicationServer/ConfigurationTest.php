<?php 

/**
 * TechDivision\ApplicationServer\ConfigurationTest
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
class ConfigurationTest extends \PHPUnit_Framework_TestCase {

	/**
	 * The configuration instance to test.
	 * @var TechDivision\ApplicationServer\Configuration
	 */
	protected $configuration;

	/**
	 * Initializes the configuration instance to test.
	 *
	 * @return void
	 */
	public function setUp() {
		$this->configuration = new Configuration();
	}
	
	/**
	 * Test if a manually added configuration instance
	 * will has been added correctly.
	 *
	 * @return void
	 */
	public function testHasChildrenByAddingOneManually() {
		$child = new Configuration('foo');
		$this->configuration->addChild($child);
		$this->assertTrue($this->configuration->hasChildren());
	}
	
	/**
	 * Test if a manually added configuration instance
	 * will has been added correctly.
	 *
	 * @return void
	 */
	public function testGetChildrenByAddingOneManually() {
		$child = new Configuration('foo');
		$this->configuration->addChild($child);
		$this->assertSame(array($child), $this->configuration->getChildren());
	}
	
	/**
	 * Test if a configuration init with a SimpleXMLElement
	 * has been added correctly.
	 *
	 * @return void
	 */
	public function testHasChildrenByInitWithSimpleXmlElement() {
		$this->configuration->init($this->getTestNode());
		$this->assertTrue($this->configuration->hasChildren());
	}
	
	/**
	 * Test if a configuration init with a SimpleXMLElement
	 * has been added correctly.
	 *
	 * @return void
	 */
	public function testGetChildrenByInitWithSimpleXmlElement() {
		$this->configuration->init($this->getTestNode('test', 'testValue'));
		$toBeTested = new Configuration('testNode');
		$toBeTested->setAttr('test');
		$toBeTested->setValue('testValue');
		$this->assertEquals(array($toBeTested), $this->configuration->getChildren());
	}

    public function testLoadFromFile() {

        $this->configuration->initFromFile(__DIR__ . '/_files/META-INF/appserver-ds.xml');

        $driver = $this->configuration->getChild('/datasources/datasource/database/driver');
        $user = $this->configuration->getChild('/datasources/datasource/database/user');
        $password = $this->configuration->getChild('/datasources/datasource/database/password');
        $databaseName = $this->configuration->getChild('/datasources/datasource/database/databaseName');

        $this->assertEquals('pdo_mysql', $driver);
        $this->assertEquals('appserver', $user);
        $this->assertEquals('appserver', $password);
        $this->assertEquals('appserver_ApplicationServer', $databaseName);
    }
	
	/**
	 * Creates a SimpleXMLElement representing a test
	 * configuration element.
	 *
	 * @return SimpleXMLElement The test configuration element
	 */
	protected function getTestNode($attr = NULL, $value = NULL) {
		return new \SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>
			 <test>
			   <testNode attr="' . $attr . '">' . $value . '</testNode>
			 </test>'
		);
	}
	
	/**
	 * Tests the getData() method with an existing key.
	 *
	 * @return void
	 */
	public function testGetDataAvailable() {
		$this->configuration->setData('foo', 'bar');
		$this->assertEquals('bar', $this->configuration->getData('foo'));
	}
	
	/**
	 * Tests the getData() method with a not existing key.
	 *
	 * @return void
	 */
	public function testGetDataNotAvailable() {
		$this->assertNull($this->configuration->getData('foo'));
	}
	
	/**
	 * Tests the configuration's equals method be return FALSE if
	 * not a reference has been passed.
	 * 
	 * @return void
	 */
	public function testEqualsAndExpectFalse() {
	    $this->configuration->init($this->getTestNode('test', 'testValue'));
	    $configuration = new Configuration();
	    $configuration->init($this->getTestNode('test', 'testValue'));
	    $this->assertFalse($this->configuration->equals($configuration));
	}
	
	/**
	 * Tests the configuration's equals method be return TRUE if
	 * a reference has been passed.
	 * 
	 * @return void
	 */
	public function testEqualsAndExpectTrue() {
	    $this->configuration->init($this->getTestNode('test', 'testValue'));
	    $this->assertTrue($this->configuration->equals($this->configuration));
	}
	
	/**
	 * Tests if NULL will be returned if an invalid configuration
	 * path has been requested.
	 * 
	 * @return void
	 */
	public function testGetChildWithInvalidPath() {
	    $this->assertNull($this->configuration->getChild('/invalid/path'));
	}
	
	/**
	 * Tests if the method returns FALSE if the configuration
	 * has NO children.
	 * 
	 * @return void
	 */
	public function testHasChildrenWithEmptyConfiguration() {
	    $this->assertFalse($this->configuration->hasChildren());
	}
	
	/**
	 * Tests if the method returns TRUE if the configuration
	 * HAS children.
	 * 
	 * @return void
	 */
	public function testHasChildrenWithConfiguration() {
	    $this->configuration->init($this->getTestNode('test', 'testValue'));
	    $this->assertTrue($this->configuration->hasChildren());
	}
	
	/**
	 * Tests if the magic function __call throws an exception if invoked
	 * nor with a getter or setter.
	 * 
	 * @return void
	 * @expectedException \Exception
	 */
	public function testCallMagicFunctionNoGetterOrSetter() {
	    $this->configuration->someUnknownPath();
	}
	
	/**
	 * Tests if the magic function __call returns NULL if invoked
	 * with a non existing child or attribute name.
	 * 
	 * @return void
	 */
	public function testCallMagicFunctionWithInvalidPath() {
	    $this->assertNull($this->configuration->getSomeUnknownPath());
	}
	
	/**
	 * Tests if the magic function __call returns the configuration value
	 * if invoked with a existing child name.
	 * 
	 * @return void
	 */
	public function testCallMagicFunctionWithValidChildName() {
	    $this->configuration->init($this->getTestNode('test', 'testValue'));
	    $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $this->configuration->getTestNode());
	}
	/**
	 * Tests if the magic function __call returns the configuration value
	 * if invoked with a existing child name.
	 * 
	 * @return void
	 */
	public function testCallMagicFunctionWithValidAttributeName() {
	    $this->configuration->init($this->getTestNode($testAttrValue = 'test', 'testValue'));
	    $this->assertEquals($testAttrValue, $this->configuration->getTestNode()->getAttr());
	}
}