<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ModulesNodeTrait
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;

/**
 * Trait which allows for the management of module nodes within another node.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ModulesNodeTrait
{

    /**
     * The modules.
     *
     * @var array
     * @DI\Mapping(nodeName="modules/module", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ModuleNode")
     */
    protected $modules = array();

    /**
     * Returns the module nodes.
     *
     * @return array
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * Returns the modules as an associative array
     *
     * @return array The array with the sorted modules
     */
    public function getModulesAsArray()
    {

        // initialize the array for the modules
        $modules = array();

        // iterate over the module nodes and sort them into an array
        /** @var \AppserverIo\Appserver\Core\Api\Node\ModuleNode $module */
        foreach ($this->getModules() as $module) {
            $modules[$module->getUuid()] = $module->getType();
        }

        // return the array
        return $modules;
    }
}
