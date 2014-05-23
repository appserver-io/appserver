<?php
/**
 * TechDivision\ApplicationServer\Utilities\StateKeys
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Utilities
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Utilities;

/**
 * Utility class that contains keys the appservers state.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Utilities
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class StateKeys
{

    /**
     * The unique class name.
     *
     * @var string
     */
    const KEY = __CLASS__;
    
    /**
     * Key that flags the server to be started.
     *
     * @var string
     */
    const STARTING = 'tech_division.application_server.state_keys.starting';
    
    /**
     * Key that flags the server to be stopped.
     *
     * @var string
     */
    const STOPPING = 'tech_division.application_server.state_keys.stopping';
    
    /**
     * Key for the the running state.
     *
     * @var string
     */
    const RUNNING = 'tech_division.application_server.state_keys.running';

    /**
     * The actual state.
     *
     * @var string
     */
    private $state;
    
    /**
     * This is a utility class, so protect it against direct
     * instantiation.
     *
     * @param string $state The state to initialize the instance with
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
     * Returns the state representation as string.
     *
     * @return string The string representation of the state
     */
    public function getState()
    {
        return $this->state;
    }
    
    /**
     * Returns the state representation as string.
     *
     * @return string The string representation of the state
     * @see \TechDivision\ApplicationServer\Utilities\StateKeys::getState()
     */
    public function __toString()
    {
        return $this->getState();
    }

    /**
     * Returns the application server's state keys.
     *
     * @return array The state keys
     */
    public static function getStates()
    {
        return array(
            StateKeys::RUNNING,
            StateKeys::STARTING,
            StateKeys::STOPPING
        );
    }
    
    /**
     * Returns TRUE if the passed state key equals
     * the actual one, else FALSE.
     *
     * @param \TechDivision\ApplicationServer\Utilities\StateKeys $stateKey The state key to check
     *
     * @return boolean TRUE if equal, else FALSE
     */
    public function equals(StateKeys $stateKey)
    {
        return $this->state === $stateKey->getState();
    }
    
    /**
     * Factory method to create a new state instance.
     *
     * @param string $state The state to create an instance for
     *
     * @return \TechDivision\ApplicationServer\Utilities\StateKeys The state instance
     * @throws \TechDivision\ApplicationServer\Utilities\InvalidStateException
     *      Is thrown if the state is not available
     */
    public static function get($state)
    {
        
        // check if the requested state is available and create a new instance
        if (in_array($state, StateKeys::getStates())) {
            return new StateKeys($state);
        }
        
        //throw a exception if the requested state is not available
        throw new InvalidStateException(
            sprintf(
                "Requested state %s is not available (choose on of: %s)",
                $state,
                implode(',', StateKeys::getStates())
            )
        );
    }
}
