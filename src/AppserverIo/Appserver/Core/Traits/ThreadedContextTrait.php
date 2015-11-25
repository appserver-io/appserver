<?php

/**
 * \AppserverIo\Appserver\Core\Traits\ThreadedContextTrait
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

namespace AppserverIo\Appserver\Core\Traits;

/**
 * Trait implementation provides context functionality for threaded objects.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ThreadedContextTrait
{

    /**
     * Returns the serial of the instance.
     *
     * @return string The serial
     */
    protected function getSerial()
    {
        return $this->serial;
    }

    /**
     * Creates a unique identifier to append attributes to the internal property table.
     *
     * @param string $key The key used to create a unique identifier
     *
     * @return string The unique identifier
     */
    protected function maskKey($key)
    {
        return sprintf('%s-%s', $this->getSerial(), $key);
    }

    /**
     * Umasks the unique key.
     *
     * @param string $key The unique key to unmask
     *
     * @return string The unmasked key
     */
    protected function unmaskKey($key)
    {
        return str_replace(sprintf('%s-', $this->getSerial()), '', $key);
    }

    /**
     * All values registered in the context.
     *
     * @return array The context data
     */
    public function getAttributes()
    {

        // initialize the array with the attributes
        $attributes = array();

        // prepare the pattern to filter the attributes
        $pattern = sprintf('%s-*', $this->getSerial());

        // prepare the array with the attributes
        foreach ($this as $key => $value) {
            if (fnmatch($pattern, $key)) {
                $attributes[$key] = $value;
            }
        }

        // return the attributes
        return $attributes;
    }

    /**
     * Returns the keys of the bound attributes.
     *
     * @return array The keys of the bound attributes
     */
    public function getAllKeys()
    {

        // initialize the array with keys of all attributes
        $keys = array();

        // prepare the pattern to filter the attributes
        $pattern = sprintf('%s-*', $this->getSerial());

        // prepare the array with the attribute keys
        foreach ($this as $key => $value) {
            if (fnmatch($pattern, $key)) {
                $keys[] = $this->unmaskKey($key);
            }
        }

        // return the attribute keys
        return $keys;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     * @see \AppserverIo\Psr\Context\ContextInterface::getAttribute()
     */
    public function getAttribute($key)
    {

        // query whether the identifier exists or not
        if (isset($this[$uid = $this->maskKey($key)])) {
            return $this[$uid];
        }
    }

    /**
     * Sets the passed key/value pair in the directory.
     *
     * @param string $key   The attributes key
     * @param mixed  $value Tha attribute to be bound
     *
     * @return void
     * @throws \Exception Is thrown if the value already exists
     */
    public function setAttribute($key, $value)
    {

        // if the value is already set, throw an exception
        if (isset($this[$uid = $this->maskKey($key)])) {
            throw new \Exception(
                sprintf('A value with key %s has already been set in application %s', $key, $this->getName())
            );
        }

        // append the value to the application
        $this[$uid] = $value;
    }

    /**
     * Queries whether the attribute already exists or not.
     *
     * @param string $key The attribute to query for
     *
     * @return boolean TRUE if the attribute already exists, else FALSE
     */
    public function hasAttribute($key)
    {
        return isset($this[$this->maskKey($key)]);
    }

    /**
     * Removes the attribue from the application.
     *
     * @param string $key The attribute to remove
     *
     * @return void
     */
    public function removeAttribute($key)
    {
        if (isset($this[$uid = $this->maskKey($key)])) {
            unset($this[$uid]);
        }
    }
}
