<?php
/**
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
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core;

use TechDivision\PBC\Config;
use TechDivision\PBC\StructureMap;
use AppserverIo\Storage\GenericStackable;

/**
 * AppserverIo\Appserver\Core\StructureMap
 *
 * This class wraps PBC's StructureMap class to make it possible to have the map stored as a Stackable object.
 * This is needed for memory and time efficient usage of the PBC loader.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class StructureMapWrapper extends StructureMap
{
    /**
     * The unique key to store the class map in the initial context.
     *
     * @var string
     */
    const CLASS_MAP = 'StructureMapWrapper.classMap';

    /**
     * @var \AppserverIo\Appserver\Core\GenericStackable $map The actual container for the map
     */
    protected $stackableMap;

    /**
     * @var \AppserverIo\Appserver\Core\InitialContext $initialContext Our initial context
     */
    protected $initialContext;

    /**
     * Default constructor
     *
     * @param array                                          $autoloaderPaths  Which paths do we include in our map?
     * @param array                                          $enforcementPaths Which paths do we have to enforce
     * @param \TechDivision\PBC\Config                       $config           Configuration
     * @param \AppserverIo\Appserver\Core\InitialContext $initialContext   Our initial context
     */
    public function __construct($autoloaderPaths, $enforcementPaths, Config $config, $initialContext)
    {
        // Save our initial context as we need it for loading
        $this->initialContext = $initialContext;

        // Call the parent constructor as we need the information
        parent::__construct($autoloaderPaths, $enforcementPaths, $config);
    }

    /**
     * Will load a serialized map from the storage file, if it exists
     *
     * @return bool
     */
    protected function load()
    {
        $this->stackableMap = $this->getInitialContext()->getAttribute(self::CLASS_MAP);

        // Can we read the intended path?
        if (!isset($this->stackableMap)) {

            // If the map of our parent is empty we have to start generating it
            if (empty($this->map)) {

                // First of all try to use the parent load function, maybe we got it within a file.
                // If not we have to start generating
                if (!parent::load()) {

                    // Start generation
                    parent::generate();
                }
            }

            // Get the map
            $this->stackableMap = new GenericStackable();

            // Copy the contents from the array into our stackable
            foreach ($this->map as $key => $entry) {

                $this->stackableMap[$key] = $entry;
            }

            // Remove the version entry from the map, we do not need it
            unset($this->stackableMap['version']);

            // Save the generated class into our storage
            $this->getInitialContext()->setAttribute(self::CLASS_MAP, $this->stackableMap);

            return true;

        } else {

            return true;
        }
    }

    /**
     * Simple getter for our initial context
     *
     * @return \AppserverIo\Appserver\Core\InitialContext InitialContext
     */
    public function getInitialContext()
    {
        return $this->initialContext;
    }
}
