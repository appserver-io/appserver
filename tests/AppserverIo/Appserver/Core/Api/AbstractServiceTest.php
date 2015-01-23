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
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use org\bovigo\vfs\vfsStream;

/**
 * Unit tests for our abstract service implementation.
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

        $this->assertCount(8, $directories);
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
     * Tests if changing user and group is not done for Windows systems
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

        $service->setUserRight($this->getMockBuilder('\SplFileInfo')->setConstructorArgs(array($this->getTmpDir()))->getMock());
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
        $this->service->setUserRights($this->getMockBuilder('\SplFileInfo')->setConstructorArgs(array($this->getTmpDir()))->getMock());
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractService
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
     * Tests if we are able to successfully run through SSL certificate creation logic for Linux.
     * We do NOT test the actual OpenSSL functionality
     *
     * @return null
     */
    public function testCreateSslCertificateLinux()
    {
        // temporarily mock the isOpenSslAvailable() method to fake a windows system
        $service = $this->getPartialServiceMock(array('isOpenSslAvailable', 'getOsIdentifier'));
        $service->expects($this->once())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(true));
        $service->expects($this->once())
            ->method('getOsIdentifier')
            ->will($this->returnValue('LIN'));

        $certPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);

        $service->createSslCertificate(new \SplFileInfo($certPath));
        $this->assertTrue(file_exists($certPath));
    }

    /**
     * Tests if we are able to successfully run through SSL certificate creation logic for Mac.
     * We do NOT test the actual OpenSSL functionality
     *
     * @return null
     */
    public function testCreateSslCertificateMac()
    {
        // temporarily mock the isOpenSslAvailable() method to fake a windows system
        $service = $this->getPartialServiceMock(array('isOpenSslAvailable', 'getOsIdentifier'));
        $service->expects($this->once())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(true));
        $service->expects($this->once())
            ->method('getOsIdentifier')
            ->will($this->returnValue('DAR'));

        $certPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);

        $service->createSslCertificate(new \SplFileInfo($certPath));
        $this->assertTrue(file_exists($certPath));
    }

    /**
     * Tests if we are able to successfully run through SSL certificate creation logic for Windows.
     * We do NOT test the actual OpenSSL functionality
     *
     * @return null
     */
    public function testCreateSslCertificateWindows()
    {
        // temporarily mock the isOpenSslAvailable() method to fake a windows system
        $service = $this->getPartialServiceMock(array('isOpenSslAvailable', 'getOsIdentifier'));
        $service->expects($this->once())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(true));
        $service->expects($this->once())
            ->method('getOsIdentifier')
            ->will($this->returnValue('WIN'));

        $certPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);

        $service->createSslCertificate(new \SplFileInfo($certPath));
        $this->assertTrue(file_exists($certPath));
    }

    /**
     * Tests if we will not try to create a certificate without the OpenSSL extension
     *
     * @return null
     */
    public function testCreateSslCertificateMissingExtension()
    {
        // temporarily mock the isOpenSslAvailable() method to test for specific logic flow
        $service = $this->getPartialServiceMock(array('isOpenSslAvailable'));
        $service->expects($this->atLeastOnce())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(false));

        // mock an \SplFileInfo object to test if we reached any file operation (which we should not)
        $certPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);
        $splFileMock = $this->getMockBuilder('\SplFileInfo')
            ->setMethods(array('isFile'))
            ->setConstructorArgs(array($certPath))
            ->getMock();
        $splFileMock->expects($this->never())
            ->method('isFile');

        $service->createSslCertificate($splFileMock);
    }

    /**
     * Tests if we will not try to create a certificate without the OpenSSL extension
     *
     * @return null
     *
     * @expectedException \Exception
     */
    public function testCreateSslCertificateWhichCannotBeWritten()
    {
        // temporarily mock the isOpenSslAvailable() method to test for specific logic flow
        $service = $this->getPartialServiceMock(array('isOpenSslAvailable'));
        $service->expects($this->once())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(true));

        // create a \SplFileObject mock which cannot be written to
        $certPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);
        $existingCertPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__METHOD__);
        touch($existingCertPath);
        $splFileMock = $this->getMockBuilder('\SplFileObject')
            ->setMethods(array('fwrite'))
            ->setConstructorArgs(array($existingCertPath))
            ->getMock();
        $splFileMock->expects($this->once())
            ->method('fwrite')
            ->will($this->returnValue(false));

        // mock an \SplFileInfo mock which only opens a readonly file
        $splInfoMock = $this->getMockBuilder('\SplFileInfo')
            ->setMethods(array('openFile'))
            ->setConstructorArgs(array($certPath))
            ->getMock();
        $splInfoMock->expects($this->once())
            ->method('openFile')
            ->will($this->returnValue($splFileMock));

        $service->createSslCertificate($splInfoMock);
    }

    /**
     * Tests if we will not try to create a certificate without the OpenSSL extension
     *
     * @return null
     */
    public function testCreateSslCertificateLogThatAnOpenSslErrorOccured()
    {
        $mockSystemLogger = $this->getMockBuilder('\AppserverIo\Appserver\Core\Mock\InitialContext\MockSystemLogger')
            ->setMethods(array_merge(array('debug')))
            ->getMock();
        $mockSystemLogger->expects($this->once())
            ->method('debug');

        $mockInitialContext = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\Mock\MockInitialContext')
            ->setMethods(array_merge(array('getSystemLogger')))
            ->setConstructorArgs(array($this->getAppserverNode()))
            ->getMock();
        $mockInitialContext->expects($this->atLeastOnce())
            ->method('getSystemLogger')
            ->will($this->returnValue($mockSystemLogger));

        // temporarily mock the isOpenSslAvailable() method to test for specific logic flow
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractService')
            ->setMethods(array_merge(array('findAll', 'load', 'isOpenSslAvailable')))
            ->setConstructorArgs(array($mockInitialContext))
            ->getMockForAbstractClass();
        $service->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array()));
        $service->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));
        $service->expects($this->atLeastOnce())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(true));

        // make our "OpenSSL extension" return an error (but only once!)
        AbstractServiceTest::$opensslErrorStringCallback = function () {

            if (!in_array('openssl_error_string', AbstractServiceTest::$callbackCallStack)) {
                AbstractServiceTest::$callbackCallStack[] = 'openssl_error_string';
                return 'I am an error, fear me!';
            }

            return false;
        };

        $service->createSslCertificate(new \SplFileInfo($this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__)));
    }

    /**
     * Tests if we omit overwriting of an already existing file
     *
     * @return null
     */
    public function testCreateSslCertificateNoOverwriteOfFile()
    {
        // temporarily mock the isOpenSslAvailable() and getOsIdentifier() method to test for specific logic flow
        $service = $this->getPartialServiceMock(array('isOpenSslAvailable', 'getOsIdentifier'));
        $service->expects($this->atLeastOnce())
            ->method('isOpenSslAvailable')
            ->will($this->returnValue(true));
        $service->expects($this->never())
            ->method('getOsIdentifier');

        $certPath = $this->getTmpDir() . DIRECTORY_SEPARATOR . md5(__FUNCTION__);
        touch($certPath);

        // test is fine if we never reach the call to 'getOsIdentifier'
        $service->createSslCertificate(new \SplFileInfo($certPath));
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
