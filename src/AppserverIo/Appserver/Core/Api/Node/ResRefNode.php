<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ResRefNode
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
 * DTO to transfer resource reference information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ResRefNode extends AbstractNode implements ResRefNodeInterface
{

    /**
     * The resource reference name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ResRefNameNode
     * @AS\Mapping(nodeName="res-ref-name", nodeType="AppserverIo\Appserver\Core\Api\Node\ResRefNameNode")
     */
    protected $resRefName;

    /**
     * The resource reference type information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ResRefTypeNode
     * @AS\Mapping(nodeName="res-ref-type", nodeType="AppserverIo\Appserver\Core\Api\Node\ResRefTypeNode")
     */
    protected $resRefType;

    /**
     * The resource description information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * The resource lookup name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\LookupNameNode
     * @AS\Mapping(nodeName="lookup-name", nodeType="AppserverIo\Appserver\Core\Api\Node\LookupNameNode")
     */
    protected $lookupName;

    /**
     * The resource injection target information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode
     * @AS\Mapping(nodeName="injection-target", nodeType="AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode")
     */
    protected $injectionTarget;

    /**
     * Return's the resource reference name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The resource reference name information
     */
    public function getResRefName()
    {
        return $this->restRefName;
    }

    /**
     * Return's the resource reference type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbRefNameNode The resource reference type information
     */
    public function getResRefType()
    {
        return $this->resRefType;
    }

    /**
     * Return's the resource description information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The resource description information
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return's the resource lookup name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LookupNameNode The resource lookup name information
     */
    public function getLookupName()
    {
        return $this->lookupName;
    }

    /**
     * Return's the resource injection target information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InjectionTargetNode The resource injection target information
     */
    public function getInjectionTarget()
    {
        return $this->injectionTarget;
    }
}
