<?php

/**
 * AppserverIo\Appserver\ServletEngine\Http\Session
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

namespace AppserverIo\Appserver\ServletEngine\Http;

use AppserverIo\Storage\StackableStorage;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Servlet\ServletSession;
use AppserverIo\Psr\Servlet\Http\HttpSession;
use AppserverIo\Psr\Servlet\Http\HttpServletResponse;

/**
 * A modular session implementation based on the caching framework.
 *
 * You may access the currently active session in userland code. In order to do this,
 * inject TYPO3\Flow\Session\SessionInterface and NOT just TYPO3\Flow\Session\Session.
 * The former will be a unique instance (singleton) representing the current session
 * while the latter would be a completely new session instance!
 *
 * You can use the Session Manager for accessing sessions which are not currently
 * active.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class Session extends GenericStackable implements ServletSession
{

    /**
     * Constructor to initialize a newly created session.
     *
     * @param mixed            $id         The session ID
     * @param string           $name       The session name
     * @param integer|DateTime $lifetime   Date and time after the session expires
     * @param integer|null     $maximumAge Number of seconds until the session expires
     * @param string|null      $domain     The host to which the user agent will send this cookie
     * @param string           $path       The path describing the scope of this cookie
     * @param boolean          $secure     If this cookie should only be sent through a "secure" channel by the user agent
     * @param boolean          $httpOnly   If this cookie should only be used through the HTTP protocol
     */
    public function __construct($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly)
    {

        // set the session status flag
        $this->started = false;

        // initialize the session
        $this->init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly);

        // initialize the storage for the session data
        $this->data = new StackableStorage();
    }

    /**
     * Initializes the session with the passed data.
     *
     * @param mixed            $id         The session ID
     * @param string           $name       The session name
     * @param integer|DateTime $lifetime   Date and time after the session expires
     * @param integer|null     $maximumAge Number of seconds until the session expires
     * @param string|null      $domain     The host to which the user agent will send this cookie
     * @param string           $path       The path describing the scope of this cookie
     * @param boolean          $secure     If this cookie should only be sent through a "secure" channel by the user agent
     * @param boolean          $httpOnly   If this cookie should only be used through the HTTP protocol
     *
     * @return void
     */
    public function init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly)
    {
        // initialize the session
        $this->id = $id;
        $this->name = $name;
        $this->lifetime = $lifetime;
        $this->maximumAge = $maximumAge;
        $this->domain = $domain;
        $this->path = $path;
        $this->secure = $secure;
        $this->httpOnly = $httpOnly;

        // the UNIX timestamp where the last action on this session happens
        $this->lastActivityTimestamp = time();
    }

    /**
     * Starts the session, if it has not been already started
     *
     * @return void
     */
    public function start()
    {
        $this->started = true;
    }

    /**
     * Tells if the session has been started already.
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Returns the unix time stamp marking the last point in time this session has
     * been in use.
     *
     * For the current (local) session, this method will always return the current
     * time. For a remote session, the unix timestamp will be returned.
     *
     * @return integer UNIX timestamp
     */
    public function getLastActivityTimestamp()
    {
        return $this->lastActivityTimestamp;
    }

    /**
     * Returns the current session identifier.
     *
     * @return string The current session identifier
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the current session identifier.
     *
     * @param string $id The current session identifier
     *
     * @return void
     */
    public function setId($id)
    {
         $this->id = $id;
    }

    /**
     * Returns the session name.
     *
     * @return string The session name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the session name.
     *
     * @param string $name The session name
     *
     * @return void
     */
    public function setName($name)
    {
         $this->name = $name;
    }

    /**
     * Returns date and time after the session expires.
     *
     * @return integer|DateTime The date and time after the session expires
     */
    public function getLifetime()
    {
        return $this->lifetime;
    }

    /**
     * Sets date and time after the session expires.
     *
     * @param integer|DateTime $lifetime The date and time after the session expires
     *
     * @return void
     */
    public function setLifetime($lifetime)
    {
        $this->lifetime = $lifetime;
    }

    /**
     * Returns the number of seconds until the session expires.
     *
     * @return integer|null Number of seconds until the session expires
     */
    public function getMaximumAge()
    {
        return $this->maximumAge;
    }

    /**
     * Sets the number of seconds until the session expires.
     *
     * @param integer $maximumAge Number of seconds until the session expires
     *
     * @return void
     */
    public function setMaximumAge($maximumAge)
    {
        $this->maximumAge = $maximumAge;
    }

    /**
     * Returns the host to which the user agent will send this cookie.
     *
     * @return string|null The host to which the user agent will send this cookie
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * Sets the host to which the user agent will send this cookie.
     *
     * @param string $domain The host to which the user agent will send this cookie
     *
     * @return void
     */
    public function setDomain($domain)
    {
         $this->domain = $domain;
    }

    /**
     * Returns the path describing the scope of this cookie.
     *
     * @return string The path describing the scope of this cookie
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Sets the path describing the scope of this cookie.
     *
     * @param string $path The path describing the scope of this cookie
     *
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns if this session should only be sent through a "secure" channel by the user agent.
     *
     * @return boolean TRUE if the session should only be sent through a "secure" channel, else FALSE
     */
    public function isSecure()
    {
        return $this->secure;
    }

    /**
     * Sets the flag that this session should only be sent through a "secure" channel by the user agent.
     *
     * @param boolean $secure TRUE if the session should only be sent through a "secure" channel, else FALSE
     *
     * @return void
     */
    public function setSecure($secure = true)
    {
        $this->secure = $secure;
    }

    /**
     * Returns if this session should only be used through the HTTP protocol.
     *
     * @return boolean TRUE if the session should only be used through the HTTP protocol
     */
    public function isHttpOnly()
    {
        return $this->httpOnly;
    }

    /**
     * Sets the flag that this session should only be used through the HTTP protocol.
     *
     * @param boolean $httpOnly TRUE if the session should only be used through the HTTP protocol
     *
     * @return void
     */
    public function setHttpOnly($httpOnly = true)
    {
        $this->httpOnly = $httpOnly;
    }

    /**
     * Returns the data associated with the given key.
     *
     * @param string $key An identifier for the content stored in the session.
     *
     * @return mixed The contents associated with the given key
     */
    public function getData($key)
    {
        return $this->data->get($key);
    }

    /**
     * Returns TRUE if a session data entry $key is available.
     *
     * @param string $key Entry identifier of the session data
     *
     * @return boolean
     */
    public function hasKey($key)
    {
        return $this->data->has($key);
    }

    /**
     * Stores the given data under the given key in the session
     *
     * @param string $key  The key under which the data should be stored
     * @param mixed  $data The data to be stored
     *
     * @return void
     */
    public function putData($key, $data)
    {
        $this->data->set($key, $data);
    }

    /**
     * Tags this session with the given tag.
     *
     * Note that third-party libraries might also tag your session. Therefore it is
     * recommended to use namespaced tags such as "Acme-Demo-MySpecialTag".
     *
     * @param string $tag The tag – must match be a valid cache frontend tag
     *
     * @return void
     */
    public function addTag($tag)
    {
        throw new \Exception(__METHOD__ . ' not implemented yet');
    }

    /**
     * Removes the specified tag from this session.
     *
     * @param string $tag The tag – must match be a valid cache frontend tag
     *
     * @return void
     */
    public function removeTag($tag)
    {
    }

    /**
     * Returns the tags this session has been tagged with.
     *
     * @return array The tags or an empty array if there aren't any
     */
    public function getTags()
    {
        throw new \Exception(__METHOD__ . ' not implemented yet');
    }

    /**
     * Returns the checksum for this session instance.
     *
     * @return string The checksum
     */
    public function checksum()
    {

        // create an array with the data we want to add to a checksum
        $checksumData = array($this->started, $this->id, $this->name, $this->data);

        // create the checksum and return it
        return md5(json_encode($checksumData));
    }

    /**
     * Returns TRUE if there is a session that can be resumed.
     *
     * If a to-be-resumed session was inactive for too long, this function will
     * trigger the expiration of that session. An expired session cannot be resumed.
     *
     * NOTE that this method does a bit more than the name implies: Because the
     * session info data needs to be loaded, this method stores this data already
     * so it doesn't have to be loaded again once the session is being used.
     *
     * @return boolean TRUE if the session can be resumed, else FALSE
     */
    public function canBeResumed()
    {
        return !$this->autoExpire();
    }

    /**
     * Resumes an existing session, if any.
     *
     * @return integer If a session was resumed, the inactivity of since the last request is returned
     */
    public function resume()
    {
        if ($this->canBeResumed()) {
            $lastActivitySecondsAgo = (time() - $this->getLastActivityTimestamp());
            $this->lastActivityTimestamp = time();
            return $lastActivitySecondsAgo;
        }
    }

    /**
     * Creates a new and empty session instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletSession The empty, but initialized session instance
     */
    public static function emptyInstance()
    {

        // extract the values
        $id = null;
        $name = 'empty';
        $lifetime = -1;
        $maximumAge = -1;
        $domain = '';
        $path = '';
        $secure = false;
        $httpOnly = false;

        // initialize and return the empty instance
        return new Session($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly);
    }

    /**
     * Explicitly destroys all session data.
     *
     * @param string $reason The reason why the session has been destroyed
     *
     * @return void
     */
    public function destroy($reason)
    {
        $this->id = null;
        $this->lifetime = 0;
        $this->lastActivityTimestamp = 0;
        $this->maximumAge = -1;
    }

    /**
     * Automatically expires the session if the user has been inactive for too long.
     *
     * @return boolean TRUE if the session expired, FALSE if not
     */
    protected function autoExpire()
    {
        $lastActivitySecondsAgo = time() - $this->getLastActivityTimestamp();
        $expired = false;
        if ($this->getMaximumAge() !== 0 && $lastActivitySecondsAgo > $this->getMaximumAge()) {
            $this->destroy(sprintf('Session %s was inactive for %s seconds, more than the configured timeout of %s seconds.', $this->getId(), $lastActivitySecondsAgo, $this->getMaximumAge()));
            $expired = true;
        }
        return $expired;
    }
}
