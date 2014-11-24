<?php

/**
 * AppserverIo\Appserver\MemcacheServer\MemcacheServerTest
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
 * @author     Philipp Dittert <pd@appserver.io>
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @link       https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */

namespace AppserverIo\Appserver\MemcacheServer;

/**
 * Memcache server test implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Philipp Dittert <pd@appserver.io>
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 * @link       https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
class MemcacheServerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests a incr request with a not existent key.
     *
     * @return void
     */
    public function testIncrementRequestWithNotExistentKey()
    {

        // initialize the stackable and the server
        $stackable = $this->getMock('\Stackable');
        $memcacheServer = new MemcacheServer($stackable);

        // initialize the request
        $memcacheRequest = $this->getMock('AppserverIo\Appserver\MemcacheProtocol\MemcacheRequest');
        $memcacheRequest->expects($this->once())
            ->method('getRequestAction')
            ->will($this->returnValue($requestAction = 'increment'));
        $memcacheRequest->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue($key = 'aIncrementKey'));
        $memcacheRequest->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data = '555'));

        // try to increment the value of a NOT existent key
        $memcacheServer->request($memcacheRequest);

        // check if the expected result
        $this->assertSame('NOT_FOUND', $memcacheServer->getResponse());
    }

    /**
     * Tests a incr request with a new value to set.
     *
     * @return void
     */
    public function testIncrementRequestWithNumericValue()
    {

        // initialize the stackable and the server
        $stackable = $this->getMock('\Stackable');
        $memcacheServer = new MemcacheServer($stackable);

        // initialize the request
        $memcacheRequest = $this->getMock('AppserverIo\Appserver\MemcacheProtocol\MemcacheRequest');
        $memcacheRequest->expects($this->once())
            ->method('getRequestAction')
            ->will($this->returnValue($requestAction = 'increment'));
        $memcacheRequest->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue($key = 'aIncrementKey'));
        $memcacheRequest->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($data = '555'));

        // preinitialize the stackable with the expected value
        $stackable['0-' . $key] = array('key' => $key, 'flags' => 0, 'exptime' => 100, 'bytes' => 1, 'value' => 1);

        // increment/set the value with 555
        $memcacheServer->request($memcacheRequest);

        // check if the expected result
        $this->assertSame($data, $memcacheServer->getResponse());
    }

    /**
     * Tests a incr request without a value.
     *
     * @return void
     */
    public function testIncrementRequestWithoutValue()
    {

        // initialize the stackable and the server
        $stackable = $this->getMock('\Stackable');
        $memcacheServer = new MemcacheServer($stackable);

        // initialize the request
        $memcacheRequest = $this->getMock('AppserverIo\Appserver\MemcacheProtocol\MemcacheRequest');
        $memcacheRequest->expects($this->once())
            ->method('getRequestAction')
            ->will($this->returnValue($requestAction = 'increment'));
        $memcacheRequest->expects($this->once())
            ->method('getKey')
            ->will($this->returnValue($key = 'aIncrementKey'));

        // preinitialize the stackable with the expected value
        $stackable['0-' . $key] = array('key' => $key, 'flags' => 0, 'exptime' => 100, 'bytes' => 1, 'value' => 1);

        // increment the value +1
        $memcacheServer->request($memcacheRequest);

        // check if the expected result
        $this->assertSame('2', $memcacheServer->getResponse());
    }
}
