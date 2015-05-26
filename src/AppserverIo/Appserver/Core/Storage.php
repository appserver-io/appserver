<?php

/**
 * AppserverIo\Appserver\Core;\Storage
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
*
* PHP version 5
*
* @author    Johann Zelger <jz@appserver.io>
* @copyright 2015 TechDivision GmbH <info@appserver.io>
* @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
* @link      https://github.com/appserver-io/appserver
* @link      http://www.appserver.io
*/

namespace AppserverIo\Appserver\Core;

/**
 * Simple userland storage implementation for execution service usage
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Storage
{

    /**
     * Defines internal data storage
     *
     * @var array
     */
    public $data = array();

    /**
     * Returns whole data storage
     *
     * @return array
     */
    public function all()
    {
        return $this->data;
    }

    /**
     * Sets value for specific key with optional subkey
     *
     * @param string $key    The key to set value for
     * @param mixed  $value  The value to set
     * @param string $subKey The optional subKey to set value for
     *
     * @return void
     * @Synchronized
     */
    public function set($key, $value, $subKey = null)
    {
        if (!is_null($subKey) && (is_array($this->data[$key]))) {
            $this->data[$key][$subKey] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Gets value for specific key with optional subkey
     *
     * @param string $key    The key to set value for
     * @param string $subKey The optional subKey to set value for
     *
     * @return mixed
     */
    public function get($key, $subKey = null)
    {
        if (!is_null($subKey) && (is_array($this->data[$key]))) {
            return $this->data[$key][$subKey];
        } else {
            return $this->data[$key];
        }
    }

    /**
     * Checks if specific key with options subkey exists
     *
     * @param string $key    The key to set value for
     * @param string $subKey The optional subKey to set value for
     *
     * @return bool
     */
    public function has($key, $subKey = null)
    {
        if (!is_null($subKey) && (is_array($this->data[$key]))) {
            return isset($this->data[$key][$subKey]);
        } else {
            return isset($this->data[$key]);
        }
    }

    /**
     * Deletes specific key with options subkey entry in data
     *
     * @param string $key    The key to set value for
     * @param string $subKey The optional subKey to set value for
     *
     * @return void
     */
    public function del($key, $subKey = null)
    {
        if (!is_null($subKey) && (is_array($this->data[$key]))) {
            unset($this->data[$key][$subKey]);
        } else {
            unset($this->data[$key]);
        }
    }
}
