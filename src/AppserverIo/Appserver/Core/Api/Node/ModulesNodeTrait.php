<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ModulesNodeTrait
 *
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
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Johann Zelger <jz@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Trait which allows for the management of module nodes within another node
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */
trait ModulesNodeTrait
{

    /**
     * The modules.
     *
     * @var array
     * @AS\Mapping(nodeName="modules/module", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ModuleNode")
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
        foreach ($this->getModules() as $module) {
            $modules[$module->getUuid()] = $module->getType();
        }

        // return the array
        return $modules;
    }
}
