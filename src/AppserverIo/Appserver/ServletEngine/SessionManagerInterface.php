<?php

/**
 * \AppserverIo\Appserver\ServletEngine\SessionManagerInterface
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

namespace AppserverIo\Appserver\ServletEngine;

use AppserverIo\Psr\Servlet\ServletSessionInterface;
use AppserverIo\Psr\Application\ManagerInterface;

/**
 * Interface for the session managers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionManagerInterface extends ManagerInterface
{

    /**
     * The unique identifier to be registered in the application context.
     *
     * @var string
     */
    const IDENTIFIER = 'SessionManagerInterface';

    /**
     * Creates a new session with the passed session ID and session name if give.
     *
     * @param mixed             $id         The session ID
     * @param string            $name       The session name
     * @param integer|\DateTime $lifetime   Date and time after the session expires
     * @param integer|null      $maximumAge Number of seconds until the session expires
     * @param string|null       $domain     The host to which the user agent will send this cookie
     * @param string            $path       The path describing the scope of this cookie
     * @param boolean           $secure     If this cookie should only be sent through a "secure" channel by the user agent
     * @param boolean           $httpOnly   If this cookie should only be used through the HTTP protocol
     *
     * @return \AppserverIo\Psr\Servlet\ServletSessionInterface The requested session
     */
    public function create($id, $name, $lifetime = null, $maximumAge = null, $domain = null, $path = null, $secure = null, $httpOnly = null);

    /**
     * Attachs the passed session to the manager and returns the instance. If a session
     * with the session identifier already exists, it will be overwritten.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $session The session to attach
     *
     * @return void
     */
    public function attach(ServletSessionInterface $session);

    /**
     * Tries to find a session for the given request. The session id will be
     * searched in the cookie header of the request, and in the request query
     * string. If both values are present, the value in the query string takes
     * precedence. If no session id is found, a new one is created and assigned
     * to the request.
     *
     * @param string $id The unique session ID to that has to be returned
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpSessionInterface The requested session
     */
    public function find($id);

    /**
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessionSettings();

    /**
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessions();

    /**
     * Returns the session factory.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Session\SessionHandlerFactory The session factory instance
     */
    public function getSessionFactory();

    /**
     * Returns the session pool instance.
     *
     * @return \AppserverIo\Storage\StorageInterface The session pool
     */
    public function getSessionPool();

    /**
     * Returns the persistence manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\Session\SessionHandlerInterface The persistence manager instance
     */
    public function getPersistenceManager();

    /**
     * Returns the garbage collector instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\GarbageCollectorInterface The garbage collector instance
     */
    public function getGarbageCollector();
}
