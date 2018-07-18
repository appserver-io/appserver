<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ProvisionersNodeTrait
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

use AppserverIo\Description\Annotations as DI;

/**
 *
 * Abstract node that a context's provisioner nodes.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait ProvisionersNodeTrait
{

    /**
     * The context's provisioner configuration.
     *
     * @var array
     * @DI\Mapping(nodeName="provisioners/provisioner", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ProvisionerNode")
     */
    protected $provisioners = array();

    /**
     * Sets the context's provisioner configuration.
     *
     * @param array $provisioners The context's provisioner configuration
     *
     * @return void
     */
    public function setProvisioners($provisioners)
    {
        $this->provisioners = $provisioners;
    }

    /**
     * Returns the context's provisioner configuration.
     *
     * @return array The context's provisioner configuration
     */
    public function getProvisioners()
    {
        return $this->provisioners;
    }
}
