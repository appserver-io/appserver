<?php

/**
 * AppserverIo\Appserver\Application\Mock\MockClassLoader
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
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application\Mock;

/**
 * Test implementation for the class loader.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class MockClassLoader extends \Stackable
{

    /**
     * Initializes the mock class loader.
     */
    public function __construct()
    {
        $this->registered = false;
    }

    /**
     * Installs this class loader on the SPL autoload stack.
     *
     * @param bool $throw   If register should throw an exception or not
     * @param bool $prepend If register should prepend
     *
     * @return void
     */
    public function register($throw = true, $prepend = false)
    {
        $this->registered = true;
    }

    /**
     * Returns TRUE if the class loaders register() method has been called.
     *
     * @return boolean TRUE if the class loader has been registered
     */
    public function isRegistered()
    {
        return $this->registered;
    }

    /**
     * Loads the given class or interface.
     *
     * @param string $className The name of the class to load.
     *
     * @return void
     * @todo Has to be refactored to improve performance
     */
    public function loadClass($className)
    {
    }
}
