<?php

/**
 * AppserverIo\Appserver\ServletEngine\DefaultSessionSettings
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Http\HttpCookie;
use AppserverIo\Psr\Servlet\ServletSession;
use AppserverIo\Psr\Servlet\ServletContext;
use TechDivision\Storage\GenericStackable;

/**
 * Interface for all session storage implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 * @link      http://php.net/session
 * @link      http://php.net/setcookie
 */
class DefaultSessionSettings extends GenericStackable implements SessionSettings
{

    /**
     * The default servlet session name.
     *
     * @var string
     */
    const DEFAULT_SESSION_NAME = 'SESSID';

    /**
     * The default session prefix.
     *
     * @var string
     */
    const DEFAULT_SESSION_FILE_PREFIX = 'sess_';

    /**
     * The default session cookie path.
     *
     * @var string
     */
    const DEFAULT_SESSION_COOKIE_PATH = '/';

    /**
     * The default inactivity timeout.
     *
     * @var string
     */
    const DEFAULT_INACTIVITY_TIMEOUT = 1440;

    /**
     * The default probaility the garbage collection will be invoked.
     *
     * @var string
     */
    const DEFAULT_GARBAGE_COLLECTION_PROBABILITY = 0.1;

    /**
     * Initialize the default session settings.
     */
    public function __construct()
    {
        // initialize the default values
        $this->setSessionCookieLifetime(86400);
        $this->setSessionName(DefaultSessionSettings::DEFAULT_SESSION_NAME);
        $this->setSessionFilePrefix(DefaultSessionSettings::DEFAULT_SESSION_FILE_PREFIX);
        $this->setSessionMaximumAge(0);
        $this->setSessionCookieDomain(HttpCookie::LOCALHOST);
        $this->setSessionCookiePath(DefaultSessionSettings::DEFAULT_SESSION_COOKIE_PATH);
        $this->setSessionCookieSecure(false);
        $this->setSessionCookieHttpOnly(false);
        $this->setGarbageCollectionProbability(DefaultSessionSettings::DEFAULT_GARBAGE_COLLECTION_PROBABILITY);
        $this->setInactivityTimeout(DefaultSessionSettings::DEFAULT_INACTIVITY_TIMEOUT);
    }

    /**
     * Set the session name
     *
     * @param string $sessionName The session name
     *
     * @return void
     */
    public function setSessionName($sessionName)
    {
        $this->sessionName = $sessionName;
    }

    /**
     * Returns the session name to use.
     *
     * @return string The session name
     */
    public function getSessionName()
    {
        return $this->sessionName;
    }

    /**
     * Set the session file prefix we use.
     *
     * @param string $sessionFilePrefix The session file prefix
     *
     * @return void
     */
    public function setSessionFilePrefix($sessionFilePrefix)
    {
        $this->sessionFilePrefix = $sessionFilePrefix;
    }

    /**
     * Returns the session file prefix to use.
     *
     * @return string The session file prefix
     */
    public function getSessionFilePrefix()
    {
        return $this->sessionFilePrefix;
    }

    /**
     * Set the default path to persist sessions.
     *
     * @param string $sessionSavePath The default path to persist sessions
     *
     * @return void
     */
    public function setSessionSavePath($sessionSavePath)
    {
        $this->sessionSavePath = $sessionSavePath;
    }

    /**
     * Returns the default path to persist sessions.
     *
     * @return string The default path to persist session
     */
    public function getSessionSavePath()
    {
        return $this->sessionSavePath;
    }

    /**
     * Sets the session cookie lifetime.
     *
     * @param integer $sessionCookieLifetime The session cookie lifetime
     *
     * @return void
     */
    public function setSessionCookieLifetime($sessionCookieLifetime)
    {
        $this->sessionCookieLifetime = $sessionCookieLifetime;
    }

    /**
     * Returns the session cookie lifetime.
     *
     * @return integer The session cookie lifetime
     */
    public function getSessionCookieLifetime()
    {
        return $this->sessionCookieLifetime;
    }

    /**
     * Sets the number of seconds until the session expires, if defined.
     *
     * @param integer $sessionMaximumAge The maximum age in seconds, or NULL if none has been defined.
     *
     * @return void
     */
    public function setSessionMaximumAge($sessionMaximumAge)
    {
        $this->sessionMaximumAge = $sessionMaximumAge;
    }

    /**
     * Returns the number of seconds until the session expires, if defined.
     *
     * @return integer The maximum age in seconds, or NULL if none has been defined.
     */
    public function getSessionMaximumAge()
    {
        return $this->sessionMaximumAge;
    }

    /**
     * Sets the cookie domain set for the session.
     *
     * @param string $sessionCookieDomain The cookie domain set for the session
     *
     * @return void
     */
    public function setSessionCookieDomain($sessionCookieDomain)
    {
        $this->sessionCookieDomain = $sessionCookieDomain;
    }

    /**
     * Returns the cookie domain set for the session.
     *
     * @return string The cookie domain set for the session
     */
    public function getSessionCookieDomain()
    {
        return $this->sessionCookieDomain;
    }

    /**
     * Sets the cookie path set for the session.
     *
     * @param string $sessionCookiePath The cookie path set for the session
     *
     * @return void
     */
    public function setSessionCookiePath($sessionCookiePath)
    {
        $this->sessionCookiePath = $sessionCookiePath;
    }

