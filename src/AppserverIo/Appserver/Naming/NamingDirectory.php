<?php

/**
 * \AppserverIo\Appserver\Naming\NamingDirectory
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

namespace AppserverIo\Appserver\Naming;

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Storage\StackableStorage;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Naming\NamingDirectoryInterface;

/**
 * Naming directory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property string $scheme The binding string scheme
 */
class NamingDirectory extends GenericStackable implements NamingDirectoryInterface
{

    /**
     * Trait which allows to bind instances and callbacks to the naming directory
     *
     * @var \AppserverIo\Appserver\Naming\BindingTrait
     */
    use BindingTrait;

    /**
     * Initialize the directory with a name and the parent one.
     *
     * @param string                                           $name   The directory name
     * @param \AppserverIo\Psr\Naming\NamingDirectoryInterface $parent The parent directory
     */
    public function __construct($name = null, NamingDirectoryInterface $parent = null)
    {

        // initialize the members
        $this->parent = $parent;
        $this->name = $name;

        // initialize the array for the attributes
        $this->data = array();
    }

    /**
     * Returns the directory name.
     *
     * @return string The directory name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the parend directory.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set the scheme, php or http for example
     *
     * @param string $scheme The scheme we want to use
     *
     * @return void
     */
    public function setScheme($scheme)
    {
        $this->scheme = $scheme;
    }

    /**
     * Returns the scheme.
     *
     * @return string The scheme we want to use
     */
    public function getScheme()
    {

        // if the parent directory has a schema, return this one
        if ($parent = $this->getParent()) {
            return $parent->getScheme();
        }

        // return our own schema
        return $this->scheme;
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
        return $this->data[$key];
    }

    /**
     * All values registered in the context.
     *
     * @return array The context data
     */
    public function getAttributes()
    {
        return $this->data;
    }

    /**
     * Queries if the attribute with the passed key is bound.
     *
     * @param string $key The key of the attribute to query
     *
     * @return boolean TRUE if the attribute is bound, else FALSE
     */
    public function hasAttribute($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Sets the passed key/value pair in the directory.
     *
     * @param string $key   The attributes key
     * @param mixed  $value Tha attribute to be bound
     *
     * @return void
     */
    public function setAttribute($key, $value)
    {

        // a bit complicated, but we're in a multithreaded environment
        if (isset($this->data[$key])) {
            throw new NamingException(sprintf('Attribute %s can\'t be overwritten', $key));
        }

        // set the key/value pair
        $data = $this->data;
        $data[$key] = $value;
        $this->data = $data;
    }

    /**
     * Returns the keys of the bound attributes.
     *
     * @return array The keys of the bound attributes
     */
    public function getAllKeys()
    {
        return array_keys($this->getAttributes());
    }

    /**
     * Removes the attribute with the passed key.
     *
     * @param string $key The key of the attribute to remove
     *
     * @return void
     */
    public function removeAttribute($key)
    {
        $data = $this->data;
        unset($data[$key]);
        $this->data = $data;
    }
}
