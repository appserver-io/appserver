<?php

/**
 * \AppserverIo\Appserver\ServletEngine\StandardSessionManager
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

use AppserverIo\Logger\LoggerUtils;
use AppserverIo\Collections\HashMap;
use AppserverIo\Collections\CollectionInterface;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Servlet\ServletContextInterface;
use AppserverIo\Psr\Servlet\ServletSessionInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Psr\Application\ManagerConfigurationInterface;

/**
 * A simple session manager implementation implementation using
 * session handlers to persist the sessions.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class StandardSessionManager implements SessionManagerInterface
{

    /**
     * The HashMap containing the sessions.
     *
     * @var \AppserverIo\Collections\HashMap
     */
    protected $sessions;

    /**
     * The HashMap containing the session handlers.
     *
     * @var \AppserverIo\Collections\HashMap
     */
    protected $sessionHandlers;

    /**
     * The application instance.
     *
     * @var \AppserverIo\Psr\Application\ApplicationInterface
     */
    protected $application;

    /**
     * The settings for the session handling.
     *
     * @var \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface
     */
    protected $sessionSettings;

    /**
     * The session marshaller instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface
     */
    protected $sessionMarshaller;

    /**
     * The manager configuration instance.
     *
     * @var  \AppserverIo\Psr\Application\ManagerConfigurationInterface
     */
    protected $managerConfiguration;

    /**
     * The garbage collector instance.
     *
     * @var \AppserverIo\Appserver\ServletEngine\GarbageCollectorInterface
     */
    protected $garbageCollector;

    /**
     * Initialize the session manager.
     */
    public function __construct()
    {
        $this->sessions = new HashMap();
        $this->sessionHandlers = new HashMap();
    }

    /**
     * Save the sessions back to the persistence layer.
     */
    public function __destruct()
    {
        $this->flush();
    }

    /**
     * Inject the application instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     */
    public function injectApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
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
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessionSettings()
    {
        return $this->sessionSettings;
    }

    /**
     * Injects the session marshaller.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface $sessionMarshaller The session marshaller instance
     *
     * @return void
     */
    public function injectSessionMarshaller($sessionMarshaller)
    {
        $this->sessionMarshaller = $sessionMarshaller;
    }

    /**
     * Returns the session marshaller.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface The session marshaller
     */
    public function getSessionMarshaller()
    {
        return $this->sessionMarshaller;
    }

    /**
     * Inject the configuration for this manager.
     *
     * @param \AppserverIo\Psr\Application\ManagerConfigurationInterface $managerConfiguration The managers configuration
     *
     * @return void
     */
    public function injectManagerConfiguration(ManagerConfigurationInterface $managerConfiguration)
    {
        $this->managerConfiguration = $managerConfiguration;
    }

    /**
     * Return's the manager configuration.
     *
     * @return \AppserverIo\Psr\Application\ManagerConfigurationInterface The manager configuration
     */
    public function getManagerConfiguration()
    {
        return $this->managerConfiguration;
    }

    /**
     * Inject the session handlers.
     *
     * @param \AppserverIo\Collections\CollectionInterface $sessionHandlers The session handlers
     *
     * @return void
     */
    public function injectSessionHandlers(CollectionInterface $sessionHandlers)
    {
        $this->sessionHandlers = $sessionHandlers;
    }

    /**
     * Returns all registered session handlers.
     *
     * @return \AppserverIo\Collections\ArrayList The session handlers
     */
    public function getSessionHandlers()
    {
        return $this->sessionHandlers;
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
     * Returns the garbage collector instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\GarbageCollectorInterface The garbage collector instance
     */
    public function getGarbageCollector()
    {
        return $this->garbageCollector;
    }

    /**
     * Returns all sessions actually attached to the session manager.
     *
     * @return \AppserverIo\Collections\HashMap The container with sessions
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Returns the attribute with the passed key from the container.
     *
     * @param string $key The key the requested value is registered with
     *
     * @return mixed|null The requested value if available
     */
    public function getAttribute($key)
    {
        // do nothing here
    }

    /**
     * Returns the session factory.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionFactory The session factory instance
     */
    public function getSessionFactory()
    {
        // do nothing here
    }

    /**
     * Returns the servlet manager instance.
     *
     * @return \AppserverIo\Psr\Servlet\ServletContextInterface The servlet manager instance
     */
    public function getServletManager()
    {
        // do nothing here
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
        // do nothing here
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
        /** @var \AppserverIo\Psr\Servlet\ServletContextInterface|\AppserverIo\Psr\Application\ManagerInterface $servletManager */
        $servletManager = $application->search(ServletContextInterface::IDENTIFIER);

        // load the settings, set the default session save path
        $sessionSettings = $this->getSessionSettings();
        $sessionSettings->setSessionSavePath($application->getSessionDir());

        // if we've session parameters defined in our servlet context
        if ($servletManager->hasSessionParameters()) {
            // we want to merge the session settings from the servlet context
            $sessionSettings->mergeServletContext($servletManager);
        }
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
    public function create(
        $id,
        $name,
        $lifetime = null,
        $maximumAge = null,
        $domain = null,
        $path = null,
        $secure = null,
        $httpOnly = null
    ) {

        // copy the default session configuration for lifetime from the settings
        if ($lifetime == null) {
            $lifetime = time() + $this->getSessionSettings()->getSessionCookieLifetime();
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
        $session = Session::emptyInstance();
        $session->init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly);

        // attach the session to the manager
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
        $this->getSessions()->add($session->getId(), $session);
    }

    /**
     * Tries to find a session for the given request. The session-ID will be
     * searched in the cookie header of the request, and in the request query
     * string. If both values are present, the value in the query string takes
     * precedence. If no session id is found, a new one is created and assigned
     * to the request.
     *
     * @param string $id The unique session ID to that has to be returned
     *
     * @return \AppserverIo\Psr\Servlet\Http\ServletSessionInterface|null The requested session
     */
    public function find($id)
    {

        // return immediately if the requested session ID is empty
        if (empty($id)) {
            return;
        }

        // declare the session variable
        $session = null;

        // query whether or not the session with the passed ID exists
        if ($this->getSessions()->exists($id)) {
            $session = $this->getSessions()->get($id);

        } else {
            // iterate over the session handlers and try to un-persist the session
            /** @var \AppserverIo\Appserver\ServletEngine\Session\SessionHandlerInterface $sessionHandler */
            foreach ($this->getSessionHandlers() as $sessionHandler) {
                try {
                    if ($session = $sessionHandler->load($id)) {
                        $this->attach($session);
                        break;
                    }

                } catch (\Exception $e) {
                    // log the exception if a system logger is available
                    if ($logger = $this->getLogger(LoggerUtils::SYSTEM)) {
                        $logger->error($e->__toString());
                    }
                }
            }
        }

        // if we found a session, we've to check if it can be resumed
        if ($session instanceof ServletSessionInterface) {
            if ($session->canBeResumed()) {
                $session->resume();
                return $session;
            }
        }
    }

    /**
     * Flushes the session storage and persists all sessions.
     *
     * @return void
     */
    public function flush()
    {

        // persist all sessions
        /** @var \AppserverIo\Psr\Servlet\ServletSessionInterface $session */
        foreach ($this->getSessions() as $session) {
            // iterate over the session handlers and persist the sessions
            /** @var \AppserverIo\Appserver\ServletEngine\Session\SessionHandlerInterface $sessionHandler */
            foreach ($this->getSessionHandlers() as $sessionHandler) {
                try {
                    $sessionHandler->save($session);
                } catch (\Exception $e) {
                    // log the exception if a system logger is available
                    if ($logger = $this->getLogger(LoggerUtils::SYSTEM)) {
                        $logger->error($e->__toString());
                    }
                }
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
        return SessionManagerInterface::IDENTIFIER;
    }

    /**
     * Shutdown the session manager instance.
     *
     * @return void
     * \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {
        $this->getGarbageCollector()->stop();
    }

    /**
     * Return's the logger with the requested name. First we look in the
     * application and then in the system itself.
     *
     * @param string $loggerName The name of the logger to return
     *
     * @return \Psr\Log\LoggerInterface The logger with the requested name
     */
    protected function getLogger($loggerName)
    {

        try {
            // first let's see if we've an application logger registered
            if ($logger = $this->getApplication()->getLogger($loggerName)) {
                return $logger;
            }

            // then try to load the global logger instance if available
            return $this->getApplication()->getNamingDirectory()->search(sprintf('php:global/log/%s', $loggerName));

        } catch (NamingException $ne) {
            // do nothing, we simply have no logger with the requested name
        }
    }
}
