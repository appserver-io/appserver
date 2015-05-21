<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PersistenceNode
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

/**
 * DTO to transfer a applications persistence configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PersistenceNode extends AbstractNode
{

    /**
     * The application's entity manager configuration.
     *
     * @var array
     * @AS\Mapping(nodeName="persistenceUnits/persistenceUnit", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\PersistenceUnitNode")
     */
    protected $persistenceUnits = array();

    /**
     * Sets the application's entity manager configuration.
     *
     * @param array $persistenceUnits The application's entity manager configuration
     *
     * @return void
     */
    public function setPersistenceUnits($persistenceUnits)
    {
        $this->persistenceUnits = $persistenceUnits;
    }

    /**
     * Returns the application's entity manager configuration.
     *
     * @return array The application's entity manager configuration
     */
    public function getPersistenceUnits()
    {
        return $this->persistenceUnits;
    }
}
