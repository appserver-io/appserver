<?php

/**
 * AppserverIo\Appserver\ServletEngine\Session\ApcSessionHandler
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
use AppserverIo\Appserver\ServletEngine\SessionCanNotBeSavedException;
use AppserverIo\Appserver\ServletEngine\SessionCanNotBeDeletedException;
use AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException;

/**
 * A session handler implementation that uses the PECL APCu PHP extension
 * to persist sessions.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ApcSessionHandler extends AbstractSessionHandler
{

    /**
     * The APCu cache type to use.
     *
     * @var string
     */
    const APCU_CACHE_TYPE_USER = 'user';

    /**
     * Loads the session with the passed ID from the persistence layer and
     * returns it.
     *
     * @param string $id The ID of the session we want to unpersist
     *
     * @return \AppserverIo\Psr\Servlet\ServletSessionInterface The unpersisted session
     */
    public function load($id)
    {

        try {
            return $this->unpersist($id);
        } catch (SessionDataNotReadableException $sdnre) {
            $this->delete($id);
        }
    }

    /**
     * Deletes the session with the passed ID from the persistence layer.
     *
     * @param string $id The ID of the session we want to delete
     *
     * @return void
     * @throws \AppserverIo\Appserver\ServletEngine\SessionCanNotBeDeletedException Is thrown if the session can't be deleted
     */
    public function delete($id)
    {
        if (apc_delete($id) === false) {
            throw new SessionCanNotBeDeletedException(
                sprintf('Session with ID %s can not be deleted', $id)
            );
        }
    }

    /**
     * Saves the passed session to the persistence layer.
     *
     * @param \AppserverIo\Psr\Servlet\ServletSessionInterface $session The session to save
     *
     * @return void
     * @throws \AppserverIo\Appserver\ServletEngine\SessionCanNotBeSavedException Is thrown if the session can't be saved
     */
    public function save(ServletSessionInterface $session)
    {

        // don't save the session if it has been destroyed
        if ($session->getId() == null) {
            return;
        }

        // update the checksum and the file that stores the session data
        if (apc_store($session->getId(), $this->marshall($session)) === false) {
            throw new SessionCanNotBeSavedException(
                sprintf('Session with ID %s can\'t be saved')
            );
        }
    }

    /**
     * Collects the garbage by deleting expired sessions.
     *
     * @return integer The number of removed sessions
     */
    public function collectGarbage()
    {

        // counter to store the number of removed sessions
        $sessionRemovalCount = 0;

        // iterate over the found session items
        foreach (new \ApcIterator(ApcSessionHandler::APCU_CACHE_TYPE_USER) as $item) {
            // initialize the key
            $key = null;
            // explode the APC item
            extract($item);
            // unpersist the session
            $session = $this->unpersist($key);

            // query whether or not the session has been expired
            if ($this->sessionTimedOut($session)) {
                // if yes, delete the file + raise the session removal count
                $this->delete($key);
                $sessionRemovalCount++;
            }
        }

        // return the number of removed sessions
        return $sessionRemovalCount;
    }

    /**
     * Tries to load the session data with the passed ID.
     *
     * @param string $id The ID of the session to load
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpSessionInterface The unmarshalled session
     * @throws \AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException Is thrown if the file containing the session data is not readable
     */
    protected function unpersist($id)
    {

        // the requested session file is not a valid file
        if (apc_exists($id) === false) {
            return;
        }

        // decode the session from the filesystem
        if (($marshalled = apc_fetch($id)) === false) {
            throw new SessionDataNotReadableException(sprintf('Can\'t load session with ID %s', $id));
        }

        // create a new session instance from the marshaled object representation
        return $this->unmarshall($marshalled);
    }
}
