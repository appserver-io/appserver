<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ServletNode
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
 * DTO to transfer a session configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletNode extends AbstractNode implements ServletNodeInterface
{

    /**
     * The initialization parameter of the servlet.
     *
     * @var array
     * @AS\Mapping(nodeName="init-param", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\InitParamNode")
     */
    protected $initParams = array();

    /**
     * The description of the servlet.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * The display name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode
     * @AS\Mapping(nodeName="display-name", nodeType="AppserverIo\Appserver\Core\Api\Node\DisplayNameNode")
     */
    protected $displayName;

    /**
     * The servlet name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ServletNameNode
     * @AS\Mapping(nodeName="servlet-name", nodeType="AppserverIo\Appserver\Core\Api\Node\ServletNameNode")
     */
    protected $servletName;

    /**
     * The servlet class information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ServletClassNode
     * @AS\Mapping(nodeName="servlet-class", nodeType="AppserverIo\Appserver\Core\Api\Node\ServletClassNode")
     */
    protected $servletClass;

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
     * Return's the description of the servlet.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return's the display name of the servlet.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Return's the name of the servlet.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The servlet name
     */
    public function getServletName()
    {
        return $this->servletName;
    }

    /**
     * Return's the servlet class.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The servlet class
     */
    public function getServletClass()
    {
        return $this->servletClass;
    }

    /**
     * Return's the servlet's initialization parameters.
     *
     * @return array The initialization parameters
     */
    public function getInitParams()
    {
        return $this->initParams;
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
