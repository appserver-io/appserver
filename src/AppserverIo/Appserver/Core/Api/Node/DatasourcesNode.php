<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\DatasourcesNode
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer datasources.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DatasourcesNode extends AbstractNode
{

    /**
     * The datasources.
     *
     * @var array
     * @DI\Mapping(nodeName="datasource", nodeType="array", elementType="AppserverIo\Description\Api\Node\DatasourceNode")
     */
    protected $datasources;

    /**
     * Return's the array with the datasources.
     *
     * @return array The datasources
     */
    public function getDatasources()
    {
        return $this->datasources;
    }

    /**
     * Return's an array with the datasources.
     *
     * @return array The array with datasources
     */
    public function getDatasourcesAsArray()
    {

        // initialize the array
        $datasources = array();

        // prepare the array with the datasources
        foreach ($this->getDatasources() as $datasource) {
            $datasources[$datasource->getPrimaryKey()] = $datasource;
        }

        // return the array with the datasources
        return $datasources;
    }
}
