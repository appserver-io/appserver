<?php

/**
 * AppserverIo\Appserver\Core\DgClassLoaderTest
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core;

/**
 * Test for the doppelgaenger class loader implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2015 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DgClassLoaderTest extends AbstractTest
{

    /**
     * Tests if the constructor initializes the members as exepcted.
     *
     * @return void
     */
    public function testConstructorWithConfigPassed()
    {

        // create a mocked configuratio instance
        $mockConfig = $this->getMock('AppserverIo\Doppelgaenger\Config');

        // create the
        $this->getMockBuilder('AppserverIo\Appserver\Core\DgClassLoader')
            ->setConstructorArgs(array($mockConfig))
            ->getMock();
    }

    /**
     * Tests the init() method.
     *
     * @return void
     */
    public function testInit()
    {

        // skip the test, because DgClassLoader needs to be refactored
        $this->markTestSkipped('DgClassLoader needs to be refactored.');

        // mock the configuration
        $mockConfig = $this->getMock('AppserverIo\Doppelgaenger\Config', array('hasValue', 'getValue'));
        $mockConfig->expects($this->once())
            ->method('hasValue')
            ->will($this->returnValue(false));
        $mockConfig->expects($this->exactly(5))
            ->method('getValue')
            ->will(
                $this->onConsecutiveCalls(
                    '/opt/appserver/var/tmp/pbc/cache',
                    'production',
                    array("PHPUnit", "Psr\\Log", "PHP"),
                    array("/opt/appserver/app/code", "/opt/appserver/webapps"),
                    array("/opt/appserver/app/code/vendor/techdivision")
                )
            );

        // mock the structure map
        $mockStructureMap = $this->getMock('AppserverIo\Doppelgaenger\StructureMap', array('fill'), array(), '', false);
        $mockStructureMap->expects($this->once())
            ->method('fill')
            ->will($this->returnValue(true));

        // mock the class loader
        $mockClassLoader = $this->getMock('AppserverIo\Appserver\Core\DgClassLoader', array('getConfig', 'getStructureMap'), array($mockConfig));
        $mockClassLoader->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($mockConfig));
        $mockClassLoader->expects($this->once())
            ->method('getStructureMap')
            ->will($this->returnValue($mockStructureMap));

        // invoke the init method
        $mockClassLoader->init();

        // check that the cache directory has been initialized successfully
        $this->assertSame('/opt/appserver/var/tmp/pbc/cache', $mockClassLoader->getCacheDir());
    }
}
