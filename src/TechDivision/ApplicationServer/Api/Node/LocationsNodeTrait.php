<?php

/**
 * TechDivision\ApplicationServer\Api\Node\LocationsNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer\Api\Node;

/**
 * Abstract node that serves nodes having a locations/location child.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait LocationsNodeTrait
{
    

    /**
     * The locations.
     *
     * @var array
     * @AS\Mapping(nodeName="locations/location", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\LocationNode")
     */
    protected $locations = array();
    
    /**
     * Will return the locations array.
     *
     * @return array
    */
    public function getLocations()
    {
        return $this->locations;
    }
    
    /**
     * Will return the location node with the specified condition and if nothing could be found we will return false.
     *
     * @param string $condition The condition of the location in question
     *
     * @return \TechDivision\ApplicationServer\Api\Node\LocationNode|boolean The requested location node
     */
    public function getLocation($condition)
    {
        // iterate over all locations
        foreach ($this->getLocations() as $location) {
    
            // if we found one with a matching condition we will return it
            if ($location->getCondition() === $condition) {
                return $location;
            }
        }
    
        // Still here? Seems we did not find anything
        return false;
    }
    
    /**
     * Returns the locations as an associative array.
     *
     * @return array The array with the sorted locations
     */
    public function getLocationsAsArray()
    {
        // iterate over the location nodes and sort them into an array
        $locations = array();
        foreach ($this->getLocations() as $locationNode) {
    
            // restructure to an array
            $locations[$locationNode->getCondition()] = array(
                'condition' => $locationNode->getCondition()
            );
        }
    
        // return what we got
        return $locations;
    }
}
