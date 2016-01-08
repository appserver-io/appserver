<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ServletNodeInterface
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
 * The interface for a servlet DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface ServletNodeInterface extends NodeInterface
{

    /**
     * Return's the description of the servlet.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DescriptionNode The description
     */
    public function getDescription();

    /**
     * Return's the display name of the servlet.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The display name
     */
    public function getDisplayName();

    /**
     * Return's the name of the servlet.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The servlet name
     */
    public function getServletName();

    /**
     * Return's the servlet class.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\DisplayNameNode The servlet class
     */
    public function getServletClass();

    /**
     * Return's the servlet's initialization parameters.
     *
     * @return array The initialization parameters
     */
    public function getInitParams();

    /**
     * Return's the enterprise bean reference information.
     *
     * @return array The enterprise bean reference information
     */
    public function getEpbRefs();

    /**
     * Return's the resource reference information.
     *
     * @return array The resource reference information
     */
    public function getResRefs();

    /**
     * Return's the persistence unit reference information.
     *
     * @return array The persistence unit reference information
     */
    public function getPersistenceUnitRefs();
}
