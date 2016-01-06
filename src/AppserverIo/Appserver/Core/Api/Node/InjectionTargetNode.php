<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode
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
 * DTO to transfer enterprise bean reference information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class InjectionTargetNode extends AbstractNode implements InjectionTargetNodeInterface
{

    /**
     * The enterprise bean injection target class information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InjectionTargetClassNode
     * @AS\Mapping(nodeName="injection-target-class", nodeType="AppserverIo\Appserver\Core\Api\Node\InjectionTargetClassNode")
     */
    protected $injectionTargetClass;

    /**
     * The enterprise bean injection target method information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InjectionTargetMethodNode
     * @AS\Mapping(nodeName="injection-target-method", nodeType="AppserverIo\Appserver\Core\Api\Node\InjectionTargetMethodNode")
     */
    protected $injectionTargetMethod;

    /**
     * The enterprise bean injection target property information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InjectionTargetPropertyNode
     * @AS\Mapping(nodeName="injection-target-property", nodeType="AppserverIo\Appserver\Core\Api\Node\InjectionTargetPropertyNode")
     */
    protected $injectionTargetProperty;

    /**
     * Return's the enterprise bean injection target class information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetClassNode The enterprise bean injection target class information
     */
    public function getInjectionTargetClass()
    {
        return $this->injectionTargetClass;
    }

    /**
     * Return's the enterprise bean injection target method information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetMethodNode The enterprise bean injection target method information
     */
    public function getInjectionTargetMethod()
    {
        return $this->injectionTargetMethod;
    }

    /**
     * Return's the enterprise bean injection target property information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetPropertyNode The enterprise bean injection target property information
     */
    public function getInjectionTargetProperty()
    {
        return $this->injectionTargetProperty;
    }
}
