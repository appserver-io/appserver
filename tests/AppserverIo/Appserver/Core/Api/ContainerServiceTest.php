<?php

/**
 * AppserverIo\Appserver\Core\Api\ContainerServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

/**
 * Test for the container service implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */
class ContainerServiceTest extends AbstractServicesTest
{

    /**
     * The abstract service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\ContainerService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new ContainerService($this->getMockInitialContext());
    }

    /**
     * Test if the application server's base directory will be returned.
     *
     * @return void
     */
    public function testGetBaseDirectory()
    {
        $this->assertSame('/opt/appserver', $this->service->getBaseDirectory());
    }

    /**
     * Test if the application server's base directory will be returned appended
     * with the directory passed as parameter.
     *
     * @return void
     */
    public function testGetBaseDirectoryWithDirectoryToAppend()
    {
        $directoryToAppend = '/webapps';
        $this->assertSame('/opt/appserver' . $directoryToAppend, $this->service->getBaseDirectory($directoryToAppend));
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
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\ContainerService')
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
        self::$opensslErrorStringCallback = function () {

            if (!in_array('openssl_error_string', self::$callbackCallStack)) {
                self::$callbackCallStack[] = 'openssl_error_string';
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
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\ContainerService
     */
    protected function getPartialServiceMock(array $methodsToMock)
    {
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\ContainerService')
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
