<?php
/**
 * AppserverIo\Appserver\ServletEngine\SessionSettings
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

use AppserverIo\Psr\Servlet\ServletContext;

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
 * @link       http://php.net/session
 * @link       http://php.net/setcookie
 */
interface SessionSettings
{

    /**
     * Returns the session name.
     *
     * @return string The session name
     */
    public function getSessionName();

    /**
     * Returns the session cookie lifetime.
     *
     * @return integer The session cookie lifetime
     */
    public function getSessionCookieLifetime();

    /**
     * Returns the number of seconds until the session expires, if defined.
     *
     * @return integer The maximum age in seconds, or NULL if none has been defined.
     */
    public function getSessionMaximumAge();

    /**
     * Returns the cookie domain set for the session.
     *
     * @return string The cookie domain set for the session
     */
    public function getSessionCookieDomain();

    /**
     * Returns the cookie path set for the session.
     *
     * @return string The cookie path set for the session
     */
    public function getSessionCookiePath();

    /**
     * Returns the flag that the session cookie should only be set in a secure connection.
     *
     * @return boolean TRUE if a secure cookie should be set, else FALSE
     */
    public function getSessionCookieSecure();

    /**
     * Returns the flag if the session should set a Http only cookie.
     *
     * @return boolean TRUE if a Http only cookie should be used
     */
    public function getSessionCookieHttpOnly();

    /**
     * Returns the probability the garbage collector will be invoked on the session.
     *
     * @return float The garbage collector probability
     */
    public function getGarbageCollectionProbability();

    /**
     * Returns the inactivity timeout until the session will be invalidated.
     *
     * @return integer The inactivity timeout in seconds
     */
    public function getInactivityTimeout();

    /**
     * Merges the values of the passed settings into this instance and overwrites the one of this instance.
     *
     * @param \AppserverIo\Psr\ServletContext\ServletContext $context The context we want to merge the session settings from
     *
     * @return void
     */
    public function mergeServletContext(ServletContext $context);
}
