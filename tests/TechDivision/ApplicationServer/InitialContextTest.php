<?php

/**
 * TechDivision\ApplicationServer\InitialContextTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

/**
 *
 * @package TechDivision\ApplicationServer
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
     * @var TechDivision\ApplicationServer\InitialContext
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
        $this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext\StorageInterface', $this->initialContext->getStorage());
    }

    /**
     * Test's the if the class loader has been initialized successfully.
     *
     * @return void
     */
    public function testGetClassLoader()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\SplClassLoader', $this->initialContext->getClassLoader());
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
        $configuration = $this->initialContext->newInstance('TechDivision\ApplicationServer\Configuration', array(
            $nodeName = 'test'
        ));
        $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $configuration);
        $this->assertEquals($nodeName, $configuration->getNodeName());
    }

    /**
     * Test the reflection API method.
     *
     * @return void
     */
    public function testNewReflectionClass()
    {
        $reflectionClass = $this->initialContext->newReflectionClass('TechDivision\ApplicationServer\Configuration');
        $this->assertInstanceOf('\ReflectionClass', $reflectionClass);
    }

    /**
     * Test if the method returns the correct bean annotation for a singleton
     * session bean.
     *
     * @return void
     */
    public function testGetBeanAnnotationWithoutBeanTypesDeclared()
    {
        $reflectionClass = $this->initialContext->newReflectionClass('TechDivision\ApplicationServer\Mock\MockSingletonSessionBean');
        $this->initialContext->removeAttribute('beanTypes');
        $this->assertEquals('singleton', $this->initialContext->getBeanAnnotation($reflectionClass));
    }

    /**
     * Test if the method returns the correct bean annotation for a singleton
     * session bean.
     *
     * @return void
     */
    public function testGetBeanAnnotation()
    {
        $reflectionClass = $this->initialContext->newReflectionClass('TechDivision\ApplicationServer\Mock\MockSingletonSessionBean');
        $this->assertEquals('singleton', $this->initialContext->getBeanAnnotation($reflectionClass));
    }

    /**
     * Test if the method throws an exception for a missing session bean annotation.
     *
     * @return void @expectedException \Exception
     */
    public function testGetBeanAnnotationExcpectingAnException()
    {
        $reflectionClass = $this->initialContext->newReflectionClass('TechDivision\ApplicationServer\Configuration');
        $this->assertEquals('singleton', $this->initialContext->getBeanAnnotation($reflectionClass));
    }

    /**
     * Test the stateless session bean lookup.
     *
     * @return voide
     */
    public function testLookupWithStatelessSessionBean()
    {
        $singletonSessionBean = $this->initialContext->lookup('TechDivision\ApplicationServer\Mock\MockStatelessSessionBean', md5($time = time()));
        $singletonSessionBean->setAValue($aValue = 10);
        $this->assertEquals($aValue, $singletonSessionBean->getAValue());
        $singletonSessionBean = $this->initialContext->lookup('TechDivision\ApplicationServer\Mock\MockStatelessSessionBean', md5($time));
        $this->assertNull($singletonSessionBean->getAValue());
    }

    /**
     * Test the singleton session bean lookup.
     *
     * @return void
     */
    public function testLookupWithSingletonSessionBean()
    {
        $this->markTestSkipped('Singleton session bean functionality has to be implemented first');

        $persistentValue = 10;
        $singletonSessionBean = $this->initialContext->lookup('TechDivision\ApplicationServer\Mock\MockSingletonSessionBean', md5($time = time()));
        $singletonSessionBean->setPersistentValue($persistentValue);
        $singletonSessionBean = $this->initialContext->lookup('TechDivision\ApplicationServer\Mock\MockSingletonSessionBean', md5(time() + 1));
        $this->assertEquals($persistentValue, $singletonSessionBean->getPersistentValue());
    }

    /**
     * Test the stateful session bean lookup.
     *
     * @return void
     */
    public function testLookupWithStatefulSessionBean()
    {
        $this->markTestSkipped('Singleton session bean functionality has to be implemented first');

        $persistentValue = 10;
        $singletonSessionBean = $this->initialContext->lookup('TechDivision\ApplicationServer\Mock\MockStatefulSessionBean', md5($time = time()));
        $singletonSessionBean->setPersistentValue($persistentValue);
        $singletonSessionBean = $this->initialContext->lookup('TechDivision\ApplicationServer\Mock\MockStatefulSessionBean', md5($time));
        $this->assertEquals($persistentValue, $singletonSessionBean->getPersistentValue());
    }
}