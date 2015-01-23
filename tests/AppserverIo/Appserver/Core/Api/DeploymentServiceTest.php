<?php

/**
 * AppserverIo\Appserver\Core\Api\DeploymentServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

/**
 * Unit tests for our deployment service implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DeploymentServiceTest extends AbstractServicesTest
{

    /**
     * The app service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\DeploymentService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return null
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new DeploymentService($this->getMockInitialContext());
    }

    /**
     * Test if the findAll() method returns the correct number of elements.
     *
     * @return null
     */
    public function testFindAll()
    {
        $this->assertCount(1, $this->service->findAll());
    }

    /**
     * Test if the load() method returns the correct deployment node.
     *
     * @return null
     */
    public function testLoad()
    {
        $deploymentNodes = $this->service->findAll();
        $deploymentNode = reset($deploymentNodes);

        $this->assertSame($deploymentNode, $this->service->load($deploymentNode->getPrimaryKey()));
    }

    /**
     * Test if the load() method returns the NULL for an invalid primary key.
     *
     * @return null
     */
    public function testLoadWithInvalidPrimaryKey()
    {
        $this->assertNull($this->service->load('invalidPrimaryKey'));
    }

    /**
     * Tests if we can create the tmp directories a passed application needs
     *
     * @return null
     */
    public function testCreateTmpFolders()
    {
        // temporarily switch off initUmask() and setUserRights() as they would make problems
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\DeploymentService')
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
        $this->service->cleanUpFolders($mockApplication);
        $this->assertCount(2, scandir($cacheDir));
    }

    /**
     * Tests if we are able to instantiate application contexts
     *
     * @return null
     */
    public function testLoadContextInstancesByContainer()
    {

    }
}
