<?php

/**
 * \AppserverIo\Appserver\Functional\AspectContainer\AspectManagerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Functional\AspectContainer;

use AppserverIo\Appserver\AspectContainer\AspectManager;
use AppserverIo\Appserver\Core\Api\ConfigurationService;
use AppserverIo\Appserver\Core\Api\Node\AppserverNode;
use AppserverIo\Appserver\Core\Mock\MockInitialContext;
use AppserverIo\Doppelgaenger\AspectRegister;

/**
 * Test class for functional tests concerning the \AppserverIo\Appserver\AspectContainer\AspectManager class
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH - <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io/
 */
class AspectManagerTest  extends \PHPUnit_Framework_TestCase
{

    /**
     * The test class instance to test
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\AspectContainer\AspectManager $testClass
     */
    protected $testClass;

    /**
     * Initializes the test class instance to test
     *
     * @return null
     */
    public function setUp()
    {
        $this->testClass = new AspectManager();
        $this->testClass->injectAspectRegister(new AspectRegister());
    }

    /**
     * Will return the directory where our mocked webapp lies
     *
     * @return string
     */
    public function getMockWebappPath()
    {
        return realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' .
        DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'webapps' .
        DIRECTORY_SEPARATOR . 'test');
    }

    /**
     * Will return a mocked application mapped to a specific pointcuts.xml file
     *
     * @param string $configPath Path to the specific configuration file
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    public function getSpecificConfigMockApplication($configPath)
    {
        // create a mock service so we can influence the glob dir
        $classToMock = '\AppserverIo\Appserver\Core\Api\ServiceInterface';
        $mockInitialContext = new MockInitialContext(new AppserverNode());
        $mockService = $this->getMockBuilder($classToMock)
            ->setMethods(array_merge(get_class_methods($classToMock), array('globDir', 'validateFile')))
            ->setConstructorArgs(array($mockInitialContext))
            ->getMock();
        $mockService->expects($this->once())
            ->method('globDir')
            ->will($this->returnValue(array($configPath)));

        // create an application mock we can work with
        $classToMock = '\AppserverIo\Psr\Application\ApplicationInterface';
        $mockApplication = $this->getMockBuilder($classToMock)
            ->setMethods(array_merge(get_class_methods($classToMock), array('newService')))
            ->getMock();
        $mockApplication->expects($this->atLeastOnce())
            ->method('getWebappPath')
            ->will($this->returnValue($this->getMockWebappPath()));
        $mockApplication->expects($this->once())
            ->method('newService')
            ->will($this->returnValue($mockService));

        return $mockApplication;
    }

    /**
     * Tests if all three pointcuts.xml files get picked up from common, META-INF and WEB-INF directory
     *
     * @return void
     */
    public function testXmlGetsTakenFromAllDirectories()
    {
        // create an application mock we can work with
        $classToMock = '\AppserverIo\Psr\Application\ApplicationInterface';
        $mockInitialContext = new MockInitialContext(new AppserverNode());
        $mockApplication = $this->getMockBuilder($classToMock)
            ->setMethods(array_merge(get_class_methods($classToMock), array('newService')))
            ->getMock();
        $mockApplication->expects($this->atLeastOnce())
            ->method('getWebappPath')
            ->will($this->returnValue($this->getMockWebappPath()));
        $mockApplication->expects($this->once())
            ->method('newService')
            ->will($this->returnValue(new ConfigurationService($mockInitialContext)));

        // inject our mock and run the parsing process
        $this->testClass->injectApplication($mockApplication);
        $this->testClass->registerAspectXml($mockApplication);

        // did we get everything? Check for the aspects as they are the most basic AOP structure
        $aspectRegister = $this->testClass->getAspectRegister();
        $this->assertTrue($aspectRegister->entryExists($this->getMockWebappPath()  . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR .'pointcuts.xml'));
        $this->assertTrue($aspectRegister->entryExists($this->getMockWebappPath()  . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR .'pointcuts.xml'));
        $this->assertTrue($aspectRegister->entryExists($this->getMockWebappPath()  . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR .'pointcuts.xml'));
    }

    /**
     * Tests if we are able to register multiple advices of different aspects referencing the same pointcut using XML
     *
     * @return void
     */
    public function testXmlAllowsForMultiAdviceOfDifferentAspect()
    {
        $this->markTestSkipped('Strange behaviour related to \Stackable usage. Skipped until an alternative has been found.');

        $configPath = $this->getMockWebappPath()  . DIRECTORY_SEPARATOR . 'WEB-INF' . DIRECTORY_SEPARATOR .'pointcuts.xml';
        $mockApplication = $this->getSpecificConfigMockApplication($configPath);

        // inject our mock and run the parsing process
        $this->testClass->injectApplication($mockApplication);
        $this->testClass->registerAspectXml($mockApplication);

        // did we get everything? Check for the expected advices
        $advicesList = $this->testClass->getAspectRegister()->get($configPath)->getAdvices();
        $this->assertTrue($advicesList->entryExists('\AppserverIo\Appserver\NonExisting\WebInf\Namespace\TestAspect1->webinfAdvice'));
        $this->assertTrue($advicesList->entryExists('\AppserverIo\Appserver\NonExisting\WebInf\Namespace\TestAspect2->webinfAdvice'));
    }

    /**
     * Tests if we are able to register multiple advices of the same aspect referencing the same pointcut using XML
     *
     * @return void
     */
    public function testXmlAllowsForMultiAdviceOfSameAspect()
    {
        $this->markTestSkipped('Strange behaviour related to \Stackable usage. Skipped until an alternative has been found.');

        $configPath = $this->getMockWebappPath()  . DIRECTORY_SEPARATOR . 'common' . DIRECTORY_SEPARATOR .'pointcuts.xml';
        $mockApplication = $this->getSpecificConfigMockApplication($configPath);

        // inject our mock and run the parsing process
        $this->testClass->injectApplication($mockApplication);
        $this->testClass->registerAspectXml($mockApplication);

        // did we get everything? Check for the expected advices
        $advicesList = $this->testClass->getAspectRegister()->get($configPath)->getAdvices();
        $this->assertTrue($advicesList->entryExists('\AppserverIo\Appserver\NonExisting\Common\Namespace\TestAspect->commonAdvice1'));
        $this->assertTrue($advicesList->entryExists('\AppserverIo\Appserver\NonExisting\Common\Namespace\TestAspect->commonAdvice2'));
    }

    /**
     * Tests if we are able to reference multiple pointcuts for the same advice using XML
     *
     * @return void
     */
    public function testXmlAllowsForMultiPointcut()
    {
        $this->markTestSkipped('Strange behaviour related to \Stackable usage. Skipped until an alternative has been found.');

        $configPath = $this->getMockWebappPath()  . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR .'pointcuts.xml';
        $mockApplication = $this->getSpecificConfigMockApplication($configPath);

        // inject our mock and run the parsing process
        $this->testClass->injectApplication($mockApplication);
        $this->testClass->registerAspectXml($mockApplication);

        // did we get everything? Check for the expected advice
        $advicesList = $this->testClass->getAspectRegister()->get($configPath)->getAdvices();
        $this->assertTrue($advicesList->entryExists('\AppserverIo\Appserver\NonExisting\MetaInf\Namespace\TestAspect->metainfAdvice'));

        // check for the expected pointcuts the advice should reference
        $referencedPointcuts = $advicesList->get('\AppserverIo\Appserver\NonExisting\MetaInf\Namespace\TestAspect->metainfAdvice')->getPointcuts()->get(0)->getReferencedPointcuts();
        $this->assertCount(2, $referencedPointcuts);
    }
}
