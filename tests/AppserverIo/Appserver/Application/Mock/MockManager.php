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
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Application\Mock;

use AppserverIo\Psr\Application\ManagerInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface;

/**
 * Test implementation for the virtual host.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
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
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function initialize(ApplicationInterface $application)
    {
        $this->initialized = true;
    }

    /**
     * Lifecycle callback that'll be invoked after the application has been started.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::postStartup()
     */
    public function postStartup(ApplicationInterface $application)
    {
    }

    /**
     * Factory method that adds a initialized manager instance to the passed application.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface              $application          The application instance
     * @param \AppserverIo\Appserver\Core\Api\Node\ManagerNodeInterface|null $managerConfiguration The manager configuration
     *
     * @return \AppserverIo\Psr\Application\ManagerInterface The configured manager instance
     */
    public static function visit(ApplicationInterface $application, ManagerNodeInterface $managerConfiguration = null)
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

    /**
     * Stops the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
    }
}
