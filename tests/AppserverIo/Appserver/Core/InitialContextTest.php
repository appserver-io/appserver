<?php

/**
 * AppserverIo\Appserver\Core\InitialContextTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class InitialContextTest extends AbstractTest
{

    /**
     * The initial context instance to test.
     *
     * @var AppserverIo\Appserver\Core\InitialContext
     */
    protected $initialContext;

    /**
     * Initializes the configuration instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->initialContext = $this->getMockInitialContext();
    }

    /**
     * Test's the if the storage has been initialized successfully.
     *
     * @return void
     */
    public function testGetStorage()
    {
        $this->assertInstanceOf('TechDivision\Storage\StorageInterface', $this->initialContext->getStorage());
    }

    /**
     * Test's the if the class loader has been initialized successfully.
     *
     * @return void
     */
    public function testGetClassLoader()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\SplClassLoader', $this->initialContext->getClassLoader());
    }

    /**
     * Test's the if the attribute getter/setter works with a simple data type.
     *
     * @return void
     */
    public function testGetSetAttributeWithSimpleDataType()
    {
        $integerValue = 10;
        $this->initialContext->setAttribute('integerValue', $integerValue);
        $this->assertEquals($integerValue, $this->initialContext->getAttribute('integerValue'));
    }

    /**
     * Test's the if the method to remove a attribute works.
     *
     * @return void
     */
    public function testRemoveAttribute()
    {
        $integerValue = 10;
        $this->initialContext->setAttribute('integerValue', $integerValue);
        $this->assertEquals($integerValue, $this->initialContext->getAttribute('integerValue'));
        $this->initialContext->removeAttribute('integerValue');
        $this->assertFalse($this->initialContext->getAttribute('integerValue'));
    }

    /**
     * Test's the if the attribute getter/setter works with a object.
     *
     * @return void
     */
    public function testGetSetAttributeWithObject()
    {
        $stdClass = new \stdClass();
        $stdClass->test = 'Testvalue';
        $this->initialContext->setAttribute('stdClass', $stdClass);
        $this->assertEquals($stdClass->test, $this->initialContext->getAttribute('stdClass')->test);
    }

    /**
     * Test's if creating a new instance with a constructor argument works.
     *
     * @return void
     */
    public function testNewInstanceWithArgument()
    {
        $configuration = $this->initialContext->newInstance('TechDivision\Configuration\Configuration', array(
            $nodeName = 'test'
        ));
        $this->assertInstanceOf('TechDivision\Configuration\Configuration', $configuration);
        $this->assertEquals($nodeName, $configuration->getNodeName());
    }

    /**
     * Test the reflection API method.
     *
     * @return void
     */
    public function testNewReflectionClass()
    {
        $reflectionClass = $this->initialContext->newReflectionClass('TechDivision\Configuration\Configuration');
        $this->assertInstanceOf('\ReflectionClass', $reflectionClass);
    }
}