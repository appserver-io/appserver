<?php

/**
 * AppserverIo\Appserver\Core\Api\AppServiceTest
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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Appserver\Core\Api\Node\AppNode;

/**
 * Unit tests for our app service implementation.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AppServiceTest extends AbstractServicesTest
{

    /**
     * The app service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AppService
     */
    protected $appService;

    /**
     * Initializes the service instance to test.
     *
     * @return null
     */
    public function setUp()
    {
        parent::setUp();
        $this->appService = new AppService($this->getMockInitialContext());
    }

    /**
     * Test if the findAll() method returns the correct number of elements.
     *
     * @return null
     */
    public function testFindAll()
    {
        $this->assertCount(4, $this->appService->findAll());
    }

    /**
     * Test if the findAllByName() method returns the correct number of elements.
     *
     * @return null
     */
    public function testFindAllByName()
    {
        $this->assertCount(2, $this->appService->findAllByName('api'));
    }

    /**
     * Test if the load() method returns the correct app node.
     *
     * @return null
     */
    public function testLoad()
    {
        $appNode = new AppNode('someappname', '/opt/appserver/anotherwebapppath');
        $this->appService->persist($appNode);

        $this->assertSame($appNode, $this->appService->load($appNode->getPrimaryKey()));
    }

    /**
     * Test if the load() method returns the NULL for an invalid primary key.
     *
     * @return null
     */
    public function testLoadWithInvalidPrimaryKey()
    {
        $this->assertNull($this->appService->load('invalidPrimaryKey'));
    }

    /**
     * Will test if we can successfully add an app to the configuration based on a given application object
     *
     * @return null
     */
    public function testNewFromApplication()
    {
        // create a basic mock for our abstract service class
        $mockApp = $this->getMock('\AppserverIo\Psr\Application\ApplicationInterface');
        $mockApp->expects($this->once())
            ->method('getName')
            ->will($this->returnValue(__METHOD__));

        $this->appService->newFromApplication($mockApp);

        $apps = $this->appService->getSystemConfiguration()->getApps();
        $foundApp = false;
        foreach ($apps as $app) {
            if ($app->getName() === __METHOD__) {
                $foundApp = true;
                break;
            }
        }

        $this->assertTrue($foundApp);
    }

    /**
     * Tests if we can load a webapp by path
     *
     * @return null
     */
    public function testLoadByWebappPath()
    {
        $appNode = new AppNode('someappname', '/opt/appserver/targetwebapp');
        $this->appService->persist($appNode);

        $this->assertSame($appNode, $this->appService->loadByWebappPath($appNode->getWebappPath()));
        $this->assertNull($this->appService->loadByWebappPath('/absolutely/non/existing/webapp/path'));
    }

    /**
     * Tests if we can persist apps into our configuration
     *
     * @return null
     */
    public function testPersist()
    {
        $appNode = new AppNode(__METHOD__, '/opt/appserver/targetwebapp');
        $this->appService->persist($appNode);

        $apps = $this->appService->getSystemConfiguration()->getApps();
        $foundApp = false;
        foreach ($apps as $app) {
            if ($app->getName() === __METHOD__) {
                $foundApp = true;
                break;
            }
        }

        $this->assertTrue($foundApp);
    }

    /**
     * Tests if are able to always get an extractor, either default or injected one
     *
     * @return null
     */
    public function testGetExtractor()
    {
        $this->assertInstanceOf('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface', $this->appService->getExtractor());

        $this->appService->injectExtractor($this->getMock('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface'));
        $this->assertInstanceOf('\PHPUnit_Framework_MockObject_MockObject', $this->appService->getExtractor());
    }

    /**
     * Tests if are able to run through the method
     *
     * @return null
     */
    public function testGetExtractorWithoutDefaultConfiguration()
    {
        // Lets create a mocked configuration which does not know about extractors
        $mockConfig = $this->getMock('\AppserverIo\Appserver\Core\Api\Node\AppserverNode');

        $this->appService->setSystemConfiguration($mockConfig);
        $this->assertNull($this->appService->getExtractor());
    }

    /**
     * Tests if we are able to inject our extractor
     *
     * @return null
     */
    public function testInjectExtractor()
    {
        $this->appService->injectExtractor($this->getMock('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface'));
        $this->assertInstanceOf('\PHPUnit_Framework_MockObject_MockObject', $this->appService->getExtractor());
    }

    /**
     * Tests if we can soak an archive without receiving an exception
     *
     * @return null
     */
    public function testSoak()
    {
        $mockExtractor = $this->getMock('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface');
        $mockExtractor->expects($this->once())
            ->method('soakArchive');

        $this->appService->injectExtractor($mockExtractor);
        $this->appService->soak($this->getMockBuilder('\SplFileInfo')->setConstructorArgs(array(__FILE__))->getMock());
    }

    /**
     * Tests if we will run through the deploy process without an error
     *
     * @return null
     */
    public function testDeploy()
    {
        $mockExtractor = $this->getMock('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface');
        $mockExtractor->expects($this->once())
            ->method('flagArchive');
        $this->appService->injectExtractor($mockExtractor);

        $this->appService->deploy(new AppNode(__METHOD__, '/opt/appserver/targetwebapp'));
    }

    /**
     * If we can run the un-deploy functionality without receiving an error
     *
     * @return null
     */
    public function testUndeploy()
    {
        $mockExtractor = $this->getMock('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface');
        $mockExtractor->expects($this->once())
            ->method('unflagArchive');
        $this->appService->injectExtractor($mockExtractor);

        $appNode = new AppNode(__METHOD__, '/opt/appserver/targetwebapp');
        $this->appService->persist($appNode);

        $this->appService->undeploy($appNode->getUuid());
    }

    /**
     * If we can determine if a passed UUID is an invalid one
     *
     * @return null
     */
    public function testUndeployInvalidUuid()
    {
        $mockExtractor = $this->getMock('\AppserverIo\Appserver\Core\Interfaces\ExtractorInterface');
        $mockExtractor->expects($this->never())
            ->method('unflagArchive');
        $this->appService->injectExtractor($mockExtractor);

        $appNode = new AppNode(__METHOD__, '/opt/appserver/targetwebapp');

        $this->appService->undeploy($appNode->getUuid());
    }

    /**
     * Tests if we can create the tmp directories a passed application needs
     *
     * @return null
     */
    public function testCreateTmpFolders()
    {
        // temporarily switch off initUmask() and setUserRights() as they would make problems
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AppService')
            ->setMethods(array_merge(array('findAll', 'load', 'initUmask', 'setUserRights')))
            ->setConstructorArgs(array($this->getMockInitialContext()))
            ->getMockForAbstractClass();
        $service->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array()));
        $service->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));
        $service->expects($this->exactly(3))
            ->method('initUmask');
        $service->expects($this->exactly(3))
            ->method('setUserRights');

        $tmp = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $tmpDir = $tmp . 'tmp';
        $cacheDir = $tmp . 'cache';
        $sessionDir = $tmp . 'session';

        $mockApplication = $this->getMockBuilder('\AppserverIo\Psr\Application\ApplicationInterface')
            ->setMethods(get_class_methods('\AppserverIo\Appserver\Application\Application'))
            ->getMock();
        $mockApplication->expects($this->once())
            ->method('getTmpDir')
            ->will($this->returnValue($tmpDir));
        $mockApplication->expects($this->once())
            ->method('getCacheDir')
            ->will($this->returnValue($cacheDir));
        $mockApplication->expects($this->once())
            ->method('getSessionDir')
            ->will($this->returnValue($sessionDir));

        $service->createTmpFolders($mockApplication);

        $this->assertTrue(is_dir($tmpDir));
        $this->assertTrue(is_dir($cacheDir));
        $this->assertTrue(is_dir($sessionDir));
    }

    /**
     * Tests if we are able to clear the tmp directory of an application
     *
     * @return null
     */
    public function testCleanUpFolders()
    {
        $cacheDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . 'cache';
        if (!is_dir($cacheDir)) {

            \mkdir($cacheDir);
        }
        touch($cacheDir . DIRECTORY_SEPARATOR . md5(__METHOD__));

        $mockApplication = $this->getMockBuilder('\AppserverIo\Psr\Application\ApplicationInterface')
            ->setMethods(get_class_methods('\AppserverIo\Appserver\Application\Application'))
            ->getMock();
        $mockApplication->expects($this->once())
            ->method('getCacheDir')
            ->will($this->returnValue($cacheDir));

        $this->assertCount(3, scandir($cacheDir));
        $this->appService->cleanUpFolders($mockApplication);
        $this->assertCount(2, scandir($cacheDir));
    }
}
