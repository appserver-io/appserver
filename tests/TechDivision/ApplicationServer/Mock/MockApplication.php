<?php

/**
 * TechDivision\ApplicationServer\Mock\MockApplication
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Mock;

use TechDivision\ApplicationServer\AbstractApplication;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class MockApplication extends AbstractApplication
{

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @return \TechDivision\ApplicationServer\Interfaces\ApplicationInterface The connected application
     */
    public function connect()
    {
        return $this;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key)
    {
    }

    /**
     * Returns the absolute path to the applications temporary directory.
     *
     * @return string The app temporary directory
     */
    public function getTmpDir()
    {
    }

    /**
     * Returns the absolute path to the applications session directory.
     *
     * @return string The app session directory
     */
    public function getSessionDir()
    {
    }

    /**
     * Returns the absolute path to the applications cache directory.
     *
     * @return string The app cache directory
     */
    public function getCacheDir()
    {
    }

    /**
     * Injects the username the application should be executed with.
     *
     * @return string The username
     */
    public function getUser()
    {
    }

    /**
     * Injects the groupname the application should be executed with.
     *
     * @return string The groupname
     */
    public function getGroup()
    {
    }

    /**
     * Returns the umask the application should create files/directories with.
     *
     * @return string The umask
     */
    public function getUmask()
    {
    }

    /**
     * Returns the directory name.
     *
     * @return string The directory name
     */
    public function getName()
    {
    }

    /**
     * Returns the parend directory.
     *
     * @return \TechDivision\Naming\NamingDirectoryInterface
     */
    public function getParent()
    {
    }

    /**
     * Returns the scheme.
     *
     * @return string The scheme we want to use
     */
    public function getScheme()
    {
    }

    /**
     * Binds the passed instance with the name to the naming directory.
     *
     * @param string $name  The name to bind the object with
     * @param object $value The object instance to bind
     * @param array  $args  The array with the arguments
     *
     * @return void
     */
    public function bind($name, $value, array $args = array())
    {
    }

    /**
     * Binds the passed callback with the name to the naming directory.
     *
     * @param string   $name     The name to bind the callback with
     * @param callable $callback The callback to be invoked when searching for
     * @param array    $args     The array with the arguments passed to the callback when executed
     *
     * @return void
     * @see \TechDivision\Naming\NamingDirectoryInterface::bind()
     */
    public function bindCallback($name, callable $callback, array $args = array())
    {
    }


    /**
     * Queries the naming directory for the requested name and returns the value
     * or invokes the binded callback.
     *
     * @param string $name The name of the requested value
     * @param array  $args The arguments to pass to the callback
     *
     * @return mixed The requested value
     * @throws \TechDivision\Naming\NamingException Is thrown if the requested name can't be resolved in the directory
     */
    public function search($name, array $args = array())
    {
    }

    /**
     * Create and return a new naming subdirectory with the attributes
     * of this one.
     *
     * @param string $name   The name of the new subdirectory
     * @param array  $filter Array with filters that will be applied when copy the attributes
     *
     * @return \TechDivision\Naming\NamingDirectory The new naming subdirectory
     */
    public function createSubdirectory($name, array $filter = array())
    {
    }
}
