<?php

/**
 * AppserverIo\Appserver\MemcacheProtocol\MemcacheRequest
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
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */

namespace AppserverIo\Appserver\MemcacheProtocol;

/**
 * This is the default implementation for a memcache/memcached compatible
 * value object that contains the request data for the CRUD methods.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
class MemcacheRequest extends AbstractRequest
{

    /**
     * Central method for pushing data into VO object.
     *
     * @param string $line The actual request instance
     *
     * @return void
     */
    public function push($line)
    {

        // check if the intial connect is already initiated and only data are expected
        // else parse this request and select fitting action
        if ($this->getRequestAction()) {
            $this->pushData($line);
        } else {
            // parse the request data
            $var = $this->parseRequest($line);

            // if the request is NOT empty
            if (empty($var)) {
                throw new \Exception('Empty request data found');
            }

            // check the action to be invoked
            switch ($var[0]) {

                case 'add':
                    $this->setAction($var);
                    break;

                case 'set':
                    $this->setAction($var);
                    break;

                case 'replace':
                    $this->setAction($var);
                    break;

                case 'get':
                    $this->getAction($var);
                    break;

                case 'delete':
                    $this->deleteAction($var);
                    break;

                case 'quit':
                    $this->quitAction($var);
                    break;

                case 'incr':
                    $this->incrementAction($var);
                    break;

                case 'decr':
                    $this->decrementAction($var);
                    break;

                case 'flush_all':
                    $this->flushAllAction($var);
                    break;

                default:
                    throw new \Exception("Found unknown request action $var[0]");
                    break;
            }

            // clear the request data
            unset($var);
        }
    }

    /**
     * Parse request and return data as array.
     *
     * @param string $line The request string to be parsed
     *
     * @return array The data found in the request
     */
    protected function parseRequest($line)
    {

        // emtpy request or only a new line is not allowed
        if ($line == false || $line == "\r" || $line == "\n" || $line == "\r\n") {
            return array();
        }

        // try to read action
        return explode(' ', trim($line));
    }

    /**
     * The memcache "get" action (that returns the value
     * with the requested key from the cache).
     *
     * The array MUST have the following structure:
     *
     * array(
     *     1  => 'key' // the key to return the value for
     * )
     *
     * @param array $request The actual request instance
     *
     * @return void
     * @link http://de1.php.net/manual/de/memcached.get.php
     */
    protected function getAction($request)
    {
        $this->setKey($request[1]);
        $this->setRequestAction('get');
        $this->setComplete(true);
    }

    /**
     * The memcache "delete" action (that removes
     * the entry from the cache).
     *
     * The array MUST have the following structure:
     *
     * array(
     *     1  => 'key' // the key to delete the value
     * )
     *
     * @param array $request The actual request instance
     *
     * @return void
     * @link http://de1.php.net/manual/de/memcached.delete.php
     */
    protected function deleteAction($request)
    {
        $this->setKey($request[1]);
        $this->setRequestAction('delete');
        $this->setComplete(true);
    }

    /**
     * The memcache "set" action (that set's the data passed
     * in the array to the cache).
     *
     * The array MUST have the following structure:
     *
     * array(
     *     1      => 'key',     // the key to store the value with
     *     2      => 'flag',    // enable/disable compression
     *     3      => 'expire'   // expiration time in seconds
     *     4      => 'bytes'    // number of bytes of the content
     *     'data' => 'data'     // the data to be stored
     * )
     *
     * @param array $request The actual request instance
     *
     * @return void
     * @throws \Exception Is thrown if the data contains an invalid flag
     * @throws \Exception Is thrown if the data contains an invalid expiration time
     * @throws \Exception Is thrown if the data has NOT the specified length in byte
     * @link http://de1.php.net/manual/de/memcached.set.php
     */
    protected function setAction($request)
    {

        $this->setRequestAction('set');
        $this->setKey($request[1]);

        // validate Flag Value
        if (is_numeric($request[2])) {
            $this->setFlags($request[2]);
        } else {
            throw new \Exception("CLIENT_ERROR bad command line format");
        }

        // validate Expiretime value
        if (is_numeric($request[3])) {
            $this->setExpTime($request[3]);
        } else {
            throw new \Exception("CLIENT_ERROR found invalid expiration time");
        }

        // validate data-length in bytes
        if (is_numeric($request[4])) {
            $this->setBytes($request[4]);
        } else {
            throw new \Exception("CLIENT_ERROR bad data chunk");
        }
    }

    /**
     * The memcache "quit" action (that closes the client connection).
     *
     * @param array $request The actual request instance
     *
     * @return void
     */
    protected function quitAction($request)
    {
        $this->setRequestAction('quit');
        $this->setComplete(true);
    }

    /**
     * The memcache "incr" action (that increments the variable with the key passed in the request by +1).
     *
     * @param array $request The actual request instance
     *
     * @return void
     */
    protected function incrementAction($request)
    {

        $this->setKey($request[1]);

        // set value to specify
        if (isset($request[2])) {
            $this->setData($request[2]);
        }

        $this->setRequestAction('increment');
        $this->setComplete(true);
    }

    /**
     * The memcache "decr" action (that decrements the variable with the key passed in the request by -1).
     *
     * @param array $request The actual request instance
     *
     * @return void
     */
    protected function decrementAction($request)
    {
        $this->setKey($request[1]);

        // set value to specify
        if (isset($request[2])) {
            $this->setData($request[2]);
        }

        $this->setRequestAction('decrement');
        $this->setComplete(true);
    }

    /**
     * The memcache "flush" action (that marks
     * all invalid).
     *
     * @param array $request The actual request instance
     *
     * @return void
     * @link http://de1.php.net/manual/en/memcached.flush.php
     */
    protected function flushAllAction($request)
    {
        $this->setRequestAction('flushAll');
        $this->setComplete(true);
    }

    /**
     * Method for validating "value" data for "set" and "add" action
     * and check's if bytes value is reached and set state/response.
     *
     * @param string $data The data to push (to the cache)
     *
     * @return boolean TRUE if the data has the correct length or is empty
     * @throws \Exception Is thrown if the data has NOT the specified length in byte
     */
    protected function pushData($data)
    {

        // first check if we are at the strings end
        if ($data == $this->getNewline() && strlen($this->getData()) == $this->getBytes()) {
            $this->setComplete(true);
            return true;
        }

        // set the data
        $this->setData(substr($data, 0, strlen($data) - 2));

        // check if data has the specified length
        if (strlen($this->getData()) == $this->getBytes() || $this->getBytes() == null) {
            $this->setComplete(true);
            return true;
        }

        // if NOT throw an exception
        if (strlen($this->getData()) > $this->getBytes()) {
            throw new \Exception("CLIENT_ERROR bad data chunk");
        }
    }
}
