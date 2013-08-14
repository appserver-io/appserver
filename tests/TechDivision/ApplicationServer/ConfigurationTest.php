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

        $configuration = Configuration::loadFromFile(__DIR__ . '/_files/appserver-ds.xml');

        $driver = $configuration->getChild('/datasources/datasource/database/driver');
        $user = $configuration->getChild('/datasources/datasource/database/user');
        $password = $configuration->getChild('/datasources/datasource/database/password');
        $databaseName = $configuration->getChild('/datasources/datasource/database/databaseName');

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
}