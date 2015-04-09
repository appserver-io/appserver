<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys
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
 * Utility class that contains the container state keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ContainerStateKeys
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
     * The actual container state.
     *
     * @var integer
     */
    private $containerState;

    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     *
     * @param integer $containerState The container state to initialize the instance with
     */
    private function __construct($containerState)
    {
        $this->containerState = $containerState;
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
     * Returns the container state representation as integer.
     *
     * @return integer The integer representation of the container state
     */
    public function getContainerState()
    {
        return $this->containerState;
    }

    /**
     * Returns the container state representation as string.
     *
     * @return string The string representation of the container state
     * @see \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys::getContainerState()
     */
    public function __toString()
    {
        return sprintf('%d', $this->getContainerState());
    }

    /**
     * Returns the container states.
     *
     * @return array The container states
     */
    public static function getContainerStates()
    {
        return array(
            ContainerStateKeys::WAITING_FOR_INITIALIZATION,
            ContainerStateKeys::INITIALIZATION_SUCCESSFUL,
            ContainerStateKeys::DEPLOYMENT_SUCCESSFUL,
            ContainerStateKeys::SERVERS_STARTED_SUCCESSFUL
        );
    }

    /**
     * Returns TRUE if the container state is greater than the passed one, else FALSE.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys $containerState The container state to be greater than
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function greaterOrEqualThan(ContainerStateKeys $containerState)
    {
        return $this->containerState >= $containerState->getContainerState();
    }

    /**
     * Returns TRUE if the passed container state equals the actual one, else FALSE.
     *
     * @param \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys $containerState The container state to check
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function equals(ContainerStateKeys $containerState)
    {
        return $this->containerState === $containerState->getContainerState();
    }

    /**
     * Factory method to create a new container state instance.
     *
     * @param integer $containerState The container state to create an instance for
     *
     * @return \AppserverIo\Appserver\Core\Utilities\ContainerStateKeys The container state key instance
     * @throws \AppserverIo\Appserver\Core\Utilities\InvalidContainerStateException
     *      Is thrown if the container state is not available
     */
    public static function get($containerState)
    {

        // check if the requested container state is available and create a new instance
        if (in_array($containerState, ContainerStateKeys::getContainerStates())) {
            return new ContainerStateKeys($containerState);
        }

        // throw a exception if the requested runlevel is not available
        throw new InvalidContainerStateException(
            sprintf(
                'Requested container state %s is not available (choose on of: %s)',
                $containerState,
                implode(',', ContainerStateKeys::getContainerStates())
            )
        );
    }
}
