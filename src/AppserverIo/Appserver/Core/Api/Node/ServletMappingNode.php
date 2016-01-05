<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ServletMappingNode
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
 * DTO to transfer a servlet mapping configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ServletMappingNode extends AbstractNode implements ServletMappingNodeInterface
{

    /**
     * The servlet name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ServletNameNode
     * @AS\Mapping(nodeName="servlet-name", nodeType="AppserverIo\Appserver\Core\Api\Node\ServletNameNode")
     */
    protected $servletName;

    /**
     * The URL pattern information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode
     * @AS\Mapping(nodeName="url-pattern", nodeType="AppserverIo\Appserver\Core\Api\Node\UrlPatternNode")
     */
    protected $urlPattern;

    /**
     * Return's the servlet name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ServletNameNode The servlet name information
     */
    public function getServletName()
    {
        return $this->servletName;
    }

    /**
     * Return's the URL pattern information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\UrlPatternNode The URL pattern information
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }
}
