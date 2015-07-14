<?php

/**
 * \AppserverIo\Appserver\Core\Utilities\FileSystem
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Utilities;

/**
 * Utility class that provides simple file system commands
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class FileSystem
{

    /**
     * OS identifier strings as returned by FileSystem::getOsIdentifier().
     * There are a lot more possible values, but these are the most used ones
     * @see http://stackoverflow.com/questions/738823/possible-values-for-php-os
     * @see https://en.wikipedia.org/wiki/Uname#Table_of_standard_uname_output
     *
     * @var string OS_IDENTIFIER_LIN
     * @var string OS_IDENTIFIER_DARWIN
     * @var string OS_IDENTIFIER_WIN
     */
    const OS_IDENTIFIER_LINUX = 'LIN';
    const OS_IDENTIFIER_DARWIN = 'DAR';
    const OS_IDENTIFIER_WIN = 'WIN';

    /**
     * Chmod function
     *
     * @param string $path Relative or absolute path to a file or directory which should be processed.
     * @param int    $perm The permissions any file or dir should get.

     * @return bool
     */
    public static function chmod($path, $perm)
    {

        // don't do anything under Windows
        if (FileSystem::getOsIdentifier() === self::OS_IDENTIFIER_WIN) {
            return;
        }

        // change the mode
        return chmod($path, $perm);
    }

    /**
     * Chown function
     *
     * @param string   $path  Relative or absolute path to a file or directory which should be processed.
     * @param int      $user  The user that should gain owner rights.
     * @param int|null $group The group that should gain group rights.
     *
     * @return bool
     */
    public static function chown($path, $user, $group = null)
    {

        // don't do anything under Windows
        if (FileSystem::getOsIdentifier() === self::OS_IDENTIFIER_WIN) {
            return;
        }

        // check if the path exists
        if (!file_exists($path)) {
            return false;
        }

        // change the owner
        chown($path, $user);

        // check if group is given too
        if (!is_null($group)) {
            chgrp($path, $group);
        }

        return true;
    }

    /**
     * Deletes all files and subdirectories from the passed directory.
     *
     * @param \SplFileInfo $dir             The directory to remove
     * @param bool         $alsoRemoveFiles The flag for removing files also
     *
     * @return void
     */
    public static function cleanUpDir(\SplFileInfo $dir, $alsoRemoveFiles = true)
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
    public static function copyDir($src, $dst)
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
                    FileSystem::copyDir("$src/$file", "$dst/$file");
                }
            }

        } elseif (is_file($src)) {
            copy($src, $dst);
        } else {
            // do nothing, we didn't have a directory to copy
        }
    }

    /**
     * Creates the passed directory.
     *
     * @param string  $directoryToCreate The directory that should be created
     * @param integer $mode              The mode to create the directory with
     * @param boolean $recursive         TRUE if the directory has to be created recursively, else FALSE
     *
     * @return void
     * @throws \Exception Is thrown if the directory can't be created
     */
    public static function createDirectory($directoryToCreate, $mode = 0775, $recursive = false)
    {

        // we don't have a directory to change the user/group permissions for
        if (is_dir($directoryToCreate) === false) {
            // create the directory if necessary
            if (mkdir($directoryToCreate, $mode, $recursive) === false) {
                throw new \Exception(sprintf('Directory %s can\'t be created', $directoryToCreate));
            }
        }
    }

    /**
     * Init the umask to use creating files/directories, either with
     * the passed value or the one found in the configuration.
     *
     * @param integer $umask The new umask to set
     *
     * @return void
     * @throws \Exception Is thrown if the umask can not be set
     */
    public static function initUmask($umask)
    {

        // don't do anything under Windows
        if (FileSystem::getOsIdentifier() === self::OS_IDENTIFIER_WIN) {
            return;
        }

        // set new umask to use
        umask($umask);

        // query whether the new umask has been set or not
        if (umask() != $umask) {
            throw new \Exception(sprintf('Can\'t set umask \'%s\' found \'%\' instead', $umask, umask()));
        }
    }

    /**
     * Will return a three character OS identifier e.g. WIN or LIN
     *
     * @return string
     */
    public static function getOsIdentifier()
    {
        return strtoupper(substr(PHP_OS, 0, 3));
    }

    /**
     * Recursively parses and returns the directories and files that matches
     * the passed glob pattern.
     *
     * @param string  $pattern The glob pattern used to parse the directories
     * @param integer $flags   The flags passed to the glob function
     *
     * @return array The directories matches the passed glob pattern
     * @link http://php.net/glob
     */
    public static function globDir($pattern, $flags = 0)
    {

        // parse the first directory
        $files = glob($pattern, $flags);

        // parse all subdirectories
        foreach (glob(dirname($pattern). DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR|GLOB_NOSORT|GLOB_BRACE) as $dir) {
            $files = array_merge($files, FileSystem::globDir($dir . DIRECTORY_SEPARATOR . basename($pattern), $flags));
        }

        // return the array with the files matching the glob pattern
        return $files;
    }

    /**
     * Will set the owner and group on the passed directory.
     *
     * @param string $path  The directory to set the rights for
     * @param string $user  The user to set
     * @param string $group The group to set
     *
     * @return void
     */
    public static function recursiveChown($path, $user, $group = null)
    {

        // we don't do anything under Windows
        if (FileSystem::getOsIdentifier() === self::OS_IDENTIFIER_WIN) {
            return;
        }

        // we don't have a directory to change the user/group permissions for
        if (is_dir($path) === false) {
            return;
        }

        // get all the files recursively
        $files = FileSystem::globDir($path . '/*');

        // query whether we've a user passed
        if (empty($user) === false) {
            // Change the rights of everything within the defined dirs
            foreach ($files as $file) {
                chown($file, $user);
            }
            chown($path, $user);
        }

        // query whether we've a group passed
        if (empty($group) === false) {
            // Change the rights of everything within the defined dirs
            foreach ($files as $file) {
                if (chgrp($file, $group) === false) {
                    error_log(sprintf('Can\'t change group to %s for file/dir %s', $group, $file));
                }
            }
            chgrp($path, $group);
        }
    }

    /**
     * Chmods files and folders with different permissions.
     *
     * This is an all-PHP alternative to using: \n
     * <tt>exec("find ".$path." -type f -exec chmod 644 {} \;");</tt> \n
     * <tt>exec("find ".$path." -type d -exec chmod 755 {} \;");</tt>
     *
     * @param string $path     Relative or absolute path to a file or directory which should be processed.
     * @param int    $filePerm The permissions any found files should get.
     * @param int    $dirPerm  The permissions any found folder should get.
     *
     * @return void
     */
    public static function recursiveChmod($path, $filePerm = 0644, $dirPerm = 0755)
    {

        // don't do anything under Windows
        if (FileSystem::getOsIdentifier() === self::OS_IDENTIFIER_WIN) {
            return;
        }

        // check if the directory exists
        if (is_dir($path) === false) {
            return false;
        }

        // get all the files recursively
        $files = FileSystem::globDir($path . '/*');

        // iterate over all directories and files
        foreach ($files as $file) {
            // see whether this is a file
            if (is_file($file)) {
                // chmod the file with our given file permissions
                FileSystem::chmod($file, $filePerm);
            // if this is a directory...
            } elseif (is_dir($file)) {
                // chmod the directory itself
                FileSystem::chmod($file, $dirPerm);
            }
        }

        // change the permmissions of the directory itself
        FileSystem::chmod($path, $dirPerm);
    }
}
