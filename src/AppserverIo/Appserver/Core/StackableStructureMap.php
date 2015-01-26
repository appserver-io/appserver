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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core;

use AppserverIo\Doppelgaenger\Config;
use AppserverIo\Doppelgaenger\StructureMap;

/**
 * AppserverIo\Appserver\Core\StructureMap
 *
 * This class wraps Doppelgaenger's StructureMap class to make it possible to have the map stored
 * as a Stackable object. This is needed for inter-thread sharing of the structure map.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH - <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.techdivision.com/
 */
class StackableStructureMap extends StructureMap
{

    /**
     * Default constructor
     *
     * @param array                             $autoloaderPaths  Which paths do we include in our map?
     * @param array                             $enforcementPaths Which paths do we have to enforce
     * @param \AppserverIo\Doppelgaenger\Config $config           Configuration
     */
    public function __construct($autoloaderPaths, $enforcementPaths, Config $config)
    {
        // call the parent constructor as we need the information
        parent::__construct($autoloaderPaths, $enforcementPaths, $config);

        // now make the map a stackable as we need this in this environment
        $this->map = new \Stackable();
    }
}
