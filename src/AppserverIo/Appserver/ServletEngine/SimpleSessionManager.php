<?php

/**
 * \AppserverIo\Appserver\ServletEngine\SimpleSessionManager
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
use AppserverIo\Psr\Servlet\ServletSessionInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Appserver\ServletEngine\Http\SimpleSession;
use AppserverIo\Psr\Application\ManagerConfigurationInterface;

/**
 * A standard session manager implementation that provides session
 * persistence while server has not been restarted.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Storage\StorageInterface                           $sessions          The sessions
 * @property \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface   $sessionSettings   Settings for the session handling
 * @property \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface $sessionMarshaller The session marshaller instance
 */
class SimpleSessionManager implements SessionManagerInterface
{

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
        $servletManager = $application->search('ServletContextInterface');

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
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessionSettings()
    {
        return $this->sessionSettings;
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
     * Returns the application instance.
     *
     * @return \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
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
     * Returns all sessions actually attached to the session manager.
     *
     * @return \AppserverIo\Storage\StorageInterface The container with sessions
     */
    public function getSessions()
    {
        // do nothing here
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
     * Returns the garbage collector instance.
     *
     * @return \AppserverIo\Appserver\ServletEngine\GarbageCollectorInterface The garbage collector instance
     */
    public function getGarbageCollector()
    {
        // do nothing here
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
        $session = SimpleSession::emptyInstance();
        $session->init($id, $name, $lifetime, $maximumAge, $domain, $path, $secure, $httpOnly);

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
        // do nothing here
    }

    /**
     * Unpersists the session with the passed ID from the persistence layer and
     * reattaches it to the internal session storage.
     *
     * @param string $id The ID of the session we want to unpersist
     *
     * @return void
     */
    protected function unpersist($id)
    {

        try {
            // prepare the pathname to the file containing the session data
            $filename = $this->getSessionSettings()->getSessionFilePrefix() . $id;
            $pathname = $this->getSessionSavePath($filename);

            // unpersist the session data itself
            return $this->loadSessionFromFile($pathname);

        } catch (SessionDataNotReadableException $sdnre) {
            // this maybe happens when the session file is corrupt
            $this->removeSessionFile($pathname);
        }
    }

    /**
     * Checks if a file with the passed name containing session data exists.
     *
     * @param string $pathname The path of the file to check
     *
     * @return boolean TRUE if the file exists, else FALSE
     */
    public function sessionFileExists($pathname)
    {
        return file_exists($pathname);
    }

    /**
     * Removes the session file with the passed name containing session data.
     *
     * @param string $pathname The path of the file to remove
     *
     * @return boolean TRUE if the file has successfully been removed, else FALSE
     */
    public function removeSessionFile($pathname)
    {
        if (file_exists($pathname)) {
            return unlink($pathname);
        }
        return false;
    }

    /**
     * Tries to load the session data from the passed filename.
     *
     * @param string $pathname The path of the file to load the session data from
     *
     * @return void
     * @throws \AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException Is thrown if the file containing the session data is not readable
     */
    public function loadSessionFromFile($pathname)
    {

        // the requested session file is not a valid file
        if ($this->sessionFileExists($pathname) === false) {
            return;
        }

        // decode the session from the filesystem
        if (($marshalled = file_get_contents($pathname)) === false) {
            throw new SessionDataNotReadableException(sprintf('Can\'t load session data from file %s', $pathname));
        }

        // create a new session instance from the marshaled object representation
        return $this->unmarshall($marshalled);
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
     * @return \AppserverIo\Psr\Servlet\Http\HttpSessionInterface The requested session
     */
    public function find($id)
    {

       // log a message that the session has been successfully unpersisted
       if ($systemLogger = $this->getApplication()->getLogger(LoggerUtils::SYSTEM)) {
           $systemLogger->info("Now try to lookup session $id");
       }

        // check if the session has already been loaded, if not try to un-persist it
       return $this->unpersist($id);
    }

    /**
     * Saves the passed session to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $session The session to save
     *
     * @return void
     */
    public function save(ServletSessionInterface $session)
    {

        if ($session->getId() == null) {
            return;
        }

        // log a message that the session has been removed successfully
        if ($systemLogger = $this->getApplication()->getLogger(LoggerUtils::SYSTEM)) {
            $systemLogger->info(sprintf("Now try to persist session %s", $session->getId()));
        }

        // prepare the session filename
        $sessionFilename = $this->getSessionSavePath($this->getSessionSettings()->getSessionFilePrefix() . $session->getId());

        // update the checksum and the file that stores the session data
        file_put_contents($sessionFilename, $this->marshall($session));

        // log a message that the session has been removed successfully
        if ($systemLogger = $this->getApplication()->getLogger(LoggerUtils::SYSTEM)) {
            $systemLogger->info(sprintf("Successfully persisted session %s", $session->getId()));
        }
    }

    /**
     * Flushes the session storage and persists all sessions.
     *
     * @return void
     */
    public function flush()
    {
        // do nothing here
    }

    /**
     * Transforms the passed session instance into a JSON encoded string. If the data contains
     * objects, each of them will be serialized before store them to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The servlet session to be transformed
     *
     * @return string The marshalled servlet session representation
     */
    public function marshall(ServletSessionInterface $servletSession)
    {
        return $this->getSessionMarshaller()->marshall($servletSession);
    }

    /**
     * Initializes the session instance from the passed JSON string. If the encoded
     * data contains objects, they will be unserialized before reattached to the
     * session instance.
     *
     * @param string $marshalled The marshaled session representation
     *
     * @return \AppserverIo\Psr\Servlet\ServletSessionInterface The un-marshaled servlet session instance
     */
    public function unmarshall($marshalled)
    {

        // create a new and empty servlet session instance
        $servletSession = SimpleSession::emptyInstance();;

        // unmarshall the session data
        $this->getSessionMarshaller()->unmarshall($servletSession, $marshalled);

        // returns the initialized servlet session instance
        return $servletSession;
    }

    /**
     * Returns the default path to persist sessions.
     *
     * @param string $toAppend A relative path to append to the session save path
     *
     * @return string The default path to persist session
     */
    public function getSessionSavePath($toAppend = null)
    {
        // load the default path
        $sessionSavePath = $this->getSessionSettings()->getSessionSavePath();

        // check if we've something to append
        if ($toAppend != null) {
            $sessionSavePath = $sessionSavePath . DIRECTORY_SEPARATOR . $toAppend;
        }

        // return the session save path
        return $sessionSavePath;
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
        // we don't have to stop anything
    }
}
