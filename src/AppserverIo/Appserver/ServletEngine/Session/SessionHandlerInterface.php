<?php

/**
 * AppserverIo\Appserver\ServletEngine\Session\SessionHandlerInterface
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

use AppserverIo\Psr\Servlet\ServletSessionInterface;

/**
 * Interface for all session handlers.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface SessionHandlerInterface
{

    /**
     * Returns the session settings.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionSettingsInterface The session settings
     */
    public function getSessionSettings();

    /**
     * Returns the session marshaller.
     *
     * @return \AppserverIo\Appserver\ServletEngine\SessionMarshallerInterface The session marshaller
     */
    public function getSessionMarshaller();

    /**
     * Loads the session with the passed ID from the persistence layer and
     * returns it.
     *
     * @param string $id The ID of the session we want to unpersist
     *
     * @return \AppserverIo\Psr\Servlet\ServletSessionInterface The unpersisted session
     */
    public function load($id);

    /**
     * Deletes the session with the passed ID from the persistence layer.
     *
     * @param string $id The ID of the session we want to delete
     *
     * @return void
     */
    public function delete($id);

    /**
     * Saves the passed session to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $session The session to save
     *
     * @return void
     */
    public function save(ServletSessionInterface $session);

    /**
     * Collects the garbage by deleting expired sessions.
     *
     * @return integer The number of removed sessions
     */
    public function collectGarbage();
}
