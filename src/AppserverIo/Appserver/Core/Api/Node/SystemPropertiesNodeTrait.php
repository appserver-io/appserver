<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SystemPropertiesNodeTrait
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Api\Node\NodeValue;

/**
 * Abstract node that serves nodes having a systemProperties/sytemProperty child.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait SystemPropertiesNodeTrait
{

    /**
     * The system properties to use.
     *
     * @var array
     * @AS\Mapping(nodeName="systemProperties/systemProperty", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\SystemPropertyNode")
     */
    protected $systemProperties = array();

    /**
     * Array with the system properties to use.
     *
     * @return array
     */
    public function getSystemProperties()
    {
        return $this->systemProperties;
    }

    /**
     * Array with the system properties to use.
     *
     * @param array $systemProperties The array with the system properties
     *
     * @return void
     */
    public function setSystemProperties(array $systemProperties)
    {
        $this->systemProperties = $systemProperties;
    }

    /**
     * Sets the system property with the passed name, type and value.
     *
     * @param string $name  The system property name
     * @param string $type  The system property type
     * @param mixed  $value The system property value
     *
     * @return void
     */
    public function setSystemProperty($name, $type, $value)
    {

        // initialize the system property to set
        $sytemPropertyToSet = new SystemPropertyNode($name, $type, new NodeValue($value));

        // query whether a system property with this name has already been set
        foreach ($this->systemProperties as $key => $systemProperty) {
            if ($systemProperty->getName() === $systemProperty->getName()) {
                // override the system property
                $this->systemProperties[$key] = $sytemPropertyToSet;
                return;
            }
        }

        // append the system property
        $this->systemProperties[] = $sytemPropertyToSet;
    }

    /**
     * Returns the system property with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the system property to be returned
     *
     * @return mixed The requested system property casted to the specified type
     */
    public function getSystemProperty($name)
    {
        $systemProperties = $this->getSystemPropertiesAsArray();
        if (array_key_exists($name, $systemProperties)) {
            return $systemProperties[$name];
        }
    }

    /**
     * Returns the system properties casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted system properties
     */
    public function getSystemPropertiesAsArray()
    {
        $systemProperties = array();
        if (is_array($this->getSystemProperties())) {
            foreach ($this->getSystemProperties() as $systemProperty) {
                $systemProperties[$systemProperty->getName()] = $systemProperty->castToType();
            }
        }
        return $systemProperties;
    }
}
