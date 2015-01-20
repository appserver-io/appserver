<?php

/**
 * AppserverIo\Appserver\Core\ServerNodeConfigurationTest
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\AbstractTest;

/**
 * Test for the server node configuration implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ServerNodeConfigurationTest extends AbstractTest
{

    /**
     * Creates a new server instance with a mocked configuration, that
     * returns the param with the passed key/value.
     *
     * @param string $key   The parameter key to return
     * @param mixed  $value The parameter value to return
     *
     * @return \AppserverIo\Appserver\Core\ServerNodeConfiguration The server node instance to be tested
     */
    protected function getMockedServerNode($key, $value)
    {

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Configuration\Configuration', array('getParam'));
        $mockNode->expects($this->once())
            ->method('getParam')
            ->with($this->equalTo($key))
            ->will($this->returnValue($value));

        // initialize the server node
        return new ServerNodeConfiguration($mockNode);
    }

    /**
     * Test the getUser() method.
     *
     * @return void
     */
    public function testGetUser()
    {
        $this->assertSame($value = 'www-data', $this->getMockedServerNode('user', $value)->getUser());
    }

    /**
     * Test the getGroup() method.
     *
     * @return void
     */
    public function testGetGroup()
    {
        $this->assertSame($value = 'nobody', $this->getMockedServerNode('group', $value)->getGroup());
    }

    /**
     * Test the getTransport() method.
     *
     * @return void
     */
    public function testGetTransport()
    {
        $this->assertSame($value = 'tcp', $this->getMockedServerNode('transport', $value)->getTransport());
    }

    /**
     * Test the getAddress() method.
     *
     * @return void
     */
    public function testGetAddress()
    {
        $this->assertSame($value = '127.0.0.1', $this->getMockedServerNode('address', $value)->getAddress());
    }

    /**
     * Test the getPort() method.
     *
     * @return void
     */
    public function testGetPort()
    {
        $this->assertSame($value = 9080, $this->getMockedServerNode('port', $value)->getPort());
    }

    /**
     * Test the getSoftware() method.
     *
     * @return void
     */
    public function testGetSoftware()
    {
        $this->assertSame($value = 'appserver/1.0.0-beta4.22 (darwin) PHP/5.5.19', $this->getMockedServerNode('software', $value)->getSoftware());
    }

    /**
     * Test the getAdmin() method.
     *
     * @return void
     */
    public function testGetAdmin()
    {
        $this->assertSame($value = 'info@appserver.io', $this->getMockedServerNode('admin', $value)->getAdmin());
    }

    /**
     * Test the getKeepAliveMax() method.
     *
     * @return void
     */
    public function testGetKeepAliveMax()
    {
        $this->assertSame($value = 64, $this->getMockedServerNode('keepAliveMax', $value)->getKeepAliveMax());
    }

    /**
     * Test the getKeepAliveTimeout() method.
     *
     * @return void
     */
    public function testGetKeepAliveTimeout()
    {
        $this->assertSame($value = 5, $this->getMockedServerNode('keepAliveTimeout', $value)->getKeepAliveTimeout());
    }

    /**
     * Test the getErrorsPageTemplatePath() method.
     *
     * @return void
     */
    public function testGetErrorsPageTemplatePath()
    {
        $this->assertSame($value = 'var/www/errors/error.phtml', $this->getMockedServerNode('errorsPageTemplatePath', $value)->getErrorsPageTemplatePath());
    }

    /**
     * Test the getWorkerNumber() method.
     *
     * @return void
     */
    public function testGetWorkerNumber()
    {
        $this->assertSame($value = 8, $this->getMockedServerNode('workerNumber', $value)->getWorkerNumber());
    }

    /**
     * Test the getWorkerAcceptMin() method.
     *
     * @return void
     */
    public function testGetWorkerAcceptMin()
    {
        $this->assertSame($value = 3, $this->getMockedServerNode('workerAcceptMin', $value)->getWorkerAcceptMin());
    }

    /**
     * Test the getWorkerAcceptMax() method.
     *
     * @return void
     */
    public function testGetWorkerAcceptMax()
    {
        $this->assertSame($value = 8, $this->getMockedServerNode('workerAcceptMax', $value)->getWorkerAcceptMax());
    }

    /**
     * Test the getDocumentRoot() method.
     *
     * @return void
     */
    public function testGetDocumentRoot()
    {
        $this->assertSame($value = 'webapps', $this->getMockedServerNode('documentRoot', $value)->getDocumentRoot());
    }

    /**
     * Test the getDirectoryIndex() method.
     *
     * @return void
     */
    public function testGetDirectoryIndex()
    {
        $this->assertSame($value = 'index.do index.php index.html index.htm', $this->getMockedServerNode('directoryIndex', $value)->getDirectoryIndex());
    }
}
