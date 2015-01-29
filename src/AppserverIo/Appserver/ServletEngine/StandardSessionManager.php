<?php

/**
 * AppserverIo\Appserver\ServletEngine\StandardSessionManager
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
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * A standard session manager implementation that provides session
 * persistence while server has not been restarted.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardSessionManager extends GenericStackable implements SessionManagerInterface
{

    /**
     * Injects the sessions.
     *
     * @param \AppserverIo\Storage\StorageInterface $sessions The sessions
     *
     * @return void
     */
    public function injectSessions($sessions)
    {
        $this->sessions = $sessions;
    }

    /**
     * Injects the session factory.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionFactory $sessionFactory The session factory
     *
     * @return void
     */
    public function injectSessionFactory($sessionFactory)
    {
        $this->sessionFactory = $sessionFactory;
    }

    /**
     * Injects the session settings.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface $sessionSettings Settings for the session handling
     *
     * @return void
     */
    public function injectSessionSettings($sessionSettings)
    {
        $this->sessionSettings = $sessionSettings;
    }

    /**
     * Injects the persistence manager.
     *
     * @param \AppserverIo\Appserver\ServletEngine\PersistenceManagerInterface $persistenceManager The persistence manager
     *
     * @return void
     */
    public function injectPersistenceManager(PersistenceManagerInterface $persistenceManager)
    {
        $this->persistenceManager = $persistenceManager;
    }

    /**
     * Injects the garbage collector.
     *
     * @param \AppserverIo\Appserver\ServletEngine\GarbageCollectorInterface $garbageCollector The garbage collector
     *
     * @return void
     */
    public function injectGarbageCollector(GarbageCollectorInterface $garbageCollector)
    {
        $this->garbageCollector = $garbageCollector;
    }

    /**
     * Initializes the session manager.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {

        // load the servlet manager with the session settings configured in web.xml
        $servletManager = $application->search('ServletContextInterface');

        // load the settings, set the default session save path
        $sessionSettings = $this->getSessionSettings();
        $sessionSettings->setSessionSavePath($application->getSessionDir());

        // if we've session parameters defined in our servlet context
        if ($servletManager->hasSessionParameters()) {
            // we want to merge the session settings from the servlet context
            $sessionSettings->mergeServletContext($servletManager);
        }

        // initialize the garbage collector and the persistence manager
        $this->getGarbageCollector()->initialize();
        $this->getPersistenceManager()->initialize();
    }

    /**
     * Returns all sessions actually attached to the session manager.
     *
     * @return \AppserverIo\Storage\StorageInterface The container with sessions
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessionSettings()
    {
        return $this->sessionSettings;
    }

    /**
     * Returns the session factory.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionFactory The session factory instance
     */
    public function getSessionFactory()
    {
        return $this->sessionFactory;
    }

    /**
     * Returns the session pool instance.
     *
     * @return \AppserverIo\Storage\StorageInterface The session pool
     */
    public function getSessionPool()
    {
        // do nothing here
    }

    /**
     * Returns the persistence manager instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\FilesystemPersistenceManager The persistence manager instance
     */
    public function getPersistenceManager()
    {
        return $this->persistenceManager;
    }

    /**
     * Returns the garbage collector instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\GarbageCollectorInterface The garbage collector instance
     */
    public function getGarbageCollector()
    {
        return $this->garbageCollector;
    }

    /**
     * Returns the servlet manager instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContextInterface The servlet manager instance
     */
    public function getServletManager()
    {
        return $this->servletManager;
    }

    /**
     * Creates a new session with the passed session ID and session name if given.
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
    public function create($id, $name, $lifetime = null, $maximumAge = null, $domain = null, $path = null, $secure = null, $httpOnly = null)
    {

        // copy the default session configuration for lifetime from the settings
        if ($lifetime == null) {
            // create a the actual date and add the cookie lifetime
            $dateTime = new \DateTime();
            $dateTime->modify("+{$this->getSessionSettings()->getSessionCookieLifetime()} second");

            // set the cookie lifetime as UNIX timestamp
            $lifetime = $dateTime->getTimestamp();
        }

        // copy the default session configuration for maximum from the settings
        if ($maximumAge == null) {
            $maximumAge = $this->getSessionSettings()->getSessionMaximumAge();
        }

        // copy the default session configuration for cookie domain from the settings
        if ($domain == null) {
            $domain = $this->getSessionSettings()->getSessionCookieDomain();
        }

        // copy the default session configuration for the cookie path from the settings
        if ($path == null) {
            $path = $this->getSessionSettings()->getSessionCookiePath();
        }

        // copy the default session configuration for the secure flag from the settings
        if ($secure == null) {
            $secure = $this->getSessionSettings()->getSessionCookieSecure();
        }

        // copy the default session configuration for the http only flag from the settings
        if ($httpOnly == null) {
            $httpOnly = $this->getSessionSettings()->getSessionCookieHttpOnly();
        }

        // initialize and return the session instance
        $session = $this->getSessionFactory()->nextFromPool();
        $session->init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly);

        // attach the session with a random
        $this->attach($session);

        // return the session
        return $session;
    }

    /**
     * Attaches the passed session to the manager and returns the instance.
     * If a session
     * with the session identifier already exists, it will be overwritten.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $session The session to attach
     *
     * @return void
     */
    public function attach(ServletSessionInterface $session)
    {

        // load session ID
        $id = $session->getId();

        // register checksum + session
        $this->getSessions()->set($id, $session);
    }

    /**
     * Tries to find a session for the given request.
     * The session id will be
     * searched in the cookie header of the request, and in the request query
     * string. If both values are present, the value in the query string takes
     * precedence. If no session id is found, a new one is created and assigned
     * to the request.
     *
     * @param string $id The unique session ID to that has to be returned
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpSessionInterface The requested session
     */
    public function find($id)
    {

        // check if the session has already been loaded, if not try to un-persist it
        $this->getPersistenceManager()->unpersist($id);

        // load the session with the passed ID
        if ($session = $this->getSessions()->get($id)) {
            // if we find a session, we've to check if it can be resumed
            if ($session->canBeResumed()) {
                $session->resume();
                return $session;
            }
        }
    }

    /**
     * Initializes the manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return SessionManager::IDENTIFIER;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key)
    {
        throw new \Exception(sprintf('%s is not implemented yes', __METHOD__));
    }
}
