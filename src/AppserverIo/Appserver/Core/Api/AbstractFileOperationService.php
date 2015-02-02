<?php
/**
 * AppserverIo\Appserver\Core\Api\AbstractService
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Appserver\Core\InitialContext;

/**
 * Abstract service implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractFileOperationService extends AbstractService
{
    /**
     * Sets the configured user/group settings on the passed file.
     *
     * @param \SplFileInfo $fileInfo The file to set user/group for
     *
     * @return void
     */
    public function setUserRight(\SplFileInfo $fileInfo)
    {

        // don't do anything under windows
        if ($this->getOsIdentifier() === 'WIN') {
            return;
        }

        // Get our system configuration as it contains the user and group to set
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // Check for the existence of a user
        $user = $systemConfiguration->getParam('user');
        if (!empty($user)) {
            chown($fileInfo, $user);
        }

        // Check for the existence of a group
        $group = $systemConfiguration->getParam('group');
        if (!empty($group)) {
            chgrp($fileInfo, $group);
        }
    }

    /**
     * Will set the owner and group on the passed directory.
     *
     * @param \SplFileInfo $targetDir The directory to set the rights for
     *
     * @return void
     */
    public function setUserRights(\SplFileInfo $targetDir)
    {
        // we don't do anything under Windows
        if ($this->getOsIdentifier() === 'WIN') {
            return;
        }

        // we don't have a directory to change the user/group permissions for
        if ($targetDir->isDir() === false) {
            return;
        }

        // Get our system configuration as it contains the user and group to set
        $systemConfiguration = $this->getInitialContext()->getSystemConfiguration();

        // get all the files recursively
        $files = $this->globDir($targetDir . '/*');

        // Check for the existence of a user
        $user = $systemConfiguration->getParam('user');
        if (!empty($user)) {
            // Change the rights of everything within the defined dirs
            foreach ($files as $file) {
                chown($file, $user);
            }
            chown($targetDir, $user);
        }

        // Check for the existence of a group
        $group = $systemConfiguration->getParam('group');
        if (!empty($group)) {
            // Change the rights of everything within the defined dirs
            foreach ($files as $file) {
                chgrp($file, $group);
            }
            chgrp($targetDir, $group);
        }
    }

    /**
     * Init the umask to use creating files/directories.
     *
     * @return void
     * @throws \Exception Is thrown if the umask can not be set
     */
    public function initUmask()
    {

        // don't do anything under Windows
        if ($this->getOsIdentifier() === 'WIN') {
            return;
        }

        // set the configured umask to use
        umask($newUmask = $this->getInitialContext()->getSystemConfiguration()->getParam('umask'));

        // check if we have successfully set the umask
        if (umask() != $newUmask) {
            // check if set, throw an exception if not
            throw new \Exception(sprintf('Can\'t set configured umask \'%s\' found \'%\' instead', $newUmask, umask()));
        }
    }

    /**
     * Creates the passed directory with the umask specified in the system
     * configuration and sets the user permissions.
     *
     * @param \SplFileInfo $directoryToCreate The directory that should be created
     *
     * @return void
     * @throws \Exception Is thrown if the directory can't be created
     */
    public function createDirectory(\SplFileInfo $directoryToCreate)
    {

        // set the umask that is necessary to create the directory
        $this->initUmask();

        // we don't have a directory to change the user/group permissions for
        if ($directoryToCreate->isDir() === false) {
            // create the directory if necessary
            if (mkdir($directoryToCreate->getPathname()) === false) {
                throw new \Exception(sprintf('Directory %s can\'t be created', $directoryToCreate->getPathname()));
            }
        }

        // load the deployment service
        $this->setUserRights($directoryToCreate);
    }

    /**
     * Deletes all files and subdirectories from the passed directory.
     *
     * @param \SplFileInfo $dir             The directory to remove
     * @param bool         $alsoRemoveFiles The flag for removing files also
     *
     * @return void
     */
    public function cleanUpDir(\SplFileInfo $dir, $alsoRemoveFiles = true)
    {

        // first check if the directory exists, if not return immediately
        if ($dir->isDir() === false) {
            return;
        }

        // remove old archive from webapps folder recursively
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir->getPathname()),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($files as $file) {
            // skip . and .. dirs
            if ($file->getFilename() === '.' || $file->getFilename() === '..') {
                continue;
            }
            if ($file->isDir()) {
                @rmdir($file->getRealPath());
            } elseif ($file->isFile() && $alsoRemoveFiles) {
                unlink($file->getRealPath());
            } else {
                // do nothing, because file should NOT be deleted obviously
            }
        }
    }

    /**
     * Copies a directory recursively.
     *
     * @param string $src The source directory to copy
     * @param string $dst The target directory
     *
     * @return void
     */
    public function copyDir($src, $dst)
    {
        if (is_link($src)) {
            symlink(readlink($src), $dst);
        } elseif (is_dir($src)) {
            if (is_dir($dst) === false) {
                mkdir($dst, 0775, true);
            }
            // copy files recursive
            foreach (scandir($src) as $file) {
                if ($file != '.' && $file != '..') {
                    $this->copyDir("$src/$file", "$dst/$file");
                }
            }

        } elseif (is_file($src)) {
            copy($src, $dst);
        } else {
            // do nothing, we didn't have a directory to copy
        }
    }
}
