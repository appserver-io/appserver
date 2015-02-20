<?php

/**
 * AppserverIo\Appserver\Application\ApplicationTest
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
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application;

use AppserverIo\Appserver\Naming\NamingDirectory;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Appserver\Application\Mock\MockManager;
use AppserverIo\Appserver\Application\Mock\MockClassLoader;
use AppserverIo\Appserver\Application\Mock\MockSystemConfiguration;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\EnterpriseBeans\Annotations\AnnotationKeys;

/**
 * Test implementation for the threaded application implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ApplicationTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The application name for testing purposes.
     *
     * @var  string
     */
    const NAME = 'foo';

    /**
     * The user for testing purposes.
     *
     * @var string
     */
    const USER = 'www-data';

    /**
     * The group for testing purposes.
     *
     * @var string
     */
    const GROUP = 'www-data';

    /**
     * The umask for testing purposes.
     *
     * @var integer
     */
    const UMASK = 0002;

    /**
     * The global tmp directory.
     *
     * @var string
     */
    const GLOBAL_TMP_DIR = '/opt/appserver/var/tmp';

    /**
     * The base directory for testing purposes.
     *
     * @var  string
     */
    const BASE_DIRECTORY = '/opt/appserver';

    /**
     * The application base directory for testing purposes.
     *
     * @var  string
     */
    const APP_BASE = '/opt/appserver/webapps';

    /**
     * The application temporary directory for testing purposes.
     *
     * @var  string
     */
    const TMP_DIR = '/opt/appserver/var/tmp/foo';

    /**
     * The application cache directory for testing purposes.
     *
     * @var string
     */
    const CACHE_DIR = '/opt/appserver/var/tmp/foo/cache';

    /**
     * The application directory for testing purposes.
     *
     * @var string
     */
    const WEBAPP_PATH = '/opt/appserver/webapps/foo';

    /**
     * The application session directory for testing purposes.
     *
     * @var string
     */
    const SESSION_DIR = '/opt/appserver/var/tmp/foo/session';

    /**
     * The server name for testing purposes.
     *
     * @var  string
     */
    const SERVER_NAME = 'test.local';

    /**
     * The application instance we want to test.
     *
     * @var \AppserverIo\Appserver\Application\Application
     */
    protected $application;

    /**
     * The storage for the managers.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $managers;

    /**
     * The storage for the class loaders.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $classLoaders;

    /**
     * The storage for the naming directory data.
     *
     * @var \AppserverIo\Storage\StackableStorage
     */
    protected $data;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {


        // initialize the application instance
        $this->application = new Application();

        // create a generic stackable for the necessary storages
        $this->data = new StackableStorage();
        $this->managers = new GenericStackable();
        $this->classLoaders = new GenericStackable();

        // create a mock instance of the naming directory
        $this->namingDirectory = new NamingDirectory();
        $this->namingDirectory->setScheme('php');
        $this->envDir = $this->namingDirectory->createSubdirectory('env');
        $this->appDir = $this->envDir->createSubdirectory('foo');

        $this->namingDirectory->bind('php:global/foo', $this->application);
        $this->namingDirectory->bind('php:env/user', ApplicationTest::USER);
        $this->namingDirectory->bind('php:env/group', ApplicationTest::GROUP);
        $this->namingDirectory->bind('php:env/umask', ApplicationTest::UMASK);
        $this->namingDirectory->bind('php:env/tmpDirectory', ApplicationTest::GLOBAL_TMP_DIR);
        $this->namingDirectory->bind('php:env/foo/webappPath', ApplicationTest::WEBAPP_PATH);
        $this->namingDirectory->bind('php:env/foo/tmpDirectory', ApplicationTest::TMP_DIR);
        $this->namingDirectory->bind('php:env/foo/cacheDirectory', ApplicationTest::CACHE_DIR);
        $this->namingDirectory->bind('php:env/foo/sessionDirectory', ApplicationTest::SESSION_DIR);
        $this->namingDirectory->bind('php:env/baseDirectory', ApplicationTest::BASE_DIRECTORY);
        $this->namingDirectory->bind('php:env/appBase', ApplicationTest::APP_BASE);

        // inject the storages
        $this->application->injectName(ApplicationTest::NAME);
        $this->application->injectData($this->data);
        $this->application->injectManagers($this->managers);
        $this->application->injectClassLoaders($this->classLoaders);
        $this->application->injectNamingDirectory($this->namingDirectory);
    }

    /**
     * Test if the application has successfully been initialized.
     *
     * @return void
     */
    public function testConstructor()
    {
        $this->assertInstanceOf('AppserverIo\Psr\Application\ApplicationInterface', $this->application);
    }

    /**
     * Test if the getter/setter for the application name works.
     *
     * @return void
     */
    public function testGetName()
    {
        $this->assertEquals(ApplicationTest::NAME, $this->application->getName());
    }

    /**
     * Test if the getter/setter for the app base works.
     *
     * @return void
     */
    public function testGetAppBase()
    {
        $this->assertEquals(ApplicationTest::APP_BASE, $this->application->getAppBase());
    }

    /**
     * Test if the getter for a certain class loader works.
     *
     * @return void
     */
    public function testGetClassLoader()
    {
        $mockLoader = $this->getMock('\AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface');
        $reflectionClass = new \ReflectionClass($mockLoader);

        $mockLoaderConfig = $this->getMock('\AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface');
        $mockLoaderConfig->expects($this->any())
            ->method('getName')
            ->will($this->returnValue($reflectionClass->getShortName()));

        $this->application->addClassLoader($mockLoader, $mockLoaderConfig);
        $this->assertEquals($mockLoader, $this->application->getClassLoader($reflectionClass->getShortName()));
    }

    /**
     * Test if the getter for the session dir works.
     *
     * @return void
     */
    public function testGetSessionDir()
    {
        $this->assertEquals(ApplicationTest::SESSION_DIR, $this->application->getSessionDir());
    }

    /**
     * Test if the getter for the cache dir works.
     *
     * @return void
     */
    public function testGetCacheDir()
    {
        $this->assertEquals(ApplicationTest::CACHE_DIR, $this->application->getCacheDir());
    }

    /**
     * Test if the getter/setter for the initial context works.
     *
     * @return void
     */
    public function testInjectGetInitialContext()
    {

        // define the methods to mock
        $methodsToMock = array('getClassLoader', 'newInstance', 'newService', 'getAttribute', 'getSystemLogger', 'getSystemConfiguration');

        // create a mock instance
        $mockInitialContext = $this->getMock('AppserverIo\Appserver\Application\Interfaces\ContextInterface', $methodsToMock);

        // check if the passed instance is equal to the getter one
        $this->application->injectInitialContext($mockInitialContext);
        $this->assertEquals($mockInitialContext, $this->application->getInitialContext());
    }

    /**
     * Test if the getter/setter for the managers works.
     *
     * @return void
     */
    public function testInjectGetManagers()
    {
        $this->assertEquals($this->managers, $this->application->getManagers());
    }

    /**
     * Test if the getter/setter for the class loaders works.
     *
     * @return void
     */
    public function testInjectGetClassLoaders()
    {
        $this->assertEquals($this->classLoaders, $this->application->getClassLoaders());
    }

    /**
     * Test if the newService() method will be forwarded to the initial context.
     *
     * @return void
     */
    public function testNewService()
    {

        // define the methods to mock
        $methodsToMock = array('getClassLoader', 'newInstance', 'newService', 'getAttribute', 'getSystemLogger', 'getSystemConfiguration');

        // create a mock instance
        $mockInitialContext = $this->getMock('AppserverIo\Appserver\Application\Interfaces\ContextInterface', $methodsToMock);
        $mockInitialContext->expects($this->any())
            ->method('newService')
            ->will($this->returnValue($newService = new \stdClass()));

        // check if the passed instance is equal to the getter one
        $this->application->injectInitialContext($mockInitialContext);
        $this->assertEquals($newService, $this->application->newService('\stdClass'));
    }

    /**
     * Test if the getter/setter for the base directory works.
     *
     * @return void
     */
    public function testGetBaseDirectory()
    {
        $this->assertEquals(ApplicationTest::BASE_DIRECTORY, $this->application->getBaseDirectory());
    }

    /**
     * Test if the passed directory will be appended correctly.
     *
     * @return void
     */
    public function testGetBaseDirectoryWithDirectoryToAppend()
    {

        // create a directory
        $aDirectory = ApplicationTest::BASE_DIRECTORY . DIRECTORY_SEPARATOR . ApplicationTest::NAME;

        // inject the base directory
        $this->assertEquals($aDirectory, $this->application->getBaseDirectory(DIRECTORY_SEPARATOR . ApplicationTest::NAME));
    }

    /**
     * Test if the getter for the webapp path works.
     *
     * @return void
     */
    public function testGetWebappPath()
    {
        $this->assertEquals(ApplicationTest::WEBAPP_PATH, $this->application->getWebappPath());
    }

    /**
     * Test if the class loader has been added successfully.
     *
     * @return void
     */
    public function testAddClassLoader()
    {

        // define the methods to mock
        $methodsToMock = array('getName', 'getDirectories', 'getEnforcementLevel', 'getProcessing', 'getType', 'getTypeSafety', 'getEnvironment');

        // create a mock loader configuration
        $mockLoaderConfiguration = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface', $methodsToMock);
        $mockLoaderConfiguration->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MockLoader'));

        // create the mock class loader instance
        $mockLoader = $this->getMock('\AppserverIo\Appserver\Core\Interfaces\ClassLoaderInterface');

        // add the class loader to the application
        $this->application->addClassLoader($mockLoader, $mockLoaderConfiguration);

        // iterate over the class loaders
        foreach ($this->application->getClassLoaders() as $cls) {
            $this->assertEquals($cls, $mockLoader);
        }
    }

    /**
     * Test if the class loader has been added successfully.
     *
     * @return void
     */
    public function testSetGetAttribute()
    {
        $this->application->setAttribute($key = 'test', ApplicationTest::NAME);
        $this->assertSame(ApplicationTest::NAME, $this->application->getAttribute($key));
    }

    /**
     * Test if the manager instance has been added successfully.
     *
     * @return void
     */
    public function testAddManager()
    {

        // define the methods to mock
        $methodsToMock = array('getFactory', 'getType', 'getName', 'getParamsAsArray');

        // create a mock manager configuration
        $mockManagerConfiguration = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface', $methodsToMock);
        $mockManagerConfiguration->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(MockManager::IDENTIFIER));

        // add a mock manager
        $this->application->addManager($mockManager = new MockManager(), $mockManagerConfiguration);
        $this->assertEquals($mockManager, $this->application->getManager(MockManager::IDENTIFIER));
    }

    /**
     * Test if the NULL will be returned for an invalid manager request.
     *
     * @return void
     */
    public function testGetInvalidManager()
    {
        $this->assertNull($this->application->getManager(MockManager::IDENTIFIER));
    }

    /**
     * Test if the added manager has been returned.
     *
     * @return void
     */
    public function testGetManagers()
    {

        // define the methods to mock
        $methodsToMock = array('getFactory', 'getType', 'getName', 'getParamsAsArray');

        // create a mock manager configuration
        $mockManagerConfiguration1 = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface', $methodsToMock);
        $mockManagerConfiguration1->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MockManager1'));

        // create a mock manager configuration
        $mockManagerConfiguration2 = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface', $methodsToMock);
        $mockManagerConfiguration2->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MockManager2'));

        // initialize the managers
        $mgr1 = new MockManager('test_01');
        $mgr2 = new MockManager('test_02');

        // add the managers
        $this->application->addManager($mgr1, $mockManagerConfiguration1);
        $this->application->addManager($mgr2, $mockManagerConfiguration2);
        $this->assertEquals(2, sizeof($this->application->getManagers()));
        foreach ($this->application->getManagers() as $manager) {
            $this->assertInstanceOf('AppserverIo\Psr\Application\ManagerInterface', $manager);
        }
    }

    /**
     * Test if the class loaders has been registered successfully.
     *
     * @return void
     */
    public function testRegisterClassLoaders()
    {

        // initialize the mock logger
        $mockLogger = $this->getMock('Psr\Log\LoggerInterface', array('log', 'error', 'warning', 'notice', 'emergency', 'debug', 'info', 'alert', 'critical'));

        // define the methods to mock
        $methodsToMock = array('getClassLoader', 'newInstance', 'newService', 'getAttribute', 'getSystemLogger', 'getSystemConfiguration');

        // create a mock instance
        $mockInitialContext = $this->getMock('AppserverIo\Appserver\Application\Interfaces\ContextInterface', $methodsToMock);
        $mockInitialContext->expects($this->any())
            ->method('getSystemLogger')
            ->will($this->returnValue($mockLogger));

        // inject the mock initial context instance
        $this->application->injectInitialContext($mockInitialContext);

        // define the methods to mock
        $methodsToMock = array('getName', 'getDirectories', 'getEnforcementLevel', 'getProcessing', 'getType', 'getTypeSafety', 'getEnvironment');

        // create a mock loader configuration
        $mockLoaderConfiguration = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ClassLoaderNodeInterface', $methodsToMock);
        $mockLoaderConfiguration->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MockLoader'));

        // register the mock class loader instance
        $this->application->addClassLoader($mockClassLoader = new MockClassLoader(), $mockLoaderConfiguration);
        $this->application->registerClassLoaders();

        // check that the mock class loader has been registered
        $this->assertTrue($mockClassLoader->isRegistered());
    }

    /**
     * Test if the managers has been initialized successfully.
     *
     * @return void
     */
    public function testInitializeManagers()
    {

        // initialize the mock logger
        $mockLogger = $this->getMock('Psr\Log\LoggerInterface', array('log', 'error', 'warning', 'notice', 'emergency', 'debug', 'info', 'alert', 'critical'));

        // define the methods to mock
        $methodsToMock = array('getClassLoader', 'newInstance', 'newService', 'getAttribute', 'getSystemLogger', 'getSystemConfiguration');

        // create a mock instance
        $mockInitialContext = $this->getMock('AppserverIo\Appserver\Application\Interfaces\ContextInterface', $methodsToMock);
        $mockInitialContext->expects($this->any())
            ->method('getSystemLogger')
            ->will($this->returnValue($mockLogger));

        // inject the mock initial context instance
        $this->application->injectInitialContext($mockInitialContext);

        // define the methods to mock
        $methodsToMock = array('getName', 'getType', 'getFactory', 'getParamsAsArray');

        // create a mock manager configuration
        $mockManagerConfiguration = $this->getMock('AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface', $methodsToMock);
        $mockManagerConfiguration->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('MockManager'));

        // register the mock manager instance
        $this->application->addManager($mockManager = new MockManager(), $mockManagerConfiguration);
        $this->application->initializeManagers();

        // check that the mock manager has been initialized
        $this->assertTrue($mockManager->isInitialized());
    }

    /**
     * Test if the getter for the user works.
     *
     * @return void
     */
    public function testGetUser()
    {
        $this->assertSame(ApplicationTest::USER, $this->application->getUser());
    }

    /**
     * Test if the getter for the group works.
     *
     * @return void
     */
    public function testGetGroup()
    {
        $this->assertSame(ApplicationTest::GROUP, $this->application->getGroup());
    }

    /**
     * Test if the getter for the group works.
     *
     * @return void
     */
    public function testGetUmask()
    {
        $this->assertSame(ApplicationTest::UMASK, $this->application->getUmask());
    }
}
