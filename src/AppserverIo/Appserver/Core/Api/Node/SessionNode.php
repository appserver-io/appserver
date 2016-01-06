<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SessionNode
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
 * DTO to transfer a session bean configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class SessionNode extends AbstractNode implements SessionNodeInterface
{

    /**
     * The session bean type information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionTypeNode
     * @AS\Mapping(nodeName="session-type", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionTypeNode")
     */
    protected $sessionType;

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
     * The init on startup information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\InitOnStartupNode
     * @AS\Mapping(nodeName="init-on-startup", nodeType="AppserverIo\Appserver\Core\Api\Node\InitOnStartupNode")
     */
    protected $initOnStartup;

    /**
     * The post construct information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PostConstructNode
     * @AS\Mapping(nodeName="post-construct", nodeType="AppserverIo\Appserver\Core\Api\Node\PostConstructNode")
     */
    protected $postConstruct;

    /**
     * The pre destroy information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PreDestroyNode
     * @AS\Mapping(nodeName="pre-destroy", nodeType="AppserverIo\Appserver\Core\Api\Node\PreDestroyNode")
     */
    protected $preDestroy;

    /**
     * The post detach information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PostDetachNode
     * @AS\Mapping(nodeName="post-detach", nodeType="AppserverIo\Appserver\Core\Api\Node\PostDetachNode")
     */
    protected $postDetach;

    /**
     * The pre destroy information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\PreAttachNode
     * @AS\Mapping(nodeName="pre-attach", nodeType="AppserverIo\Appserver\Core\Api\Node\PreAttachNode")
     */
    protected $preAttach;

    /**
     * The enterprise bean reference information.
     *
     * @var array
     * @AS\Mapping(nodeName="epb-ref", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\EpbRefNode")
     */
    protected $epbRef;

    /**
     * The resource reference information.
     *
     * @var array
     * @AS\Mapping(nodeName="res-ref", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ResRefNode")
     */
    protected $resRef;

    /**
     * The persistence unit reference information.
     *
     * @var array
     * @AS\Mapping(nodeName="persistence-unit-ref", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\PersistenceUnitRefNode")
     */
    protected $persistenceUnitRef;

    /**
     * Return's the session bean type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionTypeNode The session bean type information
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

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
     * Return's the init on startup information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InitOnStartupNode The init on startup information
     */
    public function getInitOnStartup()
    {
        return $this->initOnStartup;
    }

    /**
     * Return's the post construct information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PostConstructNode The post construct information
     */
    public function getPostConstruct()
    {
        return $this->postConstruct;
    }

    /**
     * Return's the pre destroy information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PreDestroyNode The pre destroy information
     */
    public function getPreDestroy()
    {
        return $this->preDestroy;
    }

    /**
     * Return's the post detach information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PostDetachNode The post detach information
     */
    public function getPostDetach()
    {
        return $this->postDetach;
    }

    /**
     * Return's the pre attach information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PreAttachNode The pre attach information
     */
    public function getPreAttach()
    {
        return $this->preAttach;
    }

    /**
     * Return's the enterprise bean reference information.
     *
     * @return array The enterprise bean reference information
     */
    public function getEpbRef()
    {
        return $this->epbRef;
    }

    /**
     * Return's the resource reference information.
     *
     * @return array The resource reference information
     */
    public function getResRef()
    {
        return $this->resRef;
    }

    /**
     * Return's the persistence unit reference information.
     *
     * @return array The persistence unit reference information
     */
    public function getPersistenceUnitRef()
    {
        return $this->persistenceUnitRef;
    }
}
