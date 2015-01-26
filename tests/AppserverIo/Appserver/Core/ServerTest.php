<?php

/**
 * AppserverIo\Appserver\Core\ServerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\Api\Node\BaseDirectoryNode;
use AppserverIo\Appserver\Core\Api\Node\NodeValue;

/**
 * Test for the server implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServerTest extends AbstractTest
{

    /**
     * Creates and returns a mock server instance with NO methods mocked.
     *
     * @param array   $methodsToMock           The methods we want to mock
     * @param boolean $callOriginalConstructor TRUE if the original constructor should be invoked, else FALSE
     *
     * @return PHPUnit_Framework_MockObject_MockObject The mocked server instance
     */
    public function getMockServer(array $methodsToMock = array(), $callOriginalConstructor = true)
    {

        // initialize the configuration
        $configuration = $this->getAppserverConfiguration();

        // create a new mock server implementation
        return $this->getMock('AppserverIo\Appserver\Core\Server', $methodsToMock, array($configuration), '', $callOriginalConstructor);
    }

    /**
     * Creates and returns a mock logger instance with all methods mocked.
     *
     * @param array $methodsToMock The methods we want to mock
     *
     * @return PHPUnit_Framework_MockObject_MockObject The mocked logger instance
     */
    public function getMockLogger(array $methodsToMock = array('log', 'error', 'warning', 'notice', 'emergency', 'debug', 'info', 'alert', 'critical'))
    {
        return $this->getMock('Psr\Log\LoggerInterface', $methodsToMock);
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return void
     */
    public function testGetInitialContext()
    {

        // initialize the mock server
        $mockServer = $this->getMockServer(array('initUmask','initFileSystem', 'initSslCertificate'));

        // check the initial context type
        $this->assertInstanceOf('AppserverIo\Appserver\Core\InitialContext', $mockServer->getInitialContext());
    }

    /**
     * Test if the system logger has successfully been initialized.
     *
     * @return void
     */
    public function testGetSystemLogger()
    {

        // initialize the mock server
        $mockServer = $this->getMockServer(array('initUmask','initFileSystem', 'initSslCertificate'));

        // check the logger configuration type
        $this->assertInstanceOf('Monolog\Logger', $mockServer->getSystemLogger());
    }

    /**
     * Test if the system configuration has been passed successfully.
     *
     * @return void
     */
    public function testGetSystemConfiguration()
    {

        // initialize the mock server
        $mockServer = $this->getMockServer(array('initUmask','initFileSystem', 'initSslCertificate'));

        // check the system configuration type
        $this->assertInstanceOf('AppserverIo\Appserver\Core\Api\Node\AppserverNode', $mockServer->getSystemConfiguration());
    }

    /**
     * Test the server's start method.
     *
     * @return void
     */
    public function testStart()
    {

        // initialize the configuration
        $configuration = $this->getAppserverConfiguration();

        // replace the base directory
        $appserverConfiguration = new Configuration();
        $appserverConfiguration->setNodeName('appserver');
        $baseDirectoryConfiguration = new Configuration();
        $baseDirectoryConfiguration->setNodeName('baseDirectory');
        $baseDirectoryConfiguration->setValue(__DIR__);
        $appserverConfiguration->addChild($baseDirectoryConfiguration);
        $configuration->merge($appserverConfiguration);

        // initialize the mock logger
        $mockLogger = $this->getMockLogger();

        // mock the system configuration
        $mockSystemConfiguration = $this->getMock(
            'AppserverIo\Appserver\Core\Api\Node\AppserverNode',
            array('getBaseDirectory')
        );

        // create a new mock server implementation
        $server = $this->getMock(
            'AppserverIo\Appserver\Core\Server',
            array(
                'initExtractors',
                'startContainers',
                'initProcessUser',
                'initContainers',
                'initProvisioners',
                'initFileSystem',
                'initSslCertificate',
                'getSystemLogger',
                'getSystemConfiguration'
            ),
            array($configuration),
            '',
            false
        );

        // mock the servers startContainers() and the initConstructors() method
        $server->expects($this->once())->method('initExtractors');
        $server->expects($this->once())->method('initContainers');
        $server->expects($this->once())->method('startContainers');
        $server->expects($this->once())->method('initProcessUser');
        $server->expects($this->once())->method('initProvisioners');
        $server->expects($this->once())
            ->method('getSystemLogger')
            ->will($this->returnValue($mockLogger));
        $server->expects($this->once())
            ->method('getSystemConfiguration')
            ->will($this->returnValue($mockSystemConfiguration));

        // start the server instance
        $server->start();
    }

    /**
     * Test the new instance method.
     *
     * @return void
     */
    public function testNewInstance()
    {

        // initialize the mock server
        $mockServer = $this->getMockServer(array('initUmask','initFileSystem', 'initSslCertificate'));

        // create a new container thread instance
        $id = 1;
        $className = 'AppserverIo\Appserver\Core\Mock\MockContainerThread';
        $instance = $mockServer->newInstance(
            $className,
            array($mockServer->getInitialContext(), \Mutex::create(false), $id)
        );
        $this->assertInstanceOf($className, $instance);
    }
}