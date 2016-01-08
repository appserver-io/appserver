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
    protected $servlets = array();

    /**
     * The servlet mapping informations.
     *
     * @var array
     * @AS\Mapping(nodeName="servlet-mapping", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ServletMappingNode")
     */
    protected $servletMappings = array();

    /**
     * The login configuration informations.
     *
     * @var array
     * @AS\Mapping(nodeName="login-config", nodeType="AppserverIo\Appserver\Core\Api\Node\LoginConfigNode")
     */
    protected $loginConfig;

    /**
     * The security informations (old format).
     *
     * @var array
     * @deprecated Since 1.2.0
     * @AS\Mapping(nodeName="security", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\SecurityNode")
     */
    protected $securities = array();

    /**
     * The security constraint informations.
     *
     * @var array
     * @AS\Mapping(nodeName="security-constraint", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\SecurityConstraintNode")
     */
    protected $securityConstraints = array();

    /**
     * The security role informations.
     *
     * @var array
     * @AS\Mapping(nodeName="security-role", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\SecurityRoleNode")
     */
    protected $securityRoles = array();

    /**
     * The initialization parameter of the web application.
     *
     * @var array
     * @AS\Mapping(nodeName="context-param", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ContextParamNode")
     */
    protected $contextParams = array();

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
    public function getSessionConfig()
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

    /**
     * Return's the login configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LoginConfigNode The login configuration information
     */
    public function getLoginConfig()
    {
        return $this->loginConfig;
    }

    /**
     * Return's the security informations.
     *
     * @return array The security informations
     * @deprecated Since 1.2.0
     */
    public function getSecurities()
    {
        return $this->securities;
    }

    /**
     * Return's the security constraint informations.
     *
     * @return array The security constraint informations
     */
    public function getSecurityConstraints()
    {
        return $this->securityConstraints;
    }

    /**
     * Return's the security role informations.
     *
     * @return array The security role informations
     */
    public function getSecurityRoles()
    {
        return $this->securityRoles;
    }

    /**
     * Return's the web application's context parameters.
     *
     * @return array The context parameters
     */
    public function getContextParams()
    {
        return $this->contextParams;
    }
}
