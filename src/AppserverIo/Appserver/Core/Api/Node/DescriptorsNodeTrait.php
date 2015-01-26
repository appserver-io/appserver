<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\DescriptorsNodeTrait
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
 * Abstract node that serves nodes having a descriptors/descriptor child.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
trait DescriptorsNodeTrait
{

    /**
     * The descriptors.
     *
     * @var array
     * @AS\Mapping(nodeName="descriptors/descriptor", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\DescriptorNode")
     */
    protected $descriptors = array();

    /**
     * Array with the descriptors.
     *
     * @param array $descriptors The descriptors
     *
     * @return void
     */
    public function setDescriptors(array $descriptors)
    {
        $this->descriptors = $descriptors;
    }

    /**
     * Array with the descriptors.
     *
     * @return array
     */
    public function getDescriptors()
    {
        return $this->descriptors;
    }
}
