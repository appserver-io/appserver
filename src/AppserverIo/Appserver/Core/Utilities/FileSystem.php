<?php

/**
 * AppserverIo\Appserver\Core\Utilities\FileSystem
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
     * Chmod function
     *
     * @param string $path     Relative or absolute path to a file or directory which should be processed.
     * @param int    $perm     The permissions any file or dir should get.

     * @return bool
     */
    public static function chmod($path, $perm)
    {
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
        // check if the path exists
        if (!file_exists($path)) {
            return false;
        }
        chown($path, $user);
        // check if group is given too
        if (!is_null($group)) {
            chgrp($path, $group);
        }
        return true;
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
     * @return bool  Returns TRUE if the path if found and FALSE if not.
     */
    public static function recursiveChmod($path, $filePerm = 0644, $dirPerm = 0755)
    {
        // check if the path exists
        if (!file_exists($path)) {
            return false;
        }

        // see whether this is a file
        if (is_file($path)) {
            // Chmod the file with our given filepermissions
            FileSystem::chmod($path, $filePerm);

            // if this is a directory...
        } elseif (is_dir($path)) {
            // then get an array of the contents
            $foldersAndFiles = scandir($path);

            // parse every result...
            foreach ($foldersAndFiles as $entry) {
                if (in_array($entry, array('.', '..'))) {
                    continue;
                }
                // and call this function again recursively, with the same permissions
                FileSystem::recursiveChmod($path . DIRECTORY_SEPARATOR . $entry, $filePerm, $dirPerm);
            }

            // when we are done with the contents of the directory, we chmod the directory itself
            FileSystem::chmod($path, $dirPerm);
        }

        // everything seemed to work out well, return true
        return true;
    }

    /**
     * Chowns files and folders with different permissions.
     *
     * This is an all-PHP alternative to using: \n
     * <tt>exec("find ".$path." -type f -exec chmod 644 {} \;");</tt> \n
     * <tt>exec("find ".$path." -type d -exec chmod 755 {} \;");</tt>
     *
     * @param string $path  Relative or absolute path to a file or directory which should be processed.
     * @param int    $user  The user that should gain owner rights.
     * @param int    $group The group that should gain group rights.
     *
     * @return bool  Returns TRUE if the path if found and FALSE if not.
     */
    public static function recursiveChown($path, $user, $group)
    {
        // check if the path exists
        if (!file_exists($path)) {
            return false;
        }

        // see whether this is a file
        if (is_file($path)) {
            // Chown the file with our given owner group
            FileSystem::chown($path, $user, $group);

            // if this is a directory...
        } elseif (is_dir($path)) {
            // then get an array of the contents
            $foldersAndFiles = scandir($path);

            // parse every result...
            foreach ($foldersAndFiles as $entry) {
                if (in_array($entry, array('.', '..'))) {
                    continue;
                }
                // and call this function again recursively, with the same permissions
                FileSystem::recursiveChown($path . DIRECTORY_SEPARATOR . $entry, $user, $group);
            }

            // when we are done with the contents of the directory, we chmod the directory itself
            FileSystem::chown($path, $user, $group);
        }

        // everything seemed to work out well, return true
        return true;
    }
}
