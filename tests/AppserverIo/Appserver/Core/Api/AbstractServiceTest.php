<?php

/**
 * AppserverIo\Appserver\Core\Api\AbstractServiceTest
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

use AppserverIo\Appserver\Core\Api\Mock\MockInitialContext;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use org\bovigo\vfs\vfsStream;

/**
 * Unit tests for our abstract service implementation.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AbstractServiceTest extends AbstractServicesTest
{

    /**
     * The abstract service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractService $service
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

        // create a basic mock for our abstract service class
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractService')
            ->setMethods(array('findAll', 'load'))
            ->setConstructorArgs(array(new MockInitialContext($this->getAppserverNode())))
            ->getMockForAbstractClass();
        $service->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array()));
        $service->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));

        $this->service = $service;
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return null
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\InitialContext', $this->service->getInitialContext());
    }

    /**
     * Test if the system configuration has successfully been initialized.
     *
     * @return null
     */
    public function testGetSystemConfiguration()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\Api\Node\AppserverNode', $this->service->getSystemConfiguration());
    }

    /**
     * Test if the getter/setter for the system configuration.
     *
     * @return null
     */
    public function testGetSetSystemConfiguration()
    {
        $systemConfiguration = $this->service->getSystemConfiguration();
        $this->service->setSystemConfiguration($systemConfiguration);
        $this->assertSame($systemConfiguration, $this->service->getSystemConfiguration());
    }

    /**
     * Test the new instance method.
     *
     * @return null
     */
    public function testNewInstance()
    {
        $className = 'AppserverIo\Appserver\Core\Api\Mock\MockService';
        $instance = $this->service->newInstance($className, array(
            $this->service->getInitialContext()
        ));
        $this->assertInstanceOf($className, $instance);
    }

    /**
     * Test the new service method.
     *
     * @return null
     */
    public function testNewService()
    {
        $className = 'AppserverIo\Appserver\Core\Api\Mock\MockService';
        $service = $this->service->newService($className);
        $this->assertInstanceOf($className, $service);
    }

    /**
     * Tests the returning of a base directory appended by a given string
     *
     * @return null
     */
    public function testGetBaseDirectoryDirectoryAppended()
    {

        $appendedDirectory = '/test/directory';
        $result = $this->service->getBaseDirectory($appendedDirectory);

        $this->assertEquals($this->service->getBaseDirectory() . $appendedDirectory, $result);
    }

    /**
     * Tests the returning of a base directory without appending anything
     *
     * @return null
     */
    public function testGetBaseDirectoryNothingToAppend()
    {
        $baseDir = $this->service->getBaseDirectory();

        $this->assertEquals('/opt/appserver', $baseDir);
        $this->assertNotEquals('/opt/appserver/test/directory', $baseDir);
    }

    /**
     * Test if directories get taken from our system configuration
     *
     * @return null
     */
    public function testGetDirectories()
    {
        $directories = $this->service->getDirectories();

        $this->assertCount(10, $directories);
        $this->assertArrayHasKey(DirectoryKeys::BASE, $directories);
        $this->assertArrayHasKey(DirectoryKeys::DEPLOY, $directories);
        $this->assertEquals('/opt/appserver', $directories[DirectoryKeys::BASE]);
        $this->assertEquals('/var/tmp', $directories[DirectoryKeys::TMP]);
        $this->assertEquals('/webapps', $directories[DirectoryKeys::WEBAPPS]);
    }

    /**
     * Tests if getting the tmp directory + an appendage works
     *
     * @return null
     */
    public function testGetTmpDirDirectoryAppended()
    {

        $appendedDirectory = '/test/directory';
        $result = $this->service->getTmpDir($appendedDirectory);

        $this->assertEquals($this->service->getTmpDir() . $appendedDirectory, $result);
    }

    /**
     * Tests if getting just the tmp directory works
     *
     * @return null
     */
    public function testGetTmpDirNothingToAppend()
    {
        $tmpDir = $this->service->getTmpDir();

        $this->assertEquals('/opt/appserver/var/tmp', $tmpDir);
        $this->assertNotEquals('/opt/appserver/var/tmp/test/directory', $tmpDir);
    }

    /**
     * Tests if returning the deploy dir + appendage works
     *
     * @return null
     */
    public function testGetDeployDirDirectoryAppended()
    {
        $appendedDirectory = '/test/directory';
        $result = $this->service->getDeployDir($appendedDirectory);

        $this->assertEquals($this->service->getDeployDir() . $appendedDirectory, $result);
    }

    /**
     * Tests if getting just the tmp directory works
     *
     * @return null
     */
    public function testGetDeployDirNothingToAppend()
    {
        $deployDir = $this->service->getDeployDir();

        $this->assertEquals('/opt/appserver/deploy', $deployDir);
        $this->assertNotEquals('/opt/appserver/deploy/test/directory', $deployDir);
    }


    /**
     * Tests if returning the webapps dir + appendage works
     *
     * @return null
     */
    public function testGetWebappsDirDirectoryAppended()
    {
        $appendedDirectory = '/test/directory';
        $result = $this->service->getWebappsDir($appendedDirectory);

        $this->assertEquals($this->service->getWebappsDir() . $appendedDirectory, $result);
    }

    /**
     * Tests if getting just the webapps directory works
     *
     * @return null
     */
    public function testGetWebappsDirNothingToAppend()
    {
        $webappsDir = $this->service->getWebappsDir();

        $this->assertEquals('/opt/appserver/webapps', $webappsDir);
        $this->assertNotEquals('/opt/appserver/webapps/test/directory', $webappsDir);
    }

    /**
     * Tests if returning the log dir + appendage works
     *
     * @return null
     */
    public function testGetLogDirDirectoryAppended()
    {
        $appendedDirectory = '/test/directory';
        $result = $this->service->getLogDir($appendedDirectory);

        $this->assertEquals($this->service->getLogDir() . $appendedDirectory, $result);
    }

    /**
     * Tests if getting just the log directory works
     *
     * @return null
     */
    public function testGetLogDirNothingToAppend()
    {
        $logDir = $this->service->getLogDir();

        $this->assertEquals('/opt/appserver/var/log', $logDir);
        $this->assertNotEquals('/opt/appserver/var/log/test/directory', $logDir);
    }

    /**
     * Check if we get a correctly trimmed identifier within a possible range
     *
     * @return null
     *
     * @see https://en.wikipedia.org/wiki/Uname#Table_of_standard_uname_output
     */
    public function testGetOsIdentifier()
    {
        $possibleValues = array(
            'CYG',
            'DAR',
            'FRE',
            'HP-',
            'IRI',
            'LIN',
            'Net',
            'OPE',
            'SUN',
            'UNI',
            'WIN'
        );

        $osIdentifier = $this->service->getOsIdentifier();
        $this->assertEquals(3, strlen($osIdentifier));
        $this->assertTrue(ctype_upper($osIdentifier));
        $this->assertContains($osIdentifier, $possibleValues);
    }

    /**
     * Tests if returning the configuration dir + appendage works
     *
     * @return null
     */
    public function testGetConfDirDirectoryAppended()
    {
        $appendedDirectory = '/test/directory';
        $result = $this->service->getConfDir($appendedDirectory);

        $this->assertEquals($this->service->getConfDir() . $appendedDirectory, $result);
    }

    /**
     * Tests if getting just the configuration directory works
     *
     * @return null
     */
    public function testGetConfDirNothingToAppend()
    {
        $dir = $this->service->getConfDir();

        $this->assertEquals('/opt/appserver/etc/appserver', $dir);
        $this->assertNotEquals('/opt/appserver/etc/appserver/test/directory', $dir);
    }

    /**
     * Tests if returning the configuration sub-dir + appendage works
     *
     * @return null
     */
    public function testGetConfdDirDirectoryAppended()
    {
        $appendedDirectory = '/test/directory';
        $result = $this->service->getConfdDir($appendedDirectory);

        $this->assertEquals($this->service->getConfdDir() . $appendedDirectory, $result);
    }

    /**
     * Tests if getting just the configuration sub-directory works
     *
     * @return null
     */
    public function testGetConfdDirNothingToAppend()
    {
        $dir = $this->service->getConfdDir();

        $this->assertEquals('/opt/appserver/etc/appserver/conf.d', $dir);
        $this->assertNotEquals('/opt/appserver/etc/appserver/conf.d/test/directory', $dir);
    }

    /**
     * Test if building the absolute path works like it should
     *
     * @return null
     */
    public function testRealpath()
    {
        $appendedDirectory = '/test/directory';
        $result = $this->service->realpath($appendedDirectory);

        $this->assertEquals('/opt/appserver/test/directory', $result);
    }

    /**
     * Dummy test as there currently is now functionality behind the actual method
     *
     * @return null
     *
     * @expectedException \AppserverIo\Lang\NotImplementedException
     */
    public function testPersist()
    {
        $this->service->persist($this->service->getSystemConfiguration());
    }

    /**
     * Rests if we can glob directories and files as we should
     *
     * @return null
     */
    public function testGlobDir()
    {
        $tmpDir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        touch($tmpDir . 'globMe1');
        \mkdir($tmpDir . 'globMeAsWell');
        touch($tmpDir . 'dontGlobMe');

        $files = $this->service->globDir($tmpDir . 'globMe*');
        $this->assertContains($tmpDir . 'globMe1', $files);
        $this->assertContains($tmpDir . 'globMeAsWell', $files);
        $this->assertNotContains($tmpDir . 'dontGlobMe', $files);
    }

    /**
     * Use to skip your test on windows machines
     *
     * @return null
     */
    protected function skipOnWindows()
    {
        // not testable on Windows machines
        if ($this->service->getOsIdentifier() === 'WIN') {
            $this->markTestSkipped('Not testable on Windows machines');
        }
    }

    /**
     * Returns a partially mocked mock instance of our abstract service
     *
     * @param array $methodsToMock The methods we want to mock besides 'findAll' and 'load'
     *
     * @param array $methodsToMock
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractService
     */
    protected function getPartialServiceMock(array $methodsToMock)
    {
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractService')
            ->setMethods(array_merge(array('findAll', 'load'), $methodsToMock))
            ->setConstructorArgs(array($this->getMockInitialContext()))
            ->getMockForAbstractClass();
        $service->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array()));
        $service->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));

        return $service;
    }
}
