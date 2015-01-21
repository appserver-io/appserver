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
     * @param mixed  $value      The parameter value to return
     * @param string $key        The parameter key to return
     * @param string $methodName The method name of the configuration we want to invoke
     *
     * @return \AppserverIo\Appserver\Core\ServerNodeConfiguration The server node instance to be tested
     */
    protected function getServerNodeWithMockedConfiguration($value, $key = null, $methodName = 'getParam')
    {

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array($methodName));
        $mockMethod = $mockNode->expects($this->once())->method($methodName)->will($this->returnValue($value));

        // check if we've a param
        if ($key != null) {
            $mockMethod->with($this->equalTo($key));
        }

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
        $this->assertSame(
            $value = 'www-data',
            $this->getServerNodeWithMockedConfiguration($value, 'user')->getUser()
        );
    }

    /**
     * Test the getGroup() method.
     *
     * @return void
     */
    public function testGetGroup()
    {
        $this->assertSame(
            $value = 'nobody',
            $this->getServerNodeWithMockedConfiguration($value, 'group')->getGroup()
        );
    }

    /**
     * Test the getTransport() method.
     *
     * @return void
     */
    public function testGetTransport()
    {
        $this->assertSame(
            $value = 'tcp',
            $this->getServerNodeWithMockedConfiguration($value, 'transport')->getTransport()
        );
    }

    /**
     * Test the getAddress() method.
     *
     * @return void
     */
    public function testGetAddress()
    {
        $this->assertSame(
            $value = '127.0.0.1',
            $this->getServerNodeWithMockedConfiguration($value, 'address')->getAddress()
        );
    }

    /**
     * Test the getPort() method.
     *
     * @return void
     */
    public function testGetPort()
    {
        $this->assertSame(
            $value = 9080,
            $this->getServerNodeWithMockedConfiguration($value, 'port')->getPort()
        );
    }

    /**
     * Test the getSoftware() method.
     *
     * @return void
     */
    public function testGetSoftware()
    {
        $this->assertSame(
            $value = 'appserver/1.0.0-beta4.22 (darwin) PHP/5.5.19',
            $this->getServerNodeWithMockedConfiguration($value, 'software')->getSoftware()
        );
    }

    /**
     * Test the getAdmin() method.
     *
     * @return void
     */
    public function testGetAdmin()
    {
        $this->assertSame(
            $value = 'info@appserver.io',
            $this->getServerNodeWithMockedConfiguration($value, 'admin')->getAdmin()
        );
    }

    /**
     * Test the getKeepAliveMax() method.
     *
     * @return void
     */
    public function testGetKeepAliveMax()
    {
        $this->assertSame(
            $value = 64,
            $this->getServerNodeWithMockedConfiguration($value, 'keepAliveMax')->getKeepAliveMax()
        );
    }

    /**
     * Test the getKeepAliveTimeout() method.
     *
     * @return void
     */
    public function testGetKeepAliveTimeout()
    {
        $this->assertSame(
            $value = 5,
            $this->getServerNodeWithMockedConfiguration($value, 'keepAliveTimeout')->getKeepAliveTimeout()
        );
    }

    /**
     * Test the getErrorsPageTemplatePath() method.
     *
     * @return void
     */
    public function testGetErrorsPageTemplatePath()
    {
        $this->assertSame(
            $value = 'var/www/errors/error.phtml',
            $this->getServerNodeWithMockedConfiguration($value, 'errorsPageTemplatePath')->getErrorsPageTemplatePath()
        );
    }

    /**
     * Test the getWorkerNumber() method.
     *
     * @return void
     */
    public function testGetWorkerNumber()
    {
        $this->assertSame(
            $value = 8,
            $this->getServerNodeWithMockedConfiguration($value, 'workerNumber')->getWorkerNumber()
        );
    }

    /**
     * Test the getWorkerAcceptMin() method.
     *
     * @return void
     */
    public function testGetWorkerAcceptMin()
    {
        $this->assertSame(
            $value = 3,
            $this->getServerNodeWithMockedConfiguration($value, 'workerAcceptMin')->getWorkerAcceptMin()
        );
    }

    /**
     * Test the getWorkerAcceptMax() method.
     *
     * @return void
     */
    public function testGetWorkerAcceptMax()
    {
        $this->assertSame(
            $value = 8,
            $this->getServerNodeWithMockedConfiguration($value, 'workerAcceptMax')->getWorkerAcceptMax()
        );
    }

    /**
     * Test the getDocumentRoot() method.
     *
     * @return void
     */
    public function testGetDocumentRoot()
    {
        $this->assertSame(
            $value = 'webapps',
            $this->getServerNodeWithMockedConfiguration($value, 'documentRoot')->getDocumentRoot()
        );
    }

    /**
     * Test the getDirectoryIndex() method.
     *
     * @return void
     */
    public function testGetDirectoryIndex()
    {
        $this->assertSame(
            $value = 'index.do index.php index.html index.htm',
            $this->getServerNodeWithMockedConfiguration($value, 'directoryIndex')->getDirectoryIndex()
        );
    }

    /**
     * Test the getCertPath() method.
     *
     * @return void
     */
    public function testGetCertPath()
    {
        $this->assertSame(
            $value = 'etc/appserver/server.pem',
            $this->getServerNodeWithMockedConfiguration($value, 'certPath')->getCertPath()
        );
    }

    /**
     * Test the getPassphrase() method.
     *
     * @return void
     */
    public function testGetPassphrase()
    {
        $this->assertSame(
            $value = uniqid(),
            $this->getServerNodeWithMockedConfiguration($value, 'passphrase')->getPassphrase()
        );
    }

    /**
     * Test the getName() method.
     *
     * @return void
     */
    public function testGetName()
    {
        $this->assertSame(
            $value = 'http',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getName')->getName()
        );
    }

    /**
     * Test the getType() method.
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertSame(
            $value = '\AppserverIo\Server\Servers\MultiThreadedServer',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getType')->getType()
        );
    }

    /**
     * Test the getLoggerName() method.
     *
     * @return void
     */
    public function testGetLoggerName()
    {
        $this->assertSame(
            $value = 'System',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getLoggerName')->getLoggerName()
        );
    }

    /**
     * Test the getSocketType() method.
     *
     * @return void
     */
    public function testGetSocketType()
    {
        $this->assertSame(
            $value = '\AppserverIo\Server\Sockets\StreamSocket',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getSocket')->getSocketType()
        );
    }

    /**
     * Test the getWorkerType() method.
     *
     * @return void
     */
    public function testGetWorkerType()
    {
        $this->assertSame(
            $value = '\AppserverIo\Server\Workers\ThreadWorker',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getWorker')->getWorkerType()
        );
    }

    /**
     * Test the getServerContextType() method.
     *
     * @return void
     */
    public function testGetServerContextType()
    {
        $this->assertSame(
            $value = '\AppserverIo\Server\Contexts\ServerContext',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getServerContext')->getServerContextType()
        );
    }

    /**
     * Test the getRequestContextType() method.
     *
     * @return void
     */
    public function testGetRequestContextType()
    {
        $this->assertSame(
            $value = '\AppserverIo\Server\Contexts\RequestContext',
            $this->getServerNodeWithMockedConfiguration($value, null, 'getRequestContext')->getRequestContextType()
        );
    }

    /**
     * Checks whether the getAnalytics() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetAnalytics()
    {

        // initialize the array with expected result
        $analytics = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareAnalytics.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getAnalyticsAsArray'));
        $mockNode->expects($this->once())
            ->method('getAnalyticsAsArray')
            ->will($this->returnValue($analytics));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the analytics data
        $this->assertSame($analytics, $serverNode->getAnalytics());
    }

    /**
     * Checks whether the getLocations() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetLocations()
    {

        // initialize the array with expected result
        $locations = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareLocations.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getLocationsAsArray'));
        $mockNode->expects($this->once())
            ->method('getLocationsAsArray')
            ->will($this->returnValue($locations));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the locations data
        $this->assertSame($locations, $serverNode->getLocations());
    }

    /**
     * Checks whether the getRewriteMaps() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetRewriteMaps()
    {

        // initialize the array with expected result
        $rewriteMaps = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareRewriteMaps.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getRewriteMapsAsArray'));
        $mockNode->expects($this->once())
            ->method('getRewriteMapsAsArray')
            ->will($this->returnValue($rewriteMaps));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the rewrite maps data
        $this->assertSame($rewriteMaps, $serverNode->getRewriteMaps());
    }

    /**
     * Checks whether the getAccesses() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetAccesses()
    {

        // initialize the array with expected result
        $accesses = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareAccesses.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getAccessesAsArray'));
        $mockNode->expects($this->once())
            ->method('getAccessesAsArray')
            ->will($this->returnValue($accesses));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the accesses data
        $this->assertSame($accesses, $serverNode->getAccesses());
    }

    /**
     * Checks whether the getAnalytics() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetAuthentications()
    {

        // initialize the array with expected result
        $authentications = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareAuthentications.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getAuthenticationsAsArray'));
        $mockNode->expects($this->once())
            ->method('getAuthenticationsAsArray')
            ->will($this->returnValue($authentications));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the authentications data
        $this->assertSame($authentications, $serverNode->getAuthentications());
    }

    /**
     * Checks whether the getConnectionHandlers() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetConnectionHandlers()
    {

        // initialize the array with expected result
        $connectionHandlers = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareConnectionHandlers.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getConnectionHandlersAsArray'));
        $mockNode->expects($this->once())
            ->method('getConnectionHandlersAsArray')
            ->will($this->returnValue($connectionHandlers));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the connection handlers data
        $this->assertSame($connectionHandlers, $serverNode->getConnectionHandlers());
    }

    /**
     * Checks whether the getEnvironmentVariables() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetEnvironmentVariables()
    {

        // initialize the array with expected result
        $environmentVariables = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareEnvironmentVariables.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getEnvironmentVariablesAsArray'));
        $mockNode->expects($this->once())
            ->method('getEnvironmentVariablesAsArray')
            ->will($this->returnValue($environmentVariables));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the environment variables data
        $this->assertSame($environmentVariables, $serverNode->getEnvironmentVariables());
    }

    /**
     * Checks whether the getHandlers() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetHandlers()
    {

        // initialize the array with expected result
        $handlers = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareAnalytics.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getFileHandlersAsArray'));
        $mockNode->expects($this->once())
            ->method('getFileHandlersAsArray')
            ->will($this->returnValue($handlers));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the handlers data
        $this->assertSame($handlers, $serverNode->getHandlers());
    }

    /**
     * Checks whether the getModules() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetModules()
    {

        // initialize the array with expected result
        $modules = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareModules.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getModulesAsArray'));
        $mockNode->expects($this->once())
            ->method('getModulesAsArray')
            ->will($this->returnValue($modules));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the modules data
        $this->assertSame($modules, $serverNode->getModules());
    }

    /**
     * Checks whether the getRewrites() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetRewrites()
    {

        // initialize the array with expected result
        $rewrites = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareRewrites.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getRewritesAsArray'));
        $mockNode->expects($this->once())
            ->method('getRewritesAsArray')
            ->will($this->returnValue($rewrites));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the rewrites data
        $this->assertSame($rewrites, $serverNode->getRewrites());
    }

    /**
     * Checks whether the getRewrites() method returns the expected result.
     *
     * @return void
     * @todo
     */
    public function testGetVirtualHosts()
    {

        // initialize the array with expected result
        $virtualHosts = json_decode(file_get_contents(__DIR__ . '/Api/Node/_files/prepareVirtualHosts.json'));

        // create a mock configuration instance
        $mockNode = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ServerNode', array('getVirtualHostsAsArray'));
        $mockNode->expects($this->once())
            ->method('getVirtualHostsAsArray')
            ->will($this->returnValue($virtualHosts));

        // initialize the server node
        $serverNode = new ServerNodeConfiguration($mockNode);

        // check the array with the virtual hosts data
        $this->assertSame($virtualHosts, $serverNode->getVirtualHosts());
    }
}
