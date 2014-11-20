<?php

/**
 * AppserverIo\Appserver\Application\Mock\MockManager
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

use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

/**
 * Test implementation for the virtual host.
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
class MockManager extends \Stackable implements ManagerInterface
{

    /**
     * The managers default unique identifier.
     *
     * @var string
     */
    const IDENTIFIER = 'AppserverIo\Appserver\Application\Mock\MockManager';

    /**
     * Initializes the mock manager with a unique identifier.
     *
     * @param string $identifier The managers unique identifier
     */
    public function __construct($identifier = MockManager::IDENTIFIER)
    {
        $this->identifier = $identifier;
        $this->initialized = false;
    }

    /**
     * The managers unique identifier.
     *
     * @return string The unique identifier
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function initialize(ApplicationInterface $application)
    {
        $this->initialized = true;
    }

    /**
     * Factory method that adds a initialized manager instance to the passed application.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ApplicationInterface               $application          The application instance
     * @param \AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface|null $managerConfiguration The manager configuration
     *
     * @return \AppserverIo\Appserver\Application\Interfaces\ManagerInterface The configured manager instance
     */
    public static function visit(ApplicationInterface $application, ManagerConfigurationInterface $managerConfiguration = null)
    {
        return new MockManager();
    }

    /**
     * Returns TRUE if the managers initialize() method has been called.
     *
     * @return boolean TRUE if the manager has been initialized
     */
    public function isInitialized()
    {
        return $this->initialized;
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
}