    /**
     * Returns the cookie path set for the session.
     *
     * @return string The cookie path set for the session
     */
    public function getSessionCookiePath()
    {
        return $this->sessionCookiePath;
    }

    /**
     * Sets the flag that the session cookie should only be set in a secure connection.
     *
     * @param boolean $sessionCookieSecure TRUE if a secure cookie should be set, else FALSE
     *
     * @return void
     */
    public function setSessionCookieSecure($sessionCookieSecure)
    {
        $this->sessionCookieSecure = $sessionCookieSecure;
    }

    /**
     * Returns the flag that the session cookie should only be set in a secure connection.
     *
     * @return boolean TRUE if a secure cookie should be set, else FALSE
     */
    public function getSessionCookieSecure()
    {
        return $this->sessionCookieSecure;
    }

    /**
     * Sets the flag if the session should set a Http only cookie.
     *
     * @param boolean $sessionCookieHttpOnly TRUE if a Http only cookie should be used
     *
     * @return void
     */
    public function setSessionCookieHttpOnly($sessionCookieHttpOnly)
    {
        $this->sessionCookieHttpOnly = $sessionCookieHttpOnly;
    }

    /**
     * Returns the flag if the session should set a Http only cookie.
     *
     * @return boolean TRUE if a Http only cookie should be used
     */
    public function getSessionCookieHttpOnly()
    {
        return $this->sessionCookieHttpOnly;
    }

    /**
     * Sets the probability the garbage collector will be invoked on the session.
     *
     * @param float $garbageCollectionProbability The garbage collector probability
     *
     * @return void
     */
    public function setGarbageCollectionProbability($garbageCollectionProbability)
    {
        $this->garbageCollectionProbability = $garbageCollectionProbability;
    }

    /**
     * Returns the probability the garbage collector will be invoked on the session.
     *
     * @return float The garbage collector probability
     */
    public function getGarbageCollectionProbability()
    {
        return $this->garbageCollectionProbability;
    }

    /**
     * Sets the inactivity timeout until the session will be invalidated.
     *
     * @param integer $inactivityTimeout The inactivity timeout in seconds
     *
     * @return void
     */
    public function setInactivityTimeout($inactivityTimeout)
    {
        $this->inactivityTimeout = $inactivityTimeout;
    }

    /**
     * Returns the inactivity timeout until the session will be invalidated.
     *
     * @return integer The inactivity timeout in seconds
     */
    public function getInactivityTimeout()
    {
        return $this->inactivityTimeout;
    }

    /**
     * Merges the values of the passed settings into this instance and overwrites the one of this instance.
     *
     * @param \AppserverIo\Psr\ServletContext\ServletContext $context The context we want to merge the session settings from
     *
     * @return void
     */
    public function mergeServletContext(ServletContext $context)
    {

        // check if the context has his own session parameters
        if ($context->hasSessionParameters() === true) {

            if (($garbageCollectionProbability = $context->getSessionParameter(ServletSession::GARBAGE_COLLECTION_PROBABILITY)) != null) {
                $this->setGarbageCollectionProbability((float) $garbageCollectionProbability);
            }

            if (($sessionName = $context->getSessionParameter(ServletSession::SESSION_NAME)) != null) {
                $this->setSessionName($sessionName);
            }

            if (($sessionFilePrefix = $context->getSessionParameter(ServletSession::SESSION_FILE_PREFIX)) != null) {
                $this->setSessionFilePrefix($sessionFilePrefix);
            }

            if (($sessionSavePath = $context->getSessionParameter(ServletSession::SESSION_SAVE_PATH)) != null) {
                $this->setSessionSavePath($sessionSavePath);
            }

            if (($sessionMaximumAge = $context->getSessionParameter(ServletSession::SESSION_MAXIMUM_AGE)) != null) {
                $this->setSessionMaximumAge((integer) $sessionMaximumAge);
            }

            if (($sessionInactivityTimeout = $context->getSessionParameter(ServletSession::SESSION_INACTIVITY_TIMEOUT)) != null) {
                $this->setInactivityTimeout((integer) $sessionInactivityTimeout);
            }

            if (($sessionCookieLifetime = $context->getSessionParameter(ServletSession::SESSION_COOKIE_LIFETIME)) != null) {
                $this->setSessionCookieLifetime((integer) $sessionCookieLifetime);
            }

            if (($sessionCookieDomain = $context->getSessionParameter(ServletSession::SESSION_COOKIE_DOMAIN)) != null) {
                $this->setSessionCookieDomain($sessionCookieDomain);
            }

            if (($sessionCookiePath = $context->getSessionParameter(ServletSession::SESSION_COOKIE_PATH)) != null) {
                $this->setSessionCookiePath($sessionCookiePath);
            }

            if (($sessionCookieSecure = $context->getSessionParameter(ServletSession::SESSION_COOKIE_SECURE)) != null) {
                $this->setSessionCookieSecure((boolean) $sessionCookieSecure);
            }

            if (($sessionCookieHttpOnly = $context->getSessionParameter(ServletSession::SESSION_COOKIE_HTTP_ONLY)) != null) {
                $this->setSessionCookieHttpOnly((boolean) $sessionCookieHttpOnly);
            }
        }
    }
}
