<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\SessionConfigNode
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
class SessionConfigNode extends AbstractNode implements SessionConfigNodeInterface
{

    /**
     * The session name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionNameNode
     * @AS\Mapping(nodeName="session-name", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionNameNode")
     */
    protected $sessionName;

    /**
     * The session file prefix information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionFilePrefixNode
     * @AS\Mapping(nodeName="session-file-prefix", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionFilePrefixNode")
     */
    protected $sessionFilePrefix;

    /**
     * The session save path information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionSavePathNode
     * @AS\Mapping(nodeName="session-save-path", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionSavePathNode")
     */
    protected $sessionSavePath;

    /**
     * The garbage collection probability information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\GarbageCollectionProbabilityNode
     * @AS\Mapping(nodeName="garbage-collection-probability", nodeType="AppserverIo\Appserver\Core\Api\Node\GarbageCollectionProbabilityNode")
     */
    protected $garbageCollectionProbability;

    /**
     * The session maximum age information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionMaximumAgeNode
     * @AS\Mapping(nodeName="session-maximum-age", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionMaximumAgeNode")
     */
    protected $sessionMaximumAge;

    /**
     * The session inactivity timeout information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionInactivityTimeoutNode
     * @AS\Mapping(nodeName="session-inactivity-timeout", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionInactivityTimeoutNode")
     */
    protected $sessionInactivityTimeout;

    /**
     * The session cookie lifetime information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionCookieLifetimeNode
     * @AS\Mapping(nodeName="session-cookie-lifetime", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionCookieLifetimeNode")
     */
    protected $sessionCookieLifetime;

    /**
     * The session cookie domain information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionCookieDomainNode
     * @AS\Mapping(nodeName="session-cookie-domain", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionCookieDomainNode")
     */
    protected $sessionCookieDomain;

    /**
     * The session cookie path information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionCookiePathNode
     * @AS\Mapping(nodeName="session-cookie-path", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionCookiePathNode")
     */
    protected $sessionCookiePath;

    /**
     * The session cookie secure information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionCookieSecureNode
     * @AS\Mapping(nodeName="session-cookie-secure", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionCookieSecureNode")
     */
    protected $sessionCookieSecure;

    /**
     * The session HTTP only information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\SessionHttpOnlyNode
     * @AS\Mapping(nodeName="session-http-only", nodeType="AppserverIo\Appserver\Core\Api\Node\SessionHttpOnlyNode")
     */
    protected $sessionHttpOnly;

    /**
     * Return's the session name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionNameNode The session name information
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * Return's the session file prefix information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionFilePrefixNode The session file prefix information
     */
    public function getSessionFilePrefix()
    {
        return $this->sessionFilePrefix;
    }

    /**
     * Return's the session save path information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionSavePathNode The session save path information
     */
    public function getSessionSavePath()
    {
        return $this->sessionSavePath;
    }

    /**
     * Return's the garbage collection probability information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\GarbageCollectionProbabilityNode The garbage collection probability information
     */
    public function getGarbageCollectionProbability()
    {
        return $this->garbageCollectionProbability;
    }

    /**
     * Return's the session maixmum age information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionMaximumAgeNode The session maximum age information
     */
    public function getSessionMaximumAge()
    {
        return $this->sessionMaximumAge;
    }

    /**
     * Return's the session inactivity timeout information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionInactivityTimeoutNode The session inativity timeout information
     */
    public function getSessionInactivityTimeout()
    {
        return $this->sessionInactivityTimeout;
    }

    /**
     * Return's the session cookie lifetime information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieLifetimeNode The session cookie lifetime information
     */
    public function getSessionCookieLifetime()
    {
        return $this->sessionCookieLifetime;
    }

    /**
     * Return's the session cookie domain information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieDomainNode The session cookie domain information
     */
    public function getSessionCookieDomain()
    {
        return $this->sessionCookieDomain;
    }

    /**
     * Return's the session cookie path information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookiePathNode The session cookie path information
     */
    public function getSessionCookiePath()
    {
        return $this->sessionCookiePath;
    }

    /**
     * Return's the session cookie secure information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionCookieSecureNode The session cookie secure information
     */
    public function getSessionCookieSecure()
    {
        return $this->sessionCookieSecure;
    }

    /**
     * Return's the session HTTP only information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\SessionHttpOnlyNode The session HTTP only information
     */
    public function getSessionHttpOnly()
    {
        return $this->sessionHttpOnly;
    }
}
