<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\DirectoriesNodeTrait
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
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Abstract node that serves nodes having a namespaces/namespace child.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait NamespacesNodeTrait
{

    /**
     * The namespaces.
     *
     * @var array
     * @AS\Mapping(nodeName="namespaces/namespace", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\NamespaceNode")
     */
    protected $namespaces = array();

    /**
     * Array with the namespaces.
     *
     * @param array $namespaces The namespaces
     *
     * @return void
     */
    public function setNamespaces(array $namespaces)
    {
        $this->namespaces = $namespaces;
    }

    /**
     * Array with the namespaces.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->namespaces;
    }
}
