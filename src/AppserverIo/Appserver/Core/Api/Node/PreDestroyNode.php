<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\PreDestroyNode
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
 * DTO to transfer pre destroy information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class PreDestroyNode extends AbstractNode implements PreDestroyNodeInterface
{

    /**
     * The lifecycle callback methods information.
     *
     * @var array
     * @AS\Mapping(nodeName="lifecycle-callback-method", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\LifecycleCallbackMethodNode")
     */
    protected $lifecycleCallbackMethods;

    /**
     * Return's the lifecycle callback methods information.
     *
     * @return array The lifecycle callback methods information
     */
    public function getLifecycleCallbackMethods()
    {
        return $this->lifecycleCallbackMethods;
    }
}
