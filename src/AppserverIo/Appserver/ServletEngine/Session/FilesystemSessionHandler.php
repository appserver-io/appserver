<?php

/**
 * AppserverIo\Appserver\ServletEngine\Session\FilesystemSessionHandler
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
 * A session handler implementation that uses the filesystem
 * to persist sessions.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FilesystemSessionHandler extends AbstractSessionHandler
{

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
            // prepare the pathname to the file containing the session data
            $filename = $this->getSessionSettings()->getSessionFilePrefix() . $id;
            $pathname = $this->getSessionSavePath($filename);

            // unpersist and return the session from the file system
            return $this->unpersist($pathname);

        } catch (SessionDataNotReadableException $sdnre) {
            // this maybe happens when the session file is corrupt
            $this->removeSessionFile($pathname);
        }
    }

    /**
     * Deletes the session with the passed ID from the persistence layer.
     *
     * @param string $id The ID of the session we want to delete
     *
     * @return void
     */
    public function delete($id)
    {

        // prepare the pathname to the file containing the session data
        $filename = $this->getSessionSettings()->getSessionFilePrefix() . $id;
        $pathname = $this->getSessionSavePath($filename);

        // remove the session from the file system
        $this->removeSessionFile($pathname);
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

        // don't save the session if it has been destroyed
        if ($session->getId() == null) {
            return;
        }

        // prepare the session filename
        $sessionFilename = $this->getSessionSavePath($this->getSessionSettings()->getSessionFilePrefix() . $session->getId());

        // update the checksum and the file that stores the session data
        file_put_contents($sessionFilename, $this->marshall($session));
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

        // we want to know what inactivity timeout we've to check the sessions for
        $inactivityTimeout = $this->getSessionSettings()->getInactivityTimeout();

        // prepare the expression to select the session files
        $globExpression = sprintf('%s*', $this->getSessionSavePath($this->getSessionSettings()->getSessionFilePrefix()));

        // iterate over the found session files
        foreach (glob($globExpression) as $pathname) {
            // unpersist the session
            $session = $this->unpersist($pathname);

            // load the sessions last activity timestamp
            $lastActivitySecondsAgo = time() - $session->getLastActivityTimestamp();

            // query whether or not the session has been expired
            if ($lastActivitySecondsAgo > $inactivityTimeout) {
                // if yes, delete the file
                if (unlink($pathname) === true) {
                    // raise the session removal count
                    $sessionRemovalCount++;
                }
            }
        }

        // return the number of removed sessions
        return $sessionRemovalCount;
    }

    /**
     * Tries to load the session data from the passed filename.
     *
     * @param string $pathname The path of the file to load the session data from
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpSessionInterface The unmarshalled session
     * @throws \AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException Is thrown if the file containing the session data is not readable
     */
    protected function unpersist($pathname)
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

    /**
     * Checks if a file with the passed name containing session data exists.
     *
     * @param string $pathname The path of the file to check
     *
     * @return boolean TRUE if the file exists, else FALSE
     */
    protected function sessionFileExists($pathname)
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
    protected function removeSessionFile($pathname)
    {
        if (file_exists($pathname)) {
            return unlink($pathname);
        }
        return false;
    }

    /**
     * Returns the default path to persist sessions.
     *
     * @param string $toAppend A relative path to append to the session save path
     *
     * @return string The default path to persist session
     */
    protected function getSessionSavePath($toAppend = null)
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
}
