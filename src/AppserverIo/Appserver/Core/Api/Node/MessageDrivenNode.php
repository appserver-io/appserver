<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\MessageDrivenNode
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
 * DTO to transfer a message driven bean information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageDrivenNode extends AbstractNode implements MessageDrivenNodeInterface
{

    /**
     * The enterprise bean name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\EpbNameNode
     * @AS\Mapping(nodeName="epb-name", nodeType="AppserverIo\Appserver\Core\Api\Node\EpbNameNode")
     */
    protected $epbName;

    /**
     * The enterprise bean class information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\EpbClassNode
     * @AS\Mapping(nodeName="epb-class", nodeType="AppserverIo\Appserver\Core\Api\Node\EpbClassNode")
     */
    protected $epbClass;

    /**
     * The enterprise bean reference information.
     *
     * @var array
     * @AS\Mapping(nodeName="epb-ref", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\EpbRefNode")
     */
    protected $epbRefs = array();

    /**
     * The resource reference information.
     *
     * @var array
     * @AS\Mapping(nodeName="res-ref", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ResRefNode")
     */
    protected $resRefs = array();

    /**
     * The persistence unit reference information.
     *
     * @var array
     * @AS\Mapping(nodeName="persistence-unit-ref", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNode")
     */
    protected $persistenceUnitRefs = array();

    /**
     * Return's the enterprise bean name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbNameNode The enterprise bean name information
     */
    public function getEpbName()
    {
        return $this->epbName;
    }

    /**
     * Return's the enterprise bean class information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbClassNode The enterprise bean class information
     */
    public function getEpbClass()
    {
        return $this->epbClass;
    }

    /**
     * Return's the enterprise bean reference information.
     *
     * @return array The enterprise bean reference information
     */
    public function getEpbRefs()
    {
        return $this->epbRefs;
    }

    /**
     * Return's the resource reference information.
     *
     * @return array The resource reference information
     */
    public function getResRefs()
    {
        return $this->resRefs;
    }

    /**
     * Return's the persistence unit reference information.
     *
     * @return array The persistence unit reference information
     */
    public function getPersistenceUnitRefs()
    {
        return $this->persistenceUnitRefs;
    }
}
