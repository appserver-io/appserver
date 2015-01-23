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
use AppserverIo\Appserver\Core\Api\Mock\MockInitialContext;

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
    return call_user_func_array(AbstractServicesTest::$chgrpCallback, func_get_args());
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
    return call_user_func_array(AbstractServicesTest::$chownCallback, func_get_args());
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
    return call_user_func_array(AbstractServicesTest::$mkdirCallback, func_get_args());
}

/**
 * Mocked OpenSSL function used to avoid failures if the extension might not be available in an testing environment
 *
 * @return string
 */
function openssl_csr_new()
{
    return '';
}

/**
 * Mocked OpenSSL function used to avoid failures if the extension might not be available in an testing environment
 *
 * @return string
 */
function openssl_csr_sign()
{
    return '';
}

/**
 * Mocked OpenSSL function used to avoid failures if the extension might not be available in an testing environment
 *
 * @return string
 */
function openssl_error_string()
{
    return call_user_func_array(AbstractServicesTest::$opensslErrorStringCallback, func_get_args());
}

/**
 * Mocked OpenSSL function used to avoid failures if the extension might not be available in an testing environment
 *
 * @return string
 */
function openssl_pkey_export()
{
    return '';
}

/**
 * Mocked OpenSSL function used to avoid failures if the extension might not be available in an testing environment
 *
 * @return string
 */
function openssl_pkey_new()
{
    return '';
}

/**
 * Mocked OpenSSL function used to avoid failures if the extension might not be available in an testing environment
 *
 * @return string
 */
function openssl_x509_export()
{
    return '';
}

/**
 * Callback wrapper for the umask function
 *
 * @return int
 */
function umask()
{
    return call_user_func_array(AbstractServicesTest::$umaskCallback, func_get_args());
}

/**
 * Abstract test class which overrides some functions for filesystem testing
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractServicesTest extends AbstractTest
{

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
    public static $chgrpCallback;

    /**
     * Callback wrapper for system function chown
     *
     * @var string $chownCallback
     */
    public static $chownCallback;

    /**
     * Callback wrapper for system function mkdir
     *
     * @var string $mkdirCallback
     */
    public static $mkdirCallback;

    /**
     * Callback wrapper for the OpenSSL function openssl_error_string
     *
     * @var string $opensslErrorStringCallback
     */
    public static $opensslErrorStringCallback;

    /**
     * Callback wrapper for system function umask
     *
     * @var string $umaskCallback
     */
    public static $umaskCallback;

    /**
     * Default constructor
     */
    public function setUp()
    {
        if (!is_dir($this->getTmpDir())) {
            \mkdir($this->getTmpDir());
        }

        // set default callbacks for our wrapped system functions
        self::$chownCallback = '\chgrp';
        self::$chownCallback = '\chown';
        self::$mkdirCallback = '\mkdir';
        self::$opensslErrorStringCallback = '\openssl_error_string';
        self::$umaskCallback = '\umask';
    }

    /**
     * Make sure to cleanup after our tests
     *
     * @return null
     */
    public function tearDown()
    {
        $this->clearTmpDir();
    }

    /**
     * Returns an initial context instance with a mock configuration.
     * Done again (instead of within AbstractService) to avoid \Stackable usage by omitting the autoloader management
     *
     * @return \AppserverIo\Appserver\Core\InitialContext Initial context with mock configuration
     */
    public function getMockInitialContext()
    {
        return new MockInitialContext($this->getAppserverNode());
    }
}
