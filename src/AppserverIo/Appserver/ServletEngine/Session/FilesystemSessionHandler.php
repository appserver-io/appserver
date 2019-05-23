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

use AppserverIo\Psr\Servlet\ServletSessionInterface;
use AppserverIo\Appserver\ServletEngine\SessionCanNotBeSavedException;
use AppserverIo\Appserver\ServletEngine\SessionCanNotBeDeletedException;
use AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException;

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
            $this->delete($id);
            throw $sdnre;
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

        // prepare the pathname to the file containing the session data
        $filename = $this->getSessionSettings()->getSessionFilePrefix() . $id;
        $pathname = $this->getSessionSavePath($filename);

        // remove the session from the file system
        if ($this->removeSessionFile($pathname) === false) {
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
     * @throws \AppserverIo\Appserver\ServletEngine\SessionCanNotBeSavedException Is thrown if the session can't be saved or no lock for the session file can be obtained
     */
    public function save(ServletSessionInterface $session)
    {

        // don't save the session if it has been destroyed
        if ($session->getId() == null) {
            return;
        }

        // prepare the session filename
        $sessionFilename = $this->getSessionSavePath($this->getSessionSettings()->getSessionFilePrefix() . $session->getId());

        // marshall the session
        $marshalledSession = $this->marshall($session);

        // decode the session from the filesystem
        $fh = fopen($sessionFilename, 'c');

        // try to lock the session file
        if (flock($fh, LOCK_EX) === false) {
            throw new SessionCanNotBeSavedException(
                sprintf(
                    'Can\'t get lock to save session data to file "%s"',
                    $sessionFilename
                )
            );
        }

        // try to truncate the session file
        if (ftruncate($fh, 0) === false) {
            throw new SessionCanNotBeSavedException(
                sprintf(
                    'Can\'t truncate session file "%s"',
                    $sessionFilename
                )
            );
        }

        // finally try to write the session data to the file
        if (fwrite($fh, $marshalledSession) === false) {
            throw new SessionCanNotBeSavedException(
                sprintf(
                    'Session with ID "%s" can\'t be saved to file "%s"',
                    $session->getId(),
                    $sessionFilename
                )
            );
        }

        // try to unlock the session file
        if (flock($fh, LOCK_UN) === false) {
            throw new SessionCanNotBeSavedException(
                sprintf(
                    'Can\'t unlock session file "%s" after saving data',
                    $sessionFilename
                )
            );
        }

        // try to close the session file
        if (fclose($fh) === false) {
            throw new SessionCanNotBeSavedException(
                sprintf(
                    'Can\'t close session file "%s" after saving data',
                    $sessionFilename
                )
            );
        }
    }

    /**
     * Tries to load the session data from the passed filename.
     *
     * @param string $pathname The path of the file to load the session data from
     *
     * @return \AppserverIo\Psr\Servlet\Http\HttpSessionInterface The unmarshalled session
     * @throws \AppserverIo\Appserver\ServletEngine\SessionDataNotReadableException Is thrown if the file containing the session data is not readable or no lock for the session file can be obtained
     */
    protected function unpersist($pathname)
    {

        // the requested session file is not a valid file
        if ($this->sessionFileExists($pathname) === false) {
            return;
        }

        // initialize the variable for the marshalled session data
        $marshalled = null;

        // decode the session from the filesystem
        $fh = fopen($pathname, 'r');

        // try to lock the session file
        if (flock($fh, LOCK_EX) === false) {
            throw new SessionDataNotReadableException(
                sprintf(
                    'Can\'t get lock to load session data from file "%s"',
                    $pathname
                )
            );
        }

        // read the marshalled session data from the file
        while (feof($fh) === false) {
            $marshalled .= fread($fh, 1024);
        }

        // try to unlock the session file
        if (flock($fh, LOCK_UN) === false) {
            throw new SessionDataNotReadableException(
                sprintf(
                    'Can\'t unlock session file "%s" after reading data',
                    $pathname
                )
            );
        }

        // query whether or not the session has been unmarshalled successfully
        if ($marshalled === null) {
            throw new SessionDataNotReadableException(
                sprintf(
                    'Can\'t load any session data from file %s',
                    $pathname
                )
            );
        }

        // unmarshall and return the session data
        return $this->unmarshall($marshalled);
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

        // prepare the expression to select the session files
        $globExpression = sprintf('%s*', $this->getSessionSavePath($this->getSessionSettings()->getSessionFilePrefix()));

        // iterate over the found session files
        foreach (glob($globExpression) as $pathname) {
            // unpersist the session
            $session = $this->unpersist($pathname);

            // query whether or not the session has been expired
            if ($this->sessionTimedOut($session)) {
                // if yes, delete the session + raise the session removal count
                $this->delete($session->getId());
                $sessionRemovalCount++;
            }
        }

        // return the number of removed sessions
        return $sessionRemovalCount;
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
