<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SessionConfigNodeInterface
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
 * Interface for session configuration DTO implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionConfigNodeInterface extends NodeInterface
{

    /**
     * Return's the session name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionNameNode The session name information
     */
    public function getSessionName();

    /**
     * Return's the session file prefix information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionFilePrefixNode The session file prefix information
     */
    public function getSessionFilePrefix();

    /**
     * Return's the session save path information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionSavePathNode The session save path information
     */
    public function getSessionSavePath();

    /**
     * Return's the garbage collection probability information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\GarbageCollectionProbabilityNode The garbage collection probability information
     */
    public function getGarbageCollectionProbability();

    /**
     * Return's the session maixmum age information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionMaximumAgeNode The session maximum age information
     */
    public function getSessionMaximumAge();

    /**
     * Return's the session inactivity timeout information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionInactivityTimeoutNode The session inativity timeout information
     */
    public function getSessionInactivityTimeout();

    /**
     * Return's the session cookie lifetime information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieLifetimeNode The session cookie lifetime information
     */
    public function getSessionCookieLifetime();

    /**
     * Return's the session cookie domain information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieDomainNode The session cookie domain information
     */
    public function getSessionCookieDomain();

    /**
     * Return's the session cookie path information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookiePathNode The session cookie path information
     */
    public function getSessionCookiePath();

    /**
     * Return's the session cookie secure information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieSecureNode The session cookie secure information
     */
    public function getSessionCookieSecure();

    /**
     * Return's the session cookie HTTP only information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieHttpOnlyNode The session cookie HTTP only information
     */
    public function getSessionCookieHttpOnly();
}
