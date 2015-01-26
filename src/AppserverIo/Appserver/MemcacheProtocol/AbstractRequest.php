<?php

/**
 * AppserverIo\Appserver\MemcacheProtocol\AbstractRequest
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
 * The abstract base class for a cache entry implementation, e. g. memcache.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 * @link      https://github.com/memcached/memcached/blob/master/doc/protocol.txt
 */
abstract class AbstractRequest implements CacheRequest
{

    /**
     * Keeps response text that will sent to client after finish processing request.
     *
     * @var string
     */
    protected $response = '';

    /**
     * Flag is action is already and only Data are expected.
     *
     * @var boolean
     */
    protected $action;

    /**
     * Holds completion state of the Request.
     *
     * @var boolean
     */
    protected $complete = false;

    /**
     * The memcache flag value to enable/disable compression.
     *
     * @var integer
     */
    protected $flags;

    /**
     * Expiration date in seconds after a entty is invalid.
     *
     * @var integer
     */
    protected $expTime;

    /**
     * Value length in bytes.
     *
     * @var integer
     */
    protected $bytes = 1024;

    /**
     * Holds memcache command action for this request (e. g. 'set').
     *
     * @var string
     */
    protected $requestAction;

    /**
     * The data to be stored.
     *
     * @var string
     */
    protected $data;

    /**
     * The key to store the data with.
     *
     * @var string
     */
    protected $key;

    /**
     * The newline char to use.
     *
     * @var string
     */
    protected $newLine;

    /**
     * Constructor that reset's all attributes to
     * default values.
     *
     * @return void
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * Resets all attributes to the default values.
     *
     * @return void
     * @see \AppserverIo\Appserver\MemcacheProtocol\CacheRequest::reset()
     */
    public function reset()
    {
        $this->response = "";
        $this->complete = false;
        $this->flags = 0;
        $this->expTime = 0;
        $this->bytes = 1024;
        $this->requestAction = "";
        $this->data = "";
        $this->key = "";
        $this->newLine = "\r\n";
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
    public function getData()
    {
        return $this->data;
    }

    /**
     * Set's the instance flags.
     *
     * @param integer $flags The instance flags
     *
     * @return void
     */
    protected function setFlags($flags)
    {
        $this->flags = $flags;
    }

    /**
     * Return's the instance flags.
     *
     * @return integer The instance flags
     */
    public function getFlags()
    {
        return $this->flags;
    }

    /**
     * Set's the request action passed as parameter.
     *
     * @param string $value The request action for this instance
     *
     * @return void
     */
    protected function setRequestAction($value)
    {
        $this->requestAction = $value;
    }

    /**
     * Returns the request action of this instance.
     *
     * @return string The request instance
     */
    public function getRequestAction()
    {
        return $this->requestAction;
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
    public function getBytes()
    {
        return $this->bytes;
    }

    /**
     * Set's the key to store the data with
     *
     * @param string $key The key to store the data with
     *
     * @return void
     */
    protected function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * Return's the key to store the data with.
     *
     * @return string The key to store the data with
     */
    public function getKey()
    {
        return $this->key;
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
    public function getExpTime()
    {
        return $this->expTime;
    }

    /**
     * The new line value used.
     *
     * @return string The new line value
     */
    protected function getNewLine()
    {
        return $this->newLine;
    }

    /**
     * Set's current request state, TRUE for completed, else FALSE.
     *
     * @param boolean $value The request state
     *
     * @return void
     */
    protected function setComplete($value)
    {
        $this->complete = $value;
    }

    /**
     * Return's the current request state, TRUE for completed, else FALSE.
     *
     * @return boolean The current request state
     */
    protected function getComplete()
    {
        return $this->complete;
    }

    /**
     * Return's TRUE if the request is complete, ELSE false
     *
     * @return boolean TRUE if the request is complete, ELSE false
     * @see \AppserverIo\Appserver\MemcacheProtocol\CacheRequest::getComplete()
     */
    public function isComplete()
    {
        return $this->getComplete();
    }

    /**
     * Calculates the number of bytes necessary to load from the
     * socket to complete this request.
     *
     * @return integer The necessary number of bytes to read from the socket
     */
    public function bytesToRead()
    {
        return $this->getBytes() - strlen($this->getData());
    }
}
