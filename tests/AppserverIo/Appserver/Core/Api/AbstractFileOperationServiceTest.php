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
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Appserver\Core\Api\Mock\MockInitialContext;
use org\bovigo\vfs\vfsStream;

/**
 * Unit tests for our abstract file operation service implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AbstractFileOperationServiceTest extends AbstractServicesTest
{

    /**
     * The abstract service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractFileOperationService $service
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
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractFileOperationService')
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
     * Tests if we are able to change user rights and group
     *
     * @return null
     */
    public function testSetUserRight()
    {
        $this->skipOnWindows();

        // track if the system calls to chown and chgrp have been made
        AbstractServiceTest::$chownCallback = function () {
            AbstractServiceTest::$callbackCallStack['testSetUserRight'][] = 'chown';
        };
        AbstractServiceTest::$chgrpCallback = function () {
            AbstractServiceTest::$callbackCallStack['testSetUserRight'][] = 'chgrp';
        };

        // make the call and check if we did run into the important parts
        $this->service->setUserRight($this->getMockBuilder('\SplFileInfo')->setConstructorArgs(array($this->getTmpDir()))->getMock());
        $this->assertContains('chown', AbstractServiceTest::$callbackCallStack['testSetUserRight']);
        $this->assertContains('chgrp', AbstractServiceTest::$callbackCallStack['testSetUserRight']);
    }

    /**
     * Tests if changing user and group is not done for Windows systems
     *
     * @return null
     */
    public function testSetUserRightOmitWindows()
    {
        $this->skipOnWindows();

        // temporarily mock the getOsIdentifier() method to fake a windows system
        $service = $this->getPartialServiceMock(array('getOsIdentifier'));
        $service->expects($this->atLeastOnce())
            ->method('getOsIdentifier')
            ->will($this->returnValue('WIN'));

        $service->setUserRight(new \SplFileInfo($this->getTmpDir()));
    }

    /**
     * Tests if changing user and group is possible without errors
     *
     * @return null
     */
    public function testSetUserRights()
    {
        $this->skipOnWindows();

        // track if the system calls to chown and chgrp have been made
        AbstractServiceTest::$chownCallback = function () {
            AbstractServiceTest::$callbackCallStack['testSetUserRights'][] = 'chown';
        };
        AbstractServiceTest::$chgrpCallback = function () {
            AbstractServiceTest::$callbackCallStack['testSetUserRights'][] = 'chgrp';
        };

        // make the call and check if we did run into the important parts
        $this->service->setUserRights(new \SplFileInfo($this->getTmpDir()));
        $this->assertContains('chown', AbstractServiceTest::$callbackCallStack['testSetUserRights']);
        $this->assertContains('chgrp', AbstractServiceTest::$callbackCallStack['testSetUserRights']);
    }

    /**
     * Tests if changing user and group is not done for Windows systems
     *
     * @return null
     */
    public function testSetUserRightsOmitWindows()
    {
        $this->skipOnWindows();

        // temporarily mock the getOsIdentifier() method to fake a windows system
        $service = $this->getPartialServiceMock(array('getOsIdentifier'));
        $service->expects($this->once())
            ->method('getOsIdentifier')
            ->will($this->returnValue('WIN'));

        // mock an \SplFileInfo object to test if we reached any file operation (which we should not)
        $someDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);
        $splFileMock = $this->getMockBuilder('\SplFileInfo')
            ->setMethods(array('isDir'))
            ->setConstructorArgs(array($someDir))
            ->getMock();
        $splFileMock->expects($this->never())
            ->method('isDir')
            ->will($this->returnValue(true));

        $service->setUserRights($splFileMock);
    }

    /**
     * Tests if we bail when we get passed an invalid directory
     *
     * @return null
     */
    public function testSetUserRightsBailOnWrongPath()
    {
        $this->skipOnWindows();

        $service = $this->getPartialServiceMock(array('getInitialContext'));
        $service->expects($this->never())
            ->method('getInitialContext');

        // mock an \SplFileInfo object to test if we reached any file operation (which we should not)
        $someDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);
        $splFileMock = $this->getMockBuilder('\SplFileInfo')
            ->setMethods(array('isDir'))
            ->setConstructorArgs(array($someDir))
            ->getMock();
        $splFileMock->expects($this->once())
            ->method('isDir')
            ->will($this->returnValue(false));

        // the test is fine if 'getInitialContext' never gets called
        $service->setUserRights($splFileMock);
    }

    /**
     * Will create a mock service instance specifically stubbed & mocked for tests of the createDirectory() method
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractFileOperationService
     */
    protected function getCreateDirectoryMockService()
    {

        // temporarily switch off initUmask() and setUserRights() as they would make problems
        $service = $this->getPartialServiceMock(array('initUmask', 'setUserRights'));
        $service->expects($this->once())
            ->method('initUmask');
        $service->expects($this->any())
            ->method('setUserRights');

        return $service;
    }

    /**
     * Will run through the createDirectory method
     *
     * @return null
     */
    public function testCreateDirectory()
    {
        $service = $this->getCreateDirectoryMockService();
        $service->createDirectory(new \SplFileInfo($this->getTmpDir()));
    }

    /**
     * Will test if an exception is thrown if we try to create an impossible dir
     *
     * @return null
     *
     * @expectedException \Exception
     */
    public function testCreateDirectoryImpossibleDir()
    {
        $service = $this->getCreateDirectoryMockService();

        // make mkdir return false so we can provoke our exception
        AbstractServiceTest::$mkdirCallback = function () {
            return false;
        };

        $service->createDirectory(new \SplFileInfo($this->getTmpDir() . DIRECTORY_SEPARATOR . md5(rand())));
    }

    /**
     * Will test if a directory will get created if it does not exist yet
     *
     * @return null
     */
    public function testCreateDirectoryNoDir()
    {
        $service = $this->getCreateDirectoryMockService();

        $testDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . __FUNCTION__;
        $service->createDirectory(new \SplFileInfo($testDir));
        $this->assertTrue(is_dir($testDir));
        rmdir($testDir);
    }

    /**
     * Test if we can clear directories which contain files
     *
     * @return null
     */
    public function testCleanUpDir()
    {
        $testDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(rand());

        \mkdir($testDir);
        \touch($testDir . DIRECTORY_SEPARATOR . __FUNCTION__);
        $this->assertTrue(file_exists($testDir . DIRECTORY_SEPARATOR . __FUNCTION__));

        $this->service->cleanUpDir(new \SplFileInfo($this->getTmpDir()));
        $this->assertFalse(file_exists($testDir . DIRECTORY_SEPARATOR . __FUNCTION__));
    }

    /**
     * Test if we leave paths which are no directories alone
     *
     * @return null
     */
    public function testCleanUpDirNoInitialDirGiven()
    {
        $testFile = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(rand());
        touch($testFile);

        $this->assertTrue(file_exists($testFile));
        $this->service->cleanUpDir(new \SplFileInfo($testFile), false);
        $this->assertTrue(file_exists($testFile));
    }

    /**
     * Will test if we can clean empty directories from a given path
     *
     * @return null
     */
    public function testCleanUpDirNoFilesCleanup()
    {
        $testDir1 = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(rand());
        $testDir2 = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(rand());

        \mkdir($testDir1);
        \mkdir($testDir2);
        $this->assertTrue(is_dir($testDir1));
        $this->assertTrue(is_dir($testDir2));

        $this->service->cleanUpDir(new \SplFileInfo($this->getTmpDir()), false);
        $this->assertFalse(is_dir($testDir1));
        $this->assertFalse(is_dir($testDir2));
    }

    /**
     * Test if we can run through with an invalid source without provoking errors
     *
     * @return null
     */
    public function testCopyDirInvalidSourceDir()
    {
        $invalidDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(rand());
        $tmpDir = $this->getTmpDir();

        $currentFileCount = count(scandir($tmpDir));
        $this->assertFalse(file_exists($invalidDir));
        $this->service->copyDir($invalidDir, $tmpDir);
        $this->assertCount($currentFileCount, scandir($tmpDir));
    }

    /**
     * Test if we actually can copy a file from one directory to another
     *
     * @return null
     */
    public function testCopyDirBothDirsExist()
    {
        $rootDir = $this->setUpFilesystemMock('tmp');
        $dir = vfsStream::newDirectory('tmp1');
        $dir->addChild(vfsStream::newFile('copyMe'));
        $rootDir->addChild($dir);
        $rootDir->addChild(vfsStream::newDirectory('tmp2'));

        $this->assertFalse(file_exists(vfsStream::url('tmp2/copyMe')));
        $this->service->copyDir(vfsStream::url('tmp1'), vfsStream::url('tmp2'));
        $this->assertTrue(file_exists(vfsStream::url('tmp2/copyMe')));
    }

    /**
     * Test if we can copy a file into a non existing directory
     *
     * @return null
     */
    public function testCopyDirOneDirExists()
    {
        $rootDir = $this->setUpFilesystemMock('tmp');
        $dir = vfsStream::newDirectory('tmp1');
        $dir->addChild(vfsStream::newFile('copyMe'));
        $rootDir->addChild($dir);

        $dstDir = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__) . DIRECTORY_SEPARATOR;

        $this->assertFalse(file_exists($dstDir . 'copyMe'));
        $this->service->copyDir(vfsStream::url('tmp1'), $dstDir);
        $this->assertTrue(file_exists($dstDir . 'copyMe'));
    }

    /**
     * Tests if we can copy a solitary file
     *
     * @return null
     */
    public function testCopyDirWhichIsAFile()
    {
        $tmpDir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $file = md5(rand() . microtime(true));
        $dirName = $tmpDir . md5(rand() . microtime(true)) . DIRECTORY_SEPARATOR;
        touch($tmpDir . $file);
        \mkdir($dirName);

        $this->assertFalse(file_exists($dirName . $file));
        $this->service->copyDir($tmpDir . $file, $dirName . $file);
        $this->assertTrue(file_exists($dirName . $file));
    }

    /**
     * Test if we can copy something that has been linked to
     *
     * @return null
     */
    public function testCopyDirWhichIsALink()
    {
        $tmpDir = $this->getTmpDir() . DIRECTORY_SEPARATOR;
        $file = md5(rand() . microtime(true));
        $linkName = $tmpDir . md5(rand() . microtime(true));
        $dirName = $tmpDir . md5(rand() . microtime(true)) . DIRECTORY_SEPARATOR;
        touch($tmpDir . $file);
        symlink($tmpDir . $file, $linkName);
        \mkdir($dirName);

        $this->assertFalse(file_exists($dirName . $file));
        $this->service->copyDir($linkName, $dirName . $file);
        $this->assertTrue(file_exists($dirName . $file));
    }

    /**
     * Tests if we are able to set a umask
     *
     * @return null
     */
    public function testInitUmask()
    {
        $this->skipOnWindows();

        // the test is clear if we did not receive an exception
        $this->service->initUmask();
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
     * Tests if we are able to check for failures on umask changes
     *
     * @return null
     *
     * @expectedException \Exception
     */
    public function testInitUmaskFailureException()
    {
        $this->skipOnWindows();

        AbstractServiceTest::$umaskCallback = function () {
            return 1000; // umasks will never exceed 0777
        };

        // the test is clear if we did not receive an exception
        $this->service->initUmask();
    }

    /**
     * Tests if we will successfully shun windows
     *
     * @return null
     */
    public function testInitUmaskOmitWindows()
    {
        $this->skipOnWindows();

        // temporarily mock the getOsIdentifier() method to fake a windows system
        $service = $this->getPartialServiceMock(array('getOsIdentifier'));
        $service->expects($this->atLeastOnce())
            ->method('getOsIdentifier')
            ->will($this->returnValue('WIN'));

        // make any calls to umask() trackable and avoid returning an useful value
        AbstractServiceTest::$umaskCallback = function () {
            AbstractServiceTest::$callbackCallStack['testInitUmaskOmitWindows'][] = 'umask';
            return 'definitely not an int';
        };

        // make the call and check if umask() did get called
        $service->initUmask();
        $this->assertFalse(isset(AbstractServiceTest::$callbackCallStack['testInitUmaskOmitWindows']));
    }

    /**
     * Returns a partially mocked mock instance of our abstract service
     *
     * @param array $methodsToMock The methods we want to mock besides 'findAll' and 'load'
     *
     * @param array $methodsToMock
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractFileOperationService
     */
    protected function getPartialServiceMock(array $methodsToMock)
    {
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractFileOperationService')
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
