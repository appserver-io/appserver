<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\WebAppNode
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
 * DTO to transfer a web application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class WebAppNode extends AbstractNode implements WebAppNodeInterface
{

    /**
     * The description of the web application.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptionNode
     * @AS\Mapping(nodeName="description", nodeType="AppserverIo\Appserver\Core\Api\Node\DescriptionNode")
     */
    protected $description;

    /**
     * The display name information of the web application.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode
     * @AS\Mapping(nodeName="display-name", nodeType="AppserverIo\Appserver\Core\Api\Node\DisplayNameNode")
     */
    protected $displayName;

    /**
     * The session configuration information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionConfigNode
     * @AS\Mapping(nodeName="session-config", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionConfigNode")
     */
    protected $sessionConfig;

    /**
     * The servlet informations.
     *
     * @var array
     * @AS\Mapping(nodeName="servlet", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ServletNode")
     */
    protected $servlets;

    /**
     * The servlet mapping informations.
     *
     * @var array
     * @AS\Mapping(nodeName="servlet-mapping", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ServletMappingNode")
     */
    protected $servletMappings;

    /**
     * Return's the description of the web application.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return's the display name of the web application.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The display name
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Return's the session configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The session configuration
     */
    public function geSessionConfig()
    {
        return $this->sessionConfig;
    }

    /**
     * Return's the servlet informations.
     *
     * @return array The servlet informations
     */
    public function getServlets()
    {
        return $this->servlets;
    }

    /**
     * Return's the servlet mapping informations.
     *
     * @return array The servlet mapping informations
     */
    public function getServletMappings()
    {
        return $this->servletMappings;
    }
}
