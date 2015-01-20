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

use AppserverIo\Appserver\Core\AbstractTest;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;

/**
 * Callback wrapper for the chgrp function
 *
 * @param string $filename Name of the file to work with
 * @param mixed  $group    Group to work with
 *
 * @return mixed
 */
function chgrp($filename, $group)
{
    return call_user_func_array(AbstractServiceTest::$chgrpCallback, func_get_args());
}

/**
 * Callback wrapper for the chown function
 *
 * @param string $filename Name of the file to work with
 * @param mixed  $user     User to work with
 *
 * @return mixed
 */
function chown($filename, $user)
{
    return call_user_func_array(AbstractServiceTest::$chownCallback, func_get_args());
}

/**
 * Callback wrapper for the mkdir function
 *
 * @param string   $pathname  Name of the path to work with
 * @param integer  $mode      Mode to set (optional)
 * @param boolean  $recursive Do so recursively (optional)
 * @param resource $context   Context resource (optional)
 *
 * @return mixed
 */
function mkdir($pathname, $mode = 0777, $recursive = false, $context = null)
{
    return call_user_func_array(AbstractServiceTest::$mkdirCallback, func_get_args());
}

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
class AbstractServiceTest extends AbstractTest
{

    /**
     * The abstract service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractService $service
     */
    protected $service;

    /**
     * Array which can be used to track calls of wrapped, mocked or stubbed functions and methods
     *
     * @var array $callbackCallStack
     */
    public static $callbackCallStack = array();

    /**
     * Callback wrapper for system function chgrp
     *
     * @var string $chgrpCallback
     */
    public static $chgrpCallback = '';

    /**
     * Callback wrapper for system function chown
     *
     * @var string $chownCallback
     */
    public static $chownCallback = '';

    /**
     * Callback wrapper for system function mkdir
     *
     * @var string $mkdirCallback
     */
    public static $mkdirCallback = '';

    /**
     * Initializes the service instance to test.
     *
     * @return null
     */
    public function setUp()
    {
        // set default callbacks for our wrapped system functions
        self::$chownCallback = '\chgrp';
        self::$chownCallback = '\chown';
        self::$mkdirCallback = '\mkdir';
        self::$callbackCallStack = array();

        // create a basic mock for our abstract service class
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractService', array('findAll', 'load'))
            ->setConstructorArgs(array($this->getMockInitialContext()))
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
            $this->service->getInitialContext(),
            \Mutex::create(false)
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
        // track if the system calls to chown and chgrp have been made
        self::$chownCallback = function () {
            AbstractServiceTest::$callbackCallStack[] = 'chown';
        };
        self::$chgrpCallback = function () {
            AbstractServiceTest::$callbackCallStack[] = 'chgrp';
        };

        // make the call and check if we did run into the important parts
        $this->service->setUserRight($this->getMockBuilder('\SplFileInfo')->setConstructorArgs(array($this->getTmpDir()))->getMock());
        $this->assertContains('chown', self::$callbackCallStack);
        $this->assertContains('chgrp', self::$callbackCallStack);
    }

    /**
     * Tests if changing user and group is not done for Windows systems
     *
     * @return null
     */
    public function testSetUserRightOmitWindows()
    {
        // temporarily mock the getOsIdentifier() method to fake a windows system
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractService')
            ->setMethods(array('findAll', 'load', 'getOsIdentifier'))
            ->setConstructorArgs(array($this->getMockInitialContext()))
            ->getMockForAbstractClass();
        $service->expects($this->any())
            ->method('findAll')
            ->will($this->returnValue(array()));
        $service->expects($this->any())
            ->method('load')
            ->will($this->returnValue(null));
        $service->expects($this->atLeastOnce())
            ->method('getOsIdentifier')
            ->will($this->returnValue('WIN'));

        $service->setUserRight($this->getMockBuilder('\SplFileInfo')->setConstructorArgs(array($this->getTmpDir()))->getMock());
    }

