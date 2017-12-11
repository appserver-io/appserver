<?php

/**
 * \AppserverIo\Appserver\Application\ApplicationStateKeys
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

namespace AppserverIo\Appserver\Application;

/**
 * Utility class that contains the application state keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ApplicationStateKeys
{

    /**
     * Server has to be stopped.
     *
     * @var integer
     */
    const HALT = 0;

    /**
     * Container is waiting for initialization.
     *
     * @var integer
     */
    const WAITING_FOR_INITIALIZATION = 1;

    /**
     * Container has been successfully initialized.
     *
     * @var integer
     */
    const INITIALIZATION_SUCCESSFUL = 2;

    /**
     * Deployment has been successfull.
     *
     * @var integer
     */
    const DEPLOYMENT_SUCCESSFUL = 3;

    /**
     * Servers has been started successful.
     *
     * @var integer
     */
    const SERVERS_STARTED_SUCCESSFUL = 4;

    /**
     * Servers has been shutdown successful.
     *
     * @var integer
     */
    const SHUTDOWN = 5;

    /**
     * The actual application state.
     *
     * @var integer
     */
    private $applicationState;

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     *
     * @param integer $applicationState The application state to initialize the instance with
     */
    private function __construct($applicationState)
    {
        $this->applicationState = $applicationState;
    }

    /**
     * This is a utility class, so protect it against cloning.
     *
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Returns the application state representation as integer.
     *
     * @return integer The integer representation of the application state
     */
    public function getApplicationState()
    {
        return $this->applicationState;
    }

    /**
     * Returns the application state representation as string.
     *
     * @return string The string representation of the application state
     * @see \AppserverIo\Appserver\Application\ApplicationStateKeys::getApplicationState()
     */
    public function __toString()
    {
        return sprintf('%d', $this->getApplicationState());
    }

    /**
     * Returns the container states.
     *
     * @return array The container states
     */
    public static function getApplicationStates()
    {
        return array(
            ApplicationStateKeys::HALT,
            ApplicationStateKeys::WAITING_FOR_INITIALIZATION,
            ApplicationStateKeys::INITIALIZATION_SUCCESSFUL,
            ApplicationStateKeys::DEPLOYMENT_SUCCESSFUL,
            ApplicationStateKeys::SERVERS_STARTED_SUCCESSFUL,
            ApplicationStateKeys::SHUTDOWN
        );
    }

    /**
     * Returns TRUE if the container state is greater than the passed one, else FALSE.
     *
     * @param ApplicationStateKeys $containerState The container state to be greater than
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function greaterOrEqualThan(ApplicationStateKeys $containerState)
    {
        return $this->applicationState >= $containerState->getApplicationState();
    }

    /**
     * Returns TRUE if the passed application state equals the actual one, else FALSE.
     *
     * @param ApplicationStateKeys $applicationState The container state to check
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function equals(ApplicationStateKeys $applicationState)
    {
        return $this->applicationState === $applicationState->getApplicationState();
    }

    /**
     * Returns TRUE if the passed application state NOT equals the actual one, else FALSE.
     *
     * @param \AppserverIo\Appserver\Application\ApplicationStateKeys $applicationState The container state to check NOT to be equal
     *
     * @return boolean TRUE if NOT equal, else FALSE
     */
    public function notEquals(ApplicationStateKeys $applicationState)
    {
        return $this->applicationState !== $applicationState->getApplicationState();
    }

    /**
     * Factory method to create a new application state instance.
     *
     * @param integer $applicationState The application state to create an instance for
     *
     * @return \AppserverIo\Appserver\Application\ApplicationStateKeys The application state key instance
     * @throws \AppserverIo\Appserver\Application\InvalidApplicationStateException
     *      Is thrown if the application state is not available
     */
    public static function get($applicationState)
    {

        // check if the requested container state is available and create a new instance
        if (in_array($applicationState, ApplicationStateKeys::getApplicationStates())) {
            return new ApplicationStateKeys($applicationState);
        }

        // throw a exception if the requested runlevel is not available
        throw new InvalidApplicationStateException(
            sprintf(
                'Requested application state %s is not available (choose on of: %s)',
                $applicationState,
                implode(',', ApplicationStateKeys::getApplicationStates())
            )
        );
    }
}
