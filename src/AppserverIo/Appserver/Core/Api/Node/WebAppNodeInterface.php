<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\WebAppNodeInterface
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

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * The interface for a web application DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface WebAppNodeInterface extends NodeInterface
{

    /**
     * Return's the description of the web application.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description
     */
    public function getDescription();

    /**
     * Return's the display name of the web application.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The display name
     */
    public function getDisplayName();

    /**
     * Return's the session configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The session configuration
     */
    public function getSessionConfig();

    /**
     * Return's the servlet informations.
     *
     * @return array The servlet informations
     */
    public function getServlets();

    /**
     * Return's the login configuration information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\LoginConfigNode The login configuration information
     */
    public function getLoginConfig();

    /**
     * Return's the security informations.
     *
     * @return array The security informations
     * @deprecated Since 1.2.0
     */
    public function getSecurities();

    /**
     * Return's the security constraint informations.
     *
     * @return array The security constraint informations
     */
    public function getSecurityConstraints();

    /**
     * Return's the security role informations.
     *
     * @return array The security role informations
     */
    public function getSecurityRoles();

    /**
     * Return's the web application's context parameters.
     *
     * @return array The context parameters
     */
    public function getContextParams();

    /**
     * Return's the error page informations.
     *
     * @return array The error page informations
     */
    public function getErrorPages();
}
