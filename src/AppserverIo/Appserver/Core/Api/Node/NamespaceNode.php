<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\NamespaceNode
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
 * DTO to transfer the namespace information.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2014 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class NamespaceNode extends AbstractValueNode
{

    /**
     * Used to tell design-by-contract if this namespace should be omitted for autoloading
     *
     * @var boolean
     * @AS\Mapping(nodeType="boolean")
     */
    protected $omitAutoLoading;

    /**
     * Used to tell design-by-contract if this namespace should be omitted for enforcement
     *
     * @var boolean
     * @AS\Mapping(nodeType="boolean")
     */
    protected $omitEnforcement;

    /**
     * Returns the omitAutoLoading flag.
     *
     * @return boolean The omitAutoLoading flag
     */
    public function omitAutoLoading()
    {
        return $this->omitAutoLoading;
    }

    /**
     * Returns the omitEnforcement flag.
     *
     * @return boolean The omitEnforcement flag
     */
    public function omitEnforcement()
    {
        return $this->omitEnforcement;
    }
}
