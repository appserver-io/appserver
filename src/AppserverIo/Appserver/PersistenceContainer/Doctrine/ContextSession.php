<?php

/**
 * AppserverIo\Appserver\PersistenceContainer\Doctrine\ContextSession
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
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\PersistenceContainer\Doctrine;

use AppserverIo\Collections\HashMap;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\RemoteMethodInvocation\SessionInterface;
use AppserverIo\RemoteMethodInvocation\RemoteMethodInterface;
use AppserverIo\RemoteMethodInvocation\ConnectionInterface;
use AppserverIo\RemoteMethodInvocation\InitialContextProxy;

/**
 * The interface for the remote connection.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/rmi
 * @link      http://www.appserver.io
 */
class ContextSession extends HashMap implements SessionInterface
{

    /**
     * The connection instances.
     *
     * @var array
     */
    protected $connections = null;

    /**
     * The session ID used for the connection.
     *
     * @var string
     */
    protected $sessionId = null;

    /**
     * The servlet request to load the session ID used for the connection.
     *
     * @var string
     */
    protected $servletRequest = null;

    /**
     * Initializes the session with the connection.
     *
     * @param \AppserverIo\RemoteMethodInvocation\ConnectionInterface $connection The connection for the session
     */
    public function __construct(ConnectionInterface $connection)
    {

        // parent constructor to ensure property preset
        parent::__construct(null);

        // initialize the array for the collections
        $this->connections = array();

        // add the passed connection
        $this->addConnection($connection);
    }

    /**
     * Re-Attaches the beans bound to this session to the container.
     *
     * @return void
     */
    public function __destruct()
    {
        $this->cleanUp();
    }

    /**
     * Clean-Up the session context by re-attaching the
     * session beans to the container.
     *
     * @return void
     */
    protected function cleanUp()
    {

        /*
        // query whether we've beans that has to be re-attached to the container or not
        if ($this->size() > 0) {
            // iterate over all connections to query if the bean has to be re-attached
            foreach ($this->getConnections() as $connection) {
                // query whether we've local context connection or not
                if ($application = $connection->getApplication()) {
                    // load the bean manager instance from the application
                    $beanManager = $application->search('BeanContextInterface');

                    // load the session-ID
                    $sessionId = $this->getSessionId();

                    // attach all beans of this session
                    foreach ($this->items as $instance) {
                        $beanManager->attach($instance, $sessionId);
                    }
                }
            }
        }
        */
    }

    /**
     * Injects the servlet request to load the session ID, for the remote method call, from.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface $servletRequest The servlet request instance to inject
     *
     * @return void
     */
    public function injectServletRequest(HttpServletRequestInterface $servletRequest)
    {
        $this->servletRequest = $servletRequest;
    }

    /**
     * Returns the servlet request instance to load the session ID from.
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface The servlet request instance
     */
    public function getServletRequest()
    {
        return $this->servletRequest;
    }

    /**
     * Add's the passed connection to the session's connection collection.
     *
     * @param \AppserverIo\RemoteMethodInvocation\ConnectionInterface $connection The connection instance to add
     *
     * @return void
     */
    public function addConnection(ConnectionInterface $connection)
    {
        $this->connections[] = $connection;
    }

    /**
     * Returns the collection with the session's connections.
     *
     * @return array The collection with the session's connections
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Returns the connection instance.
     *
     * @param integer $key The key of the connection to return
     *
     * @return \AppserverIo\RemoteMethodInvocation\ConnectionInterface The connection instance
     */
    public function getConnection($key = 0)
    {
        return $this->connections[$key];
    }

    /**
     * Returns the ID of the session to use for connecting to the SFSBs.
     *
     * @return string The session ID
     * @see \AppserverIo\RemoteMethodInvocation\SessionInterface::getSessionId()
     */
    public function getSessionId()
    {

        // this is necessary, because in most cases, the session will be started after the
        // SFSB has been injected, so we've to query for a session in method invocation

        // query whether we've a HTTP session ID in the servlet request.
        /** \AppserverIo\Psr\Servlet\Http\HttpSessionInterface $session */
        if ($this->getServletRequest() && $session = $this->getServletRequest()->getSession()) {
            return $session->getId();
        }

        // query whether we've a session ID that has been manually set
        if ($this->sessionId != null) {
            return $this->sessionId;
        }
    }

    /**
     * The session ID to use.
     *
     * @param string $sessionId The session ID to use
     *
     * @return void
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Invokes the remote method over the connection.
     *
     * @param \AppserverIo\RemoteMethodInvocation\RemoteMethodInterface $remoteMethod The remote method call to invoke
     *
     * @return mixed the method return value
     * @see AppserverIo\RemoteMethodInvocation\SessionInterface::send()
     * @todo Refactor to replace check for 'setSession' method, e. g. check for an interface
     */
    public function send(RemoteMethodInterface $remoteMethod)
    {

        // create an array to store connection response temporarily
        $responses = array();

        // iterate over all connections and invoke the remote method call
        foreach ($this->getConnections() as $key => $connection) {
            // invoke the remote method on the connection
            $responses[$key] = $connection->send($remoteMethod);

            // check if a proxy has been returned
            if (method_exists($responses[$key], 'setSession')) {
                $responses[$key]->setSession($this);
            }
        }

        // clean-up the session context
        $this->cleanUp();

        // return the response of the first connection
        return reset($responses);
    }

    /**
     * Creates a remote initial context instance.
     *
     * @return \AppserverIo\RemoteMethodInvocation\RemoteObjectInterface The proxy for the initial context
     * @see \AppserverIo\RemoteMethodInvocation\SessionInterface::createInitialContext()
     */
    public function createInitialContext()
    {
        $initialContext = new InitialContextProxy();
        $initialContext->__setSession($this);
        return $initialContext;
    }
}
