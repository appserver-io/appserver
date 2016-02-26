<?php

/**
 * AppserverIo\Appserver\ServletEngine\Session\AbstractSessionHandler
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

namespace AppserverIo\Appserver\ServletEngine\Session;

use AppserverIo\Lang\Reflection\ReflectionClass;
use AppserverIo\Psr\Servlet\ServletSessionInterface;
use AppserverIo\Appserver\ServletEngine\Http\Session;
use AppserverIo\Appserver\ServletEngine\SessionSettingsInterface;
use AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface;

/**
 * A abstract session handler implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
abstract class AbstractSessionHandler implements SessionHandlerInterface
{

    /**
     * The default session marshaller type.
     *
     * @var string
     */
    const DEFAULT_SESSION_MARSHALLER_TYPE = 'AppserverIo\Appserver\ServletEngine\StandardSessionMarshaller';

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
     * Initializes the session handler with the configured params.
     *
     * @param string $sessionMarshallerType The session marshaller type to use
     */
    public function __construct($sessionMarshallerType = FilesystemSessionHandler::DEFAULT_SESSION_MARSHALLER_TYPE)
    {
        // create and inject an instance of the session marshaller to use
        $this->injectSessionMarshaller(new $sessionMarshallerType());
    }

    /**
     * Injects the session settings.
     *
     * @param \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface $sessionSettings Settings for the session handling
     *
     * @return void
     */
    public function injectSessionSettings(SessionSettingsInterface $sessionSettings)
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
    public function injectSessionMarshaller(SessionMarshallerInterface $sessionMarshaller)
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
     * Transforms the passed session instance into a JSON encoded string. If the data contains
     * objects, each of them will be serialized before store them to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $servletSession The servlet session to be transformed
     *
     * @return string The marshalled servlet session representation
     */
    protected function marshall(ServletSessionInterface $servletSession)
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
    protected function unmarshall($marshalled)
    {

        // create a new and empty servlet session instance
        $servletSession = Session::emptyInstance();

        // unmarshall the session data
        $this->getSessionMarshaller()->unmarshall($servletSession, $marshalled);

        // returns the initialized servlet session instance
        return $servletSession;
    }
}
