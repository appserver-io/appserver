<?php

/**
 * \AppserverIo\Appserver\Application\StandardManagerSettings
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

use AppserverIo\Storage\GenericStackable;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface;

/**
 * Utility class that contains the application state keys.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property string $baseDirectory The base directory which may contain additional configuration informations
 */
class StandardManagerSettings extends GenericStackable implements ManagerSettingsInterface
{

    /**
     * The base directory which may contain additional configuration informations.
     *
     * @var string
     */
    protected $baseDirectory;

    /**
     * Set's the base directory which may contain additional configuration informations.
     *
     * @param string $baseDirectory The base directory
     *
     * @return void
     */
    public function setBaseDirectory($baseDirectory)
    {
        $this->baseDirectory = $baseDirectory;
    }

    /**
     * Return's the base directory which may contain additional configuration informations.
     *
     * @return string The base directory
     */
    public function getBaseDirectory()
    {
        return $this->baseDirectory;
    }

    /**
     * Merge the passed params with the default settings.
     *
     * @param array $params The associative array with the params to merge
     *
     * @return void
     */
    public function mergeWithParams(array $params)
    {
        // merge the passed properties with the default settings for the stateful session beans
        foreach (array_keys(get_object_vars($this)) as $propertyName) {
            if (array_key_exists($propertyName, $params)) {
                $this->$propertyName = $params[$propertyName];
            }
        }
    }
}
