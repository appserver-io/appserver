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

use Rhumsaa\Uuid\Uuid;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Naming\NamingDirectoryInterface;
use AppserverIo\Appserver\Core\Traits\ThreadedContextTrait;

/**
 * Naming directory implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Naming\NamingDirectoryInterface $parent The parent directory
 * @property string                                           $scheme The binding string scheme
 * @property string                                           $name   The directory name
 * @property string                                           $serial The instance unique serial number
 */
class NamingDirectory extends GenericStackable implements NamingDirectoryInterface
{

    /**
     * Trait that provides threaded context functionality.
     */
    use ThreadedContextTrait;

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

        $this->directory = null;

        // create a UUID as prefix for dynamic object properties
        $this->serial = Uuid::uuid4()->toString();
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
     * @deprecated
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
        return $this->scheme;
    }

    /**
     * Binds the passed instance with the name to the naming directory.
     *
     * @param string $name  The name to bind the value with
     * @param mixed  $value The object instance to bind
     * @param array  $args  The array with the arguments
     *
     * @return void
     * @throws \AppserverIo\Psr\Naming\NamingException Is thrown if the value can't be bound ot the directory
     */
    public function bind($name, $value, array $args = array())
    {

        try {
            // strip off the schema
            $key = $this->stripSchema($name);

            // bind the value, if possible
            if ($this->hasAttribute($key) === false) {
                $this->setAttribute($key, array($value, $args));
                return;
            }

            // throw an exeception if the name has already been bound
            throw new \Exception(sprintf('A value with name %s has already been bound to naming directory %s', $name, $this->getIdentifier()));

        } catch (\Exception $e) {
            throw new NamingException(sprintf('Cant\'t bind %s to naming directory %s', $name, $this->getIdentifier()), null, $e);
        }
    }

    /**
     * Binds the passed callback with the name to the naming directory.
     *
     * @param string   $name     The name to bind the callback with
     * @param callable $callback The callback to be invoked when searching for
     * @param array    $args     The array with the arguments passed to the callback when executed
     *
     * @return void
     * @see \AppserverIo\Appserver\Naming\NamingDirectory::bind()
     */
    public function bindCallback($name, callable $callback, array $args = array())
    {
        $this->bind($name, $callback, $args);
    }

    /**
     * Binds a reference with the passed name to the naming directory.
     *
     * @param string $name      The name to bind the reference with
     * @param string $reference The name of the reference
     *
     * @return void
     * @see \AppserverIo\Appserver\Naming\NamingDirectory::bind()
     */
    public function bindReference($name, $reference)
    {
        $this->bindCallback($name, array(&$this, 'search'), array($reference, array()));
    }

    /**
     * Unbinds the named object from the naming directory.
     *
     * @param string $name The name of the object to unbind
     *
     * @return void
     */
    public function unbind($name)
    {

        try {
            // strip off the schema
            $key = $this->stripSchema($name);
            $this->removeAttribute($key);

        } catch (\Exception $e) {
            throw new NamingException(sprintf('Cant\'t unbind %s from naming directory %s', $name, $this->getIdentifier()), null, $e);
        }
    }

    /**
     * Queries the naming directory for the requested name and returns the value
     * or invokes the bound callback.
     *
     * @param string $name The name of the requested value
     * @param array  $args The arguments to pass to the callback
     *
     * @return mixed The requested value
     * @throws \AppserverIo\Psr\Naming\NamingException Is thrown if the requested name can't be resolved in the directory
     */
    public function search($name, array $args = array())
    {

        try {
            // strip off the schema
            $key = $this->stripSchema($name);

            // throw an exception if we can't find a value
            if ($this->hasAttribute($key) === false) {
                throw new \Exception(sprintf('Requested value %s has not been bound to naming directory %s', $name, $this->getIdentifier()));
            }

            // try to load the value, which may also be NULL
            if (($found = $this->getAttribute($key)) == null) {
                return;
            }

            // load the binded value/args
            list ($value, $bindArgs) = $found;

            // check if we've a callback method
            if (is_callable($value)) {
                // if yes, merge the params and invoke the callback
                foreach ($args as $arg) {
                    $bindArgs[] = $arg;
                }

                // invoke the callback
                return call_user_func_array($value, $bindArgs);
            }

            // simply return the value
            return $value;

        } catch (\Exception $e) {
            throw new NamingException(sprintf('Cant\'t resolve %s in naming directory %s', $name, $this->getIdentifier()), null, $e);
        }
    }

    /**
     * The unique identifier of this directory. That'll be build up
     * recursive from the scheme and the root directory.
     *
     * @return string The unique identifier
     * @see \AppserverIo\Storage\StorageInterface::getIdentifier()
     *
     * @throws \AppserverIo\Psr\Naming\NamingException
     */
    public function getIdentifier()
    {

        // check if we've a parent directory
        if ($parent = $this->getParent()) {
            return $parent->getIdentifier() . $this->getName() . '/';
        }

        // load the scheme to prerpare the identifier with
        if ($scheme = $this->getScheme()) {
            return $scheme . ':' . $this->getName();
        }

        // the root node needs a scheme
        throw new NamingException(sprintf('Missing scheme for naming directory', $this->getName()));
    }

    /**
     * Returns the root node of the naming directory tree.
     *
     * @return \AppserverIo\Psr\Naming\NamingDirectoryInterface The root node
     * @deprecated
     */
    public function findRoot()
    {
        return $this;
    }

    /**
     * Builds an array with a string representation of the naming
     * directories content.
     *
     * @param array $buffer The array to append the values to
     *
     * @return array The array with the naming directories string representation
     */
    public function toArray(array &$buffer = array())
    {
        return (array) $this;
    }

    /**
     * Returns a string representation of the naming directory
     *
     * @return string The string representation of the naming directory
     */
    public function __toString()
    {
        return implode(PHP_EOL, $this->toArray());
    }

    /**
     * Create and return a new naming subdirectory with the attributes
     * of this one.
     *
     * @param string $name   The name of the new subdirectory
     * @param array  $filter Array with filters that will be applied when copy the attributes
     *
     * @return \AppserverIo\Appserver\Naming\NamingDirectory The new naming subdirectory
     */
    public function createSubdirectory($name, array $filter = array())
    {

        try {
            // query whether or not the passed directory is relative or absolute
            if ($this->containsScheme($name)) {
                $this->setAttribute($this->stripSchema($name), '.');
            } else {
                // if the directory is relative, append it
                if ($directory = $this->getDirectory()) {
                    $directory = $this->appendDirectory($name);
                } else {
                    $this->setDirectory($directory = $name);
                }
                // bind the default value
                $this->bind($directory, '.');
            }

            // return the instance itself
            return $this;

        } catch (\Exception $e) {
            throw new NamingException(sprintf('Can\'t create subdirectory %s', $name), null, $e);
        }
    }

    /**
     * Return's the directory.
     *
     * @return string The directory
     */
    protected function getDirectory()
    {
        return $this->directory;
    }

    /**
     * Set's the passed directory.
     *
     * @param string $name The directory to set
     *
     * @return void
     */
    protected function setDirectory($name)
    {
        $this->directory = $name;
    }

    /**
     * Prepare's and return's the path either by stripping of the scheme
     * if it is absolute or append the name, if it is relative.
     *
     * @param string $name The name to prepare the directory with
     *
     * @return The prepared path
     */
    protected function prepareDirectory($name)
    {

        // query whether or not the passed name is a absolute directory
        if ($this->containsScheme($name)) {
            return $this->stripSchema($name);
        }

        // if not, only append it and return the new path
        return $this->appendDirectory($name);
    }

    /**
     * Append's the passed name to the actual path and return's the new path.
     *
     * @param string $name The name to append to the path
     *
     * @return string The new path, with the name appended
     */
    protected function appendDirectory($name)
    {
        return $this->directory .= sprintf('/%s', $name);
    }

    /**
     * Query whether the passed name contains the scheme or not.
     *
     * @param string $name The name to query for a scheme
     *
     * @return boolean TRUE if the name contains the scheme, else FALSE
     */
    protected function containsScheme($name)
    {
        return (strpos($name, sprintf('%s:', $this->getScheme())) === 0);
    }

    /**
     * Strip off the schema and return the directory part only.
     *
     * @param string $name The directory, maybe containing the scheme
     *
     * @return string The directory without the scheme
     */
    protected function stripSchema($name)
    {
        return str_replace(sprintf('%s:', $this->getScheme()), '', $name);
    }
}