    /**
     * Will create a mock service instance specifically stubbed & mocked for tests of the createDirectory() method
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\AbstractService
     */
    protected function getCreateDirectoryMockService()
    {
        // temporarily switch off initUmask() and setUserRights() as they would make problems
        $service = $this->getMockBuilder('\AppserverIo\Appserver\Core\Api\AbstractService')
        ->setMethods(array('findAll', 'load', 'initUmask', 'setUserRights'))
        ->setConstructorArgs(array($this->getMockInitialContext()))
        ->getMockForAbstractClass();
        $service->expects($this->any())
        ->method('findAll')
        ->will($this->returnValue(array()));
        $service->expects($this->any())
        ->method('load')
        ->will($this->returnValue(null));
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
        self::$mkdirCallback = function () {
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
        //$this->assertTrue(file_exists($testDir));
        //$this->assertTrue(is_dir($testDir));
        rmdir($testDir);
    }

    /**
     *
     * @return null
     */
    public function testCleanUpDir()
    {



    }

    /**
     *
     * @return null
     */
    public function testCleanUpDirAlsoFiles()
    {

    }

    /**
     *
     * @return null
     */
    public function testCleanUpDirNoDir()
    {

    }

    /**
     * Deletes all files and subdirectories from the passed directory.
     *
     * @param \SplFileInfo $dir             The directory to remove
     * @param bool         $alsoRemoveFiles The flag for removing files also
     *
     * @return void
     *
    public function cleanUpDir(\SplFileInfo $dir, $alsoRemoveFiles = true)
    {

        // first check if the directory exists, if not return immediately
        if ($dir->isDir() === false) {
            return;
        }

        // remove old archive from webapps folder recursively
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir->getPathname()),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $file) {
            // skip . and .. dirs
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                @rmdir($file->getRealPath());
            } elseif ($file->isFile() && $alsoRemoveFiles) {
                unlink($file->getRealPath());
            } else {
                // do nothing, because file should NOT be deleted obviously
            }
        }
    }

    /**
     * Copies a directory recursively.
     *
     * @param string $src The source directory to copy
     * @param string $dst The target directory
     *
     * @return void
     *
    public function copyDir($src, $dst)
    {
        if (is_link($src)) {
            symlink(readlink($src), $dst);
        } elseif (is_dir($src)) {
            if (is_dir($dst) === false) {
                mkdir($dst, 0775, true);
            }
            // copy files recursive
            foreach (scandir($src) as $file) {
                if ($file != '.' && $file != '..') {
                    $this->copyDir("$src/$file", "$dst/$file");
                }
            }

        } elseif (is_file($src)) {
            copy($src, $dst);
        } else {
            // do nothing, we didn't have a directory to copy
        }
    }

    /**
     * Recursively parses and returns the directories that matches the passed
     * glob pattern.
     *
     * @param string  $pattern The glob pattern used to parse the directories
     * @param integer $flags   The flags passed to the glob function
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     *
    public function globDir($pattern, $flags = 0)
    {

        // parse the first directory
        $files = glob($pattern, $flags);

        // parse all subdirectories
        foreach (glob(dirname($pattern). DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_BRACE) as $dir) {
            $files = array_merge($files, $this->globDir($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
        }

        // return the array with the files matching the glob pattern
        return $files;
    }

    /**
     * Creates the SSL file passed as parameter or nothing if the file already exists.
     *
     * @param \SplFileInfo $certificate The file info about the SSL file to generate
     *
     * @return void
     *
    public function createSslCertificate(\SplFileInfo $certificate)
    {

        // first we've to check if OpenSSL is available
        if (!extension_loaded('openssl')) {
            return;
        }

        // do nothing if the file is already available
        if ($certificate->isFile()) {
            return;
        }

        // prepare the certificate data from our configuration
        $dn = array(
            "countryName" => "DE",
            "stateOrProvinceName" => "Bavaria",
            "localityName" => "Kolbermoor",
            "organizationName" => "appserver.io",
            "organizationalUnitName" => "Development",
            "commonName" => gethostname(),
            "emailAddress" => "info@appserver.io"
        );

        // check the operating system
        switch (strtoupper(PHP_OS)) {

            case 'DARWIN': // on Mac OS X use the system default configuration

                $configargs = array('config' => '/System/Library/OpenSSL/openssl.cnf');
                break;

            case 'WINNT': // on Windows use the system configuration we deliver

                $configargs = array('config' => $this->getBaseDirectory('/php/extras/ssl/openssl.cnf'));
                break;

            default: // on all other use a standard configuration

                $configargs = array(
                    'digest_alg' => 'md5',
                    'x509_extensions' => 'v3_ca',
                    'req_extensions'   => 'v3_req',
                    'private_key_bits' => 2048,
                    'private_key_type' => OPENSSL_KEYTYPE_RSA,
                    'encrypt_key' => false
                );
        }

        // generate a new private (and public) key pair
        $privkey = openssl_pkey_new($configargs);

        // Generate a certificate signing request
        $csr = openssl_csr_new($dn, $privkey, $configargs);

        // create a self-signed cert that is valid for 365 days
        $sscert = openssl_csr_sign($csr, null, $privkey, 365, $configargs);

        // export the cert + pk files
        $certout = '';
        $pkeyout = '';
        openssl_x509_export($sscert, $certout);
        openssl_pkey_export($privkey, $pkeyout, null, $configargs);

        // write the SSL certificate data to the target
        $file = $certificate->openFile('w');
        if (($written = $file->fwrite($certout . $pkeyout)) === false) {
            throw new \Exception(sprintf('Can\'t create SSL certificate %s', $certificate->getPathname()));
        }

        // log a message that the file has been written successfully
        $this->getInitialContext()->getSystemLogger()->info(
            sprintf('Successfully created %s with %d bytes', $certificate->getPathname(), $written)
        );

        // log any errors that occurred here
        while (($e = openssl_error_string()) !== false) {
            $this->getInitialContext()->getSystemLogger()->debug($e);
        }
    }*/
}
