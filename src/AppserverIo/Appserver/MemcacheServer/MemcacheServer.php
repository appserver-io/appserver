<?php

/**
 * AppserverIo\Appserver\MemcacheServer\MemcacheServer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Philipp Dittert <pd@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */

namespace AppserverIo\Appserver\MemcacheServer;

use AppserverIo\Appserver\MemcacheProtocol\CacheRequest;

/**
 * Memcache compatible cache implementation.
 *
 * @author    Philipp Dittert <pd@appserver.io>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
class MemcacheServer implements Cache
{

    /**
     * Holds the request instance.
     *
     * @var \AppserverIo\Appserver\MemacheProtocol\CacheRequest
     */
    protected $vo = null;

    /**
     * The string representing a the new line char.
     *
     * @var string
     */
    protected $newLine = "\r\n";

    /**
     * Prefix for saving multiple keys inside one Stackable.
     *
     * @var string
     */
    protected $storePrefix = "0-";

    /**
     * Keeps the garbage collector prefix value.
     *
     * @var string
     */
    protected $gcPrefix = "1";

    /**
     * Keeps response text that will sent to client after finish processing request.
     *
     * @var string
     */
    protected $response = "";

    /**
     * Keeps the following state of the connection values are: resume, reset, close.
     *
     * @var string
     */
    protected $state = "close";

    /**
     * Flag is action is already and only data are expected.
     *
     * @var boolean
     */
    protected $action = false;

    /**
     * Memcache flag value to enable/disable compression.
     *
     * @var integer
     */
    protected $flags = 0;

    /**
     * Seconds after a entry is invalid.
     *
     * @var integer
     */
    protected $expTime = 0;

    /**
     * Value length in bytes.
     *
     * @var integer
     */
    protected $bytes = 0;

    /**
     * holds the value to be stored.
     *
     * @var string
     */
    protected $data = "";

    /**
     * Holds the key of the value to be stored.
     *
     * @var string
     */
    protected $key = "";

    /**
     * Holds timestamp and key for invalidation of entrys.
     *
     * @var array
     */
    protected $invalidationArray = array();

    /**
     * Stackable array for sharing data between threads.
     *
     * @var \Stackable
    */
    public $store;

    /**
     * Initializes the instance with the store and the mutex value.
     *
     * @param \Stackable $store The store instance
     *
     * @return void
     */
    public function __construct(\Stackable $store)
    {
        $this->reset();
        $this->store = $store;
        $this->store[0] = array();
    }

    /**
     * Handle the the passed request VO.
     *
     * @param \AppserverIo\Appserver\MemacheProtocol\CacheRequest $vo The VO with the data to handle
     *
     * @return void
     */
    public function request(CacheRequest $vo)
    {
        // initialize the VO
        $this->vo = $vo;

        // build methodname from request action und "Action"
        $method = $vo->getRequestAction() . "Action";
        $this->$method();
    }

    /**
     * Memcache "get" action implementation.
     *
     * @return void
     * @todo API object should deleted after sending response to client
     */
    protected function getAction()
    {
        // read response from Store
        $response = $this->storeGet($this->getVO()->getKey());

        // set Response for client communication
        $this->setResponse($response);
    }

    /**
     * Memcache "set" Action
     *
     * @return void
     */
    protected function setAction()
    {
        $vo = $this->getVO();

        if ($this->storeSet($vo->getKey(), $vo->getFlags(), $vo->getExpTime(), $vo->getBytes(), $vo->getData())) {
            $this->setResponse("STORED");
            return;
        }

        $this->setResponse("NOT_STORED");

        // api object should deleted after sending response to client
        $this->setState("close");
    }

    /**
     * Memcache "add" Action
     *
     * @return void
     */
    protected function addAction()
    {
        $vo = $this->getVO();

        if (!$this->storeKeyExists($vo->getKey())) {
            if ($this->storeAdd($vo->getKey(), $vo->getFlags(), $vo->getExpTime(), $vo->getBytes(), $vo->getData())) {
                $this->setResponse("STORED");
                return;
            }
        }

        $this->setResponse("NOT_STORED");
    }

    /**
     * Memcache "replace" Action
     *
     * @return void
     */
    protected function replaceAction()
    {
        $vo = $this->getVO();

        if ($this->storeKeyExists($vo->getKey())) {
            if ($this->storeSet($vo->getKey(), $vo->getFlags(), $vo->getExpTime(), $vo->getBytes(), $vo->getData())) {
                $this->setResponse("STORED");
                return;
            }
        }

        $this->setResponse("NOT_STORED");
    }

    /**
     * Memcache "append" Action
     *
     * @return void
     */
    protected function appendAction()
    {
        $vo = $this->getVO();

        //check if Key exits
        if ($this->storeKeyExists($vo->getKey())) {
            //read Entry in Raw (array) Format for faster processing
            $ar = $this->storeRawGet($vo->getKey());
            //append new Data
            $ar['data'] .= $vo->getData();
            //save extends Entry to Store
            $this->storeRawSet($ar);

            $this->setResponse("STORED");
        } else {
            $this->setResponse("NOT_STORED");
        }
    }

    /**
     * Memcache "prepend" Action
     *
     * @return void
     */
    protected function prependAction()
    {
        $vo = $this->getVO();

        //check if Key exits
        if ($this->storeKeyExists($vo->getKey())) {
            //read Entry in Raw (array) Format for faster processing
            $ar = $this->storeRawGet($vo->getKey());
            //append new Data
            $ar['data'] = $vo->getData().$ar['data'];
            //save extends Entry to Store
            $this->storeRawSet($ar);

            $this->setResponse("STORED");
        } else {
            $this->setResponse("NOT_STORED");
        }
    }

    /**
     * Memcache "touch" Action
     *
     * @return void
     */
    protected function touchAction()
    {
        $vo = $this->getVO();

        //check if Key exits
        if ($this->storeKeyExists($vo->getKey())) {
            //read Entry in Raw (array) Format for faster processing
            $ar = $this->storeGet($vo->getKey());
            //append new Data
            $ar['expTime'] = $vo->getExpTime();
            //save extends Entry to Store
            $this->storeRawSet($ar);

            $this->setResponse("TOUCHED");
        } else {
            $this->setResponse("NOT_FOUND");
        }
    }

    /**
     * MemCache "delete" Action
     *
     * @return void
     */
    protected function deleteAction()
    {
        // read response from Store
        $response = $this->storeDelete($this->getVO()->getKey());
        // api object should deleted after sending response to client
        $this->setState("reset");
        // set Response for client communication
        $this->setResponse($response);
    }

    /**
     * Memcache "quit" Action
     *
     * @return void
     */
    protected function quitAction()
    {
        // api object should deleted after sending response to client
        $this->setState("close");

        // set Response for client communication
        $this->setResponse("");
    }

    /**
     * Memcache "increment" Action
     *
     * @return void
     */
    protected function incrementAction()
    {

        // read response from Store
        $response = $this->storeIncrement($this->getVO()->getKey(), $this->getVO()->getData());
        // set Response for client communication
        $this->setResponse($response);
    }

    /**
     * Memcache "decrement" Action
     *
     * @return void
     */
    protected function decrementAction()
    {
        // read response from Store
        $response = $this->storeDecrement($this->getVO()->getKey(), $this->getVO()->getData());
        // set Response for client communication
        $this->setResponse($response);
    }

    /**
     * MemCache "flush" action.
     *
     * @return void
     * @todo This has to be refactored, because we don't have to delete the values immediately, we've to mark them as deleted
     */
    protected function flushAllAction()
    {

        // lock the store and flush all values
        $this->store->lock();
        foreach ($this->store as $key => $value) {
            unset($this->store[$key]);
        }

        // unlock the store
        $this->store->unlock();

        // API object should deleted after sending response to client
        $this->setState("reset");

        // set Response for client communication
        $this->setResponse('OK');
    }

    /**
     * Collections the garbage by removing the cache entries from
     * the storage that has been expired.
     *
     * @return void
     */
    public function gc()
    {

        // initialize the method variables
        $startTime = microtime(true);
        $curTime = time();

        $this->store->lock();
        // save all values in "Invalidation" SubStore inside our Stackable
        $ar = $this->store[$this->getGCPrefix()];
        // delete all values in our Invalidation SubStore
        $this->store[$this->getGCPrefix()] = array();
        $this->store->unlock();

        // prepare the array with the invalid cache entries for the actual timestamp
        $asd = $this->invalidationArray[$curTime];

        // if an array with invalid entries has been found, invalidate them
        if (is_array($asd)) {
            foreach ($asd as $row) {
                $this->store->lock();
                unset($this->store[$this->getStorePrefix() . $row]);
                $this->store->unlock();
            }
        }

        // load the array with the values to be garbage collected
        if (is_array($ar)) {
            foreach ($ar as $key => $value) {
                if ($value != "0") {
                    $targetTime = $curTime + (int) $value;
                    $tmpar = $this->invalidationArray;
                    if (!$tmpar[$targetTime]) {
                        $tmpar[$targetTime] = array();
                    }
                    $tmpar[$targetTime][] = $key;
                    $this->invalidationArray = $tmpar;
                }
            }
        }

        // clear everything up and sleep
        $finishTime = microtime(true);
        $sleepTime = $this->calculateDeltaTime($startTime, $finishTime);
        usleep($sleepTime);
    }

    /**
     * Calculate difference between these Timestamaps, an substract it
     * from rounded up value of it self.
     *
     * @param float $startTime  The start time
     * @param float $finishTime The finish time
     *
     * @return integer The rounded delta
     */
    protected function calculateDeltaTime($startTime, $finishTime)
    {
        // calculate and round the value first
        $diffTime = $finishTime - $startTime;
        $roundedDiffTime = (float) ceil($diffTime);

        // we don't expect a longer runtime than 1 second
        // if we hit this value we return FALSE and our loop will run immediately again
        if ($roundedDiffTime > 1) {
            return false;
        }

        $deltaTime = $roundedDiffTime - $diffTime;
        // we need a integer microsecond value (1 million = 1 Second)
        $deltaTime = floor($deltaTime * 1000000);
        // add 1 microsecond (perhaps useful)
        $deltaTime = (int) $deltaTime + 1;

        // return the delta
        return $deltaTime;
    }

    /**
     * Return the array with cache entries to be invalidated.
     *
     * @return array The array with the invalid cache entries
     */
    protected function getInvalidationArray()
    {
        return $this->invalidationArray;
    }

    /**
     * Returns the value object instance.
     *
     * @return \AppserverIo\Appserver\MemcacheProtocol\CacheRequest
     */
    protected function getVO()
    {
        return $this->vo;
    }

    /**
     * Returns the response that will be sent back to the client.
     *
     * @return string The response that will be sent back
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Set response that will be sent back to the client.
     *
     * @param string $response The response to sent back
     *
     * @return void
     */
    protected function setResponse($response)
    {
        $this->response = $response;
    }

    /**
     * Return's following state of the connection, one of resume,
     * reset or close.
     *
     * @return string The state itself
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Set's following state of the connection, one of resume, reset or close.
     *
     * @param string $var The cache state
     *
     * @return void
     */
    protected function setState($var)
    {
        $this->state = $var;
    }

    /**
     * Set's the cache flags.
     *
     * @param integer $flags The cache flag to be set
     *
     * @return void
     */
    protected function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Return's the cache flags.
     *
     * @return integer The cache flags
     */
    protected function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set's the number of the bytes of the data.
     *
     * @param integer $bytes The number of bytes
     *
     * @return void
     */
    protected function setBytes($bytes)
    {
        $this->bytes = $bytes;
    }

    /**
     * Return's the number of bytes of the data.
     *
     * @return integer The nubmer of bytes
     */
    protected function getBytes()
    {
        return $this->bytes;
    }

    /**
     * Set's the expriation time for the data in seconds.
     *
     * @param integer $expTime The data's expiration time in seconds
     *
     * @return void
     */
    protected function setExpTime($expTime)
    {
        $this->expTime = $expTime;
    }

    /**
     * Return's the expiration time for the data in seconds.
     *
     * @return integer The data's expiration time in seconds
     */
    protected function getExpTime()
    {
        return $this->expTime;
    }

    /**
     * The new line value used.
     *
     * @return string The new line value
     */
    public function getNewLine()
    {
        return $this->newLine;
    }

    /**
     * Appends the data to this instance.
     *
     * @param string $data The data to append
     *
     * @return void
     */
    protected function setData($data)
    {
        $this->data .= $data;
    }

    /**
     * Returns the data of this instance.
     *
     * @return string The instance data
     */
    protected function getData()
    {
        return $this->data;
    }

    /**
     * Set's the key of the value to be stored.
     *
     * @param string $key The key of the value to be stored
     *
     * @return void
     */
    protected function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Return's the key of the value to be stored.
     *
     * @return string The key of the value to be stored
     */
    protected function getKey()
    {
        return $this->key;
    }

    /**
     * Return's the store instance.
     *
     * @return \Stackable The store instance itself
     */
    protected function getStore()
    {
        return $this->store;
    }

    /**
     * Check if action value is set.
     *
     * @return boolean TRUE if the action has been set, else FALSE
     */
    protected function isAction()
    {
        return $this->action;
    }

    /**
     * Set action attribute to TRUE (disabling is not important).
     *
     * @return void
     */
    protected function setIsAction()
    {
        $this->action = true;
    }

    /**
     * Return's the store prefix.
     *
     * @return string The store prefix
     */
    protected function getStorePrefix()
    {
        return $this->storePrefix;
    }

    /**
     * Return's the garbage collector prefix.
     *
     * @return string The garbage collector prefix
     */
    protected function getGCPrefix()
    {
        return $this->gcPrefix;
    }

    /**
     * Reset all attributes for reusing the object.
     *
     * @return void
     */
    public function reset()
    {
        $this->newLine = "\r\n";
        $this->response = "";
        $this->state = "reset";
        $this->action = false;
        $this->flags = 0;
        $this->expTime = 0;
        $this->bytes = 0;
        $this->data = "";
        $this->key = "";
        $this->vo = null;
    }

    /**
     * Returns the value with the passed key from the store.
     *
     * @param string $key The key to return the value for
     *
     * @return string The value for the passed key
     */
    protected function storeGet($key)
    {
        $result = "";
        $this->store->lock();
        $s = $this->store[$this->getStorePrefix() . $key];
        $this->store->unlock();
        if ($s) {
            $result = "VALUE " . $s['key'] . " ";
            $result .= $s['flags'] . " ";
            $result .= $s['bytes'] . $this->getNewLine();
            $result .= $s['value'] . $this->getNewLine();
        }
        $result .= "END";
        return $result;
    }

    /**
     * Checks if the passed key already exists in store.
     *
     * @param string $key The key to check for
     *
     * @return boolean TRUE if the value has already been stored, else FALSE
     */
    protected function storeKeyExists($key)
    {
        $this->store->lock();
        $s = $this->store[$this->getStorePrefix() . $key];
        $this->store->unlock();
        if ($s) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Adds a new value build from the passed data in store.
     *
     * @param string  $key     The key to store the value with
     * @param integer $flags   Flags to compress/uncompress the value
     * @param integer $expTime The expiration time in seconds
     * @param integer $bytes   The bytes of the value
     * @param string  $value   The value itself
     *
     * @return boolean TRUE if the value has been added, else FALSE
     * @todo Refactor invalidator code because of problems with stackable array (line 429+)
     */
    protected function storeAdd($key, $flags, $expTime, $bytes, $value)
    {

        // check if the data has already been set in cache
        if (!$this->storeKeyExists($key)) {
            return $this->storeSet($key, $flags, $expTime, $bytes, $value);
        }

        // return FALSE if the data has already been found in cache
        return false;
    }

    /**
     * Store a new value build from the passed data in store.
     *
     * @param string  $key     The key to store the value with
     * @param integer $flags   Flags to compress/uncompress the value
     * @param integer $expTime The expiration time in seconds
     * @param integer $bytes   The bytes of the value
     * @param string  $value   The value itself
     *
     * @return boolean TRUE if the value has been added, else FALSE
     * @todo Refacotr invalidator code because of problems with stackable array (line 429+)
     */
    protected function storeSet($key, $flags, $expTime, $bytes, $value)
    {

        // initialize the array with the data
        $ar = array();
        $ar['key'] = $key;
        $ar['flags'] = $flags;
        $ar['exptime'] = $expTime;
        $ar['bytes'] = $bytes;
        $ar['value'] = $value;

        // lock the container and try to store the data
        $this->store->lock();
        $this->store[$this->getStorePrefix() . $key] = $ar;
        // add for every new entry a garbage collector Entry - another thread will keep a eye on it
        $invalidator = $this->store[$this->getGCPrefix()];
        $invalidator[$key] = $expTime;
        $this->store[$this->getGCPrefix()] = $invalidator;
        $this->store->unlock();

        // return TRUE if the data has been stored successfully
        return true;
    }

    /**
     * Delete's the value with the passed key from the store.
     *
     * @param string $key The key of the value to delete
     *
     * @return string The result as string
     */
    protected function storeDelete($key)
    {
        $this->store->lock();
        if ($this->store[$this->getStorePrefix() . $key]) {
            unset($this->store[$this->getStorePrefix() . $key]);
            $result = "DELETED";
        } else {
            $result = "NOT_FOUND";
        }
        $this->store->unlock();
        return $result;
    }

    /**
     * Return's entry from store in raw (array) format.
     *
     * @param string $key The key to return the entry for
     *
     * @return array The entry itself
     */
    protected function storeRawGet($key)
    {
        $this->store->lock();
        $s = $this->store[$this->getStorePrefix() . $key];
        $this->store->unlock();
        return $s;
    }

    /**
     * Set's entry from store in raw (array) format.
     *
     * @param array $ar The array with the key to return the value for
     *
     * @return void
     */
    protected function storeRawSet($ar)
    {
        $this->store->lock();
        $this->store[$this->getStorePrefix() . $ar['key']] = $ar;
        $this->store->unlock();
    }

    /**
     * The memcache "incr" action (that increments the variable with the passed key by +1).
     *
     * @param string      $key      The key of the value to increment
     * @param string|null $newValue The value to increment if value is not a number
     *
     * @return void
     */
    protected function storeIncrement($key, $newValue = null)
    {

        $this->store->lock();

        // first we check if we have a value with the passed key in our storage
        if ($value = $this->store[$this->getStorePrefix() . $key]) {

            // if existing data is numeric and new value is empty we increment by 1
            if (is_numeric($value['value']) && $newValue == null) {
                $value['value'] = (integer) $value['value'] + 1;
            } else {
                $value['value'] = $newValue;
            }

            // calculate the bytes and store the data
            $value['bytes'] = strlen($value['value']);
            $this->store[$this->getStorePrefix() . $key] = $value;

            // result is the new value
            $result = $value['value'];

        } else {
            $result = "NOT_FOUND";
        }

        $this->store->unlock();
        return (string) $result;
    }

    /**
     * The memcache "decr" action (that decrements the variable with the passed key by -1).
     *
     * @param string      $key      The key of the value to decrement
     * @param string|null $newValue The value to increment if value is not a number
     *
     * @return void
     */
    protected function storeDecrement($key, $newValue = null)
    {

        $this->store->lock();

        // first we check if we have a value with the passed key in our storage
        if ($value = $this->store[$this->getStorePrefix() . $key]) {

            // if existing data is numeric, the value is > 0 and new value is empty we decrement by 1
            if (is_numeric($value['value']) && $value['value'] > 0 && $newValue == null) {
                $value['value'] = (integer) $value['value'] - 1;
            } else {
                $value['value'] = $newValue;
            }

            // calculate the bytes and store the data
            $value['bytes'] = strlen($value['value']);
            $this->store[$this->getStorePrefix() . $key] = $value;

            // result is the new value
            $result = $value['value'];

        } else {
            $result = "NOT_FOUND";
        }

        $this->store->unlock();
        return (string) $result;
    }
}
