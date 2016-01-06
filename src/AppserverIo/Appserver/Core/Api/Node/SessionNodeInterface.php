<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SessionNodeInterface
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
 * Interface for the session bean node information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionNodeInterface extends NodeInterface
{

    /**
     * Return's the session bean type information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionTypeNode The session bean type information
     */
    public function getSessionType();

    /**
     * Return's the enterprise bean name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbNameNode The enterprise bean name information
     */
    public function getEpbName();

    /**
     * Return's the enterprise bean class information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\EpbClassNode The enterprise bean class information
     */
    public function getEpbClass();

    /**
     * Return's the init on startup information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\InitOnStartupNode The init on startup information
     */
    public function getInitOnStartup();

    /**
     * Return's the post construct information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PostConstructNode The post construct information
     */
    public function getPostConstruct();

    /**
     * Return's the pre destroy information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PreDestroyNode The pre destroy information
     */
    public function getPreDestroy();

    /**
     * Return's the post detach information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PostDetachNode The post detach information
     */
    public function getPostDetach();

    /**
     * Return's the pre attach information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\PreAttachNode The pre attach information
     */
    public function getPreAttach();

    /**
     * Return's the enterprise bean reference information.
     *
     * @return array The enterprise bean reference information
     */
    public function getEpbRef();

    /**
     * Return's the resource reference information.
     *
     * @return array The resource reference information
     */
    public function getResRef();

    /**
     * Return's the persistence unit reference information.
     *
     * @return array The persistence unit reference information
     */
    public function getPersistenceUnitRef();
}
