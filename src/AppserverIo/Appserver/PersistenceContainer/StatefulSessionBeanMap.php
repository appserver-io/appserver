<?php

/**
 * \AppserverIo\Appserver\PersistenceContainer\StatefulSessionBeanMap
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Lang\String;
use AppserverIo\Lang\Float;
use AppserverIo\Lang\Integer;
use AppserverIo\Lang\Boolean;
use AppserverIo\Lang\NullPointerException;
use AppserverIo\Collections\MapInterface;
use AppserverIo\Collections\InvalidKeyException;
use AppserverIo\Collections\IndexOutOfBoundsException;

/**
 * A hash map implementation designed to handle stateful session beans.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StatefulSessionBeanMap extends GenericStackable implements MapInterface
{

    /**
     * Array containing the lifetime of the items.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $lifetime;

    /**
     * The items the map contains.
     *
     * @var \AppserverIo\Storage\GenericStackable
     */
    protected $items;

    /**
     * Initializes the map.
     */
    public function __construct()
    {
        $this->items = new GenericStackable();
        $this->lifetime = new GenericStackable();
    }

    /**
     * This method adds the passed object with the passed key
     * to the instance.
     *
     * @param mixed   $key      The key to add the passed value under
     * @param mixed   $object   The object to add to the instance
     * @param integer $lifetime The items lifetime
     *
     * @return null
     * @throws \AppserverIo\Collections\InvalidKeyException Is thrown if the passed key is NOT an primitive datatype
     * @throws \AppserverIo\Lang\NullPointerException Is thrown if the passed key is null or not a flat datatype like Integer, String, Double or Boolean
     */
    public function add($key, $object, $lifetime = 0)
    {
        // check if a key has been passed
        if (is_null($key)) {
            throw new NullPointerException('Passed key is null');
        }
        // check if lifetime is of type integer
        if (is_integer($lifetime) === false) {
            throw new InvalidLifetimeException(sprintf('Passed lifetime must be an integer, but is %s instead', $lifetime));
        }
        // check if a primitive datatype is passed
        if (is_integer($key) || is_string($key) || is_double($key) || is_bool($key)) {
            // add the item and lifetime to the array
            $this->items[$key] = $object;
            // add a lifetime if passed lifetime > 0
            if ($lifetime > 0) {
                $this->lifetime[$key] = time() + $lifetime;
            }
            // and return
            return;
        }
        // check if an object is passed
        if (is_object($key)) {
            if ($key instanceof String) {
                $newKey = $key->stringValue();
            } elseif ($key instanceof Float) {
                $newKey = $key->floatValue();
            } elseif ($key instanceof Integer) {
                $newKey = $key->intValue();
            } elseif ($key instanceof Boolean) {
                $newKey = $key->booleanValue();
            } elseif (method_exists($key, '__toString')) {
                $newKey = $key->__toString();
            } else {
                throw new InvalidKeyException('Passed key has to be a primitive datatype or has to implement the __toString() method');
            }
            // add the item and lifetime to the array
            $this->items[$newKey] = $object;
            // add a lifetime if passed lifetime > 0
            if ($lifetime > 0) {
                $this->lifetime[$newKey] = time() + $lifetime;
            }
            // and return
            return;
        }
        throw new InvalidKeyException('Passed key has to be a primitive datatype or has to implement the __toString() method');
    }

    /**
     * This method returns the element with the passed key
     * from the Collection.
     *
     * @param mixed $key Holds the key of the element to return
     *
     * @return mixed The requested element
     * @throws \AppserverIo\Collections\InvalidKeyException Is thrown if the passed key is NOT an integer
     * @throws \AppserverIo\Lang\NullPointerException Is thrown if the passed key OR value are NULL
     * @throws \AppserverIo\Collections\IndexOutOfBoundsException Is thrown if no element with the passed key exists in the Collection
     * @see \AppserverIo\Collections\Collection::get($key)
     */
    public function get($key)
    {
        // check if a key has been passed
        if (is_null($key)) {
            throw new NullPointerException('Passed key is null');
        }
        // check if a primitive datatype is passed
        if (is_integer($key) || is_string($key) || is_double($key) || is_bool($key)) {
            // return the value for the passed key, if it exists
            if (array_key_exists($key, $this->items) && !$this->isTimedOut($key)) {
                // item is available and NOT timed out
                return $this->items[$key];
            } elseif (array_key_exists($key, $this->items) && $this->isTimedOut($key)) {
                // item is available, but timed out
                return;
            } else {
                // item is generally not available
                throw new IndexOutOfBoundsException(sprintf('Index %s out of bounds', $key));
            }
        }
        // check if an object is passed
        if (is_object($key)) {
            if ($key instanceof String) {
                $newKey = $key->stringValue();
            } elseif ($key instanceof Float) {
                $newKey = $key->floatValue();
            } elseif ($key instanceof Integer) {
                $newKey = $key->intValue();
            } elseif ($key instanceof Boolean) {
                $newKey = $key->booleanValue();
            } elseif (method_exists($key, '__toString')) {
                $newKey = $key->__toString();
            } else {
                throw new InvalidKeyException('Passed key has to be a primitive datatype or has to implement the __toString() method');
            }
            // return the value for the passed key, if it exists
            if (array_key_exists($newKey, $this->items) && !$this->isTimedOut($newKey)) {
                // item is available and NOT timed out
                return $this->items[$newKey];
            } elseif (array_key_exists($newKey, $this->items) && $this->isTimedOut($newKey)) {
                // item is available, but timed out
                return;
            } else {
                // item is generally not available
                throw new IndexOutOfBoundsException(sprintf('Index %s out of bounds', $newKey));
            }
        }
        throw new InvalidKeyException('Passed key has to be a primitive datatype or has to implement the __toString() method');
    }

    /**
     * This method removes the element with the passed
     * key, that has to be an integer, from the
     * IndexedCollection.
     *
     * @param mixed    $key          Holds the key of the element to remove
     * @param callable $beforeRemove Is called before the item will be removed
     *
     * @return void
     * @throws \AppserverIo\Collections\InvalidKeyException Is thrown if the passed key is NOT an integer
     * @throws \AppserverIo\Lang\NullPointerException Is thrown if the passed key is NULL
     * @throws \AppserverIo\Collections\IndexOutOfBoundsException Is thrown if no element with the passed key exists in the Collection
     */
    public function remove($key, callable $beforeRemove = null)
    {
        // check if a key has been passed
        if (is_null($key)) {
            throw new NullPointerException('Passed key is null');
        }
        // check if a primitive datatype is passed
        if (is_integer($key) || is_string($key) || is_double($key) || is_bool($key)) {
            if (array_key_exists($key, $this->items)) {
                // invoke the callback before
                if (is_callable($beforeRemove)) {
                    call_user_func($beforeRemove, $this->items[$key]);
                }
                // remove the item
                unset($this->items[$key]);
                // return the lifetime is set
                if (isset($this->lifetime[$key])) {
                    unset($this->lifetime[$key]);
                }
                // return the instance
                return $this;
            } else {
                throw new IndexOutOfBoundsException('Index ' . $key . ' out of bounds');
            }
        }
        // check if an object is passed
        if (is_object($key)) {
            if ($key instanceof String) {
                $newKey = $key->stringValue();
            } elseif ($key instanceof Float) {
                $newKey = $key->floatValue();
            } elseif ($key instanceof Integer) {
                $newKey = $key->intValue();
            } elseif ($key instanceof Boolean) {
                $newKey = $key->booleanValue();
            } elseif (method_exists($key, '__toString')) {
                $newKey = $key->__toString();
            } else {
                throw new InvalidKeyException('Passed key has to be a primitive datatype or ' . 'has to implement the __toString() method');
            }
            if (array_key_exists($newKey, $this->items)) {
                // invoke the callback before
                if (is_callable($beforeRemove)) {
                    call_user_func($beforeRemove, $this->items[$newKey]);
                }
                // remove the item
                unset($this->items[$newKey]);
                // return the lifetime is set
                if (isset($this->lifetime[$newKey])) {
                    unset($this->lifetime[$newKey]);
                }
                // returns the instance
                return $this;
            } else {
                throw new IndexOutOfBoundsException('Index ' . $newKey . ' out of bounds');
            }
        }
        throw new InvalidKeyException('Passed key has to be a primitive datatype or ' . 'has to implement the __toString() method');
    }

    /**
     * Returns TRUE if an lifetime value for the passed key is available
     * and the item has timed out.
     *
     * @param mixed $key The key of the item the lifetime check is requested
     *
     * @return boolean TRUE if the item has timed out, else FALSE
     */
    protected function isTimedOut($key)
    {
        // if the item is available and has timed out, return TRUE
        if (array_key_exists($key, $this->lifetime) && $this->lifetime[$key] < time()) {
            return true;
        }
        // else return FALSE
        return false;
    }

    /**
     * Returns the lifetime of the items the instance contains.
     *
     * @return array The array with the items and their lifetime
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * This method returns the internal array
     * with the keys and the related values.
     *
     * @return array Holds the array with keys and values
     * @see \AppserverIo\Collections\Map::toIndexedArray()
     */
    public function toIndexedArray()
    {
        $array = array();
        foreach ($this->items as $key => $item) {
            $array[$key] = $item;
        }
        return $array;
    }

    /**
     * This method returns the number of entries of the Collection.
     *
     * @return integer The number of entries
     */
    public function size()
    {
        return sizeof($this->items);
    }

    /**
     * This method initializes the Collection and removes
     * all exsiting entries.
     *
     * @return void
     */
    public function clear()
    {
        $this->items = new GenericStackable();
    }

    /**
     * This returns true if the Collection has no
     * entries, otherwise false.
     *
     * @return boolean
     */
    public function isEmpty()
    {
        return $this->size() > 0;
    }

    /**
     * This method returns an array with the
     * items of the Dictionary.
     *
     * The keys are lost in the array.
     *
     * @return array Holds an array with the items of the Dictionary
     */
    public function toArray()
    {
        $array = array();
        foreach ($this->items as $item) {
            $array[] = $item;
        }
        return $array;
    }

    /**
     * This method appends all elements of the
     * passed array to the Collection.
     *
     * @param array $array Holds the array with the values to add
     *
     * @return \AppserverIo\Collections\CollectionInterface The instance
     */
    public function addAll($array)
    {
        $this->items->merge($array);
    }

    /**
     * This method checks if the element with the passed
     * key exists in the Collection.
     *
     * @param mixed $key Holds the key to check the elements of the Collection for
     *
     * @return boolean Returns true if an element with the passed key exists in the Collection
     * @throws \AppserverIo\Collections\InvalidKeyException Is thrown if the passed key is invalid
     * @throws \AppserverIo\Lang\NullPointerException Is thrown if the passed key is NULL
     */
    public function exists($key)
    {
        return isset($this->items[$key]);
    }
}
