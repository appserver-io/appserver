<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ContainerNode
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
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a container.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ContainerNode extends AbstractNode
{

    /**
     * The container's name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The container's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The thread class name that start's the container.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $threadType;

    /**
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * The receiver used to start the container's socket.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ReceiverNode
     * @AS\Mapping(nodeName="receiver", nodeType="AppserverIo\Appserver\Core\Api\Node\ReceiverNode")
     */
    protected $receiver;

    /**
     * The servers used in container
     *
     * @var array
     * @AS\Mapping(nodeName="servers/server", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ServerNode")
     */
    protected $servers;

    /**
     * The host configuration information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\HostNode
     * @AS\Mapping(nodeName="host", nodeType="AppserverIo\Appserver\Core\Api\Node\HostNode")
     */
    protected $host;

    /**
     * The deployment configuration information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DeploymentNode
     * @AS\Mapping(nodeName="deployment", nodeType="AppserverIo\Appserver\Core\Api\Node\DeploymentNode")
     */
    protected $deployment;

    /**
     * Returns the unique container name.
     *
     * @return string The unique container name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the container's class name.
     *
     * @return string The container's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the thread class name that start's the containere.
     *
     * @return string The thread class name that start's the container
     */
    public function getThreadType()
    {
        return $this->threadType;
    }

    /**
     * Returns the receiver description.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The receiver description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Returns the host configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\HostNode The host configuration information
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Returns the deployment configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DeploymentNode The deployment configuration information
     */
    public function getDeployment()
    {
        return $this->deployment;
    }

    /**
     * Return's all server nodes
     *
     * @return array
     */
    public function getServers()
    {
        return $this->servers;
    }
}
