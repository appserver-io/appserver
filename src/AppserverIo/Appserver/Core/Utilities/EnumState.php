<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\EnumState
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

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Enum class that contains the state keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EnumState
{

    /**
     * We have to stop immediately.
     *
     * @var integer
     */
    const HALT = 0;

    /**
     * We're running succesfully.
     *
     * @var integer
     */
    const RUNNING = 1;

    /**
     * Shutdown has been successful.
     *
     * @var integer
     */
    const SHUTDOWN = 5;

    /**
     * The actual state.
     *
     * @var integer
     */
    private $state;

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     *
     * @param integer $state The state to initialize the instance with
     */
    private function __construct($state)
    {
        $this->state = $state;
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
     * Returns the state representation as integer.
     *
     * @return integer The integer representation of the state
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Returns the container state representation as string.
     *
     * @return string The string representation of the container state
     * @see \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys::getContainerState()
     */
    public function __toString()
    {
        return sprintf('%d', $this->getState());
    }

    /**
     * Returns the available states.
     *
     * @return array The available states
     */
    public static function getStates()
    {
        return array(
            EnumState::HALT,
            EnumState::RUNNING,
            EnumState::SHUTDOWN
        );
    }

    /**
     * Returns TRUE if the state is greater than the passed one, else FALSE.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\EnumState $state The state to be greater than
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function greaterOrEqualThan(EnumState $state)
    {
        return $this->state >= $state->getState();
    }

    /**
     * Returns TRUE if the passed state NOT equals the actual one, else FALSE.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\EnumState $state The state to check NOT to be equal
     *
     * @return boolean TRUE if NOT equal, else FALSE
     */
    public function notEquals(EnumState $state)
    {
        return $this->state !== $state->getState();
    }

    /**
     * Returns TRUE if the passed state equals the actual one, else FALSE.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\EnumState $state The state to check
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function equals(EnumState $state)
    {
        return $this->state === $state->getState();
    }

    /**
     * Factory method to create a new state instance.
     *
     * @param integer $state The state to create an instance for
     *
     * @return \AppserverIo\Appserver\Core\Utilities\EnumState The state key instance
     * @throws \AppserverIo\Appserver\Core\Utilities\InvalidStateException
     *      Is thrown if the state is not available
     */
    public static function get($state)
    {

        // check if the requested container state is available and create a new instance
        if (in_array($state, EnumState::getStates())) {
            return new EnumState($state);
        }

        // throw a exception if the requested runlevel is not available
        throw new InvalidStateException(
            sprintf(
                'Requested state %s is not available (choose on of: %s)',
                $state,
                implode(',', EnumState::getStates())
            )
        );
    }
}
