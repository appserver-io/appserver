<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\RecursiveDirectoryScanner
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

namespace AppserverIo\Appserver\Core\Scanner;

/**
 * This is a scanner that recursively watches the configured directory for files that
 * changed and restarts the appserver by using the OS specific start/stop script.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class RecursiveDirectoryScanner extends DeploymentScanner
{

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext    The initial context instance
     * @param string                                                         $name              The unique scanner name from the configuration
     * @param string                                                         $directory         The directory we want to scan
     * @param integer                                                        $interval          The interval in seconds we want scan the directory
     * @param string                                                         $extensionsToWatch The comma separated list with extensions of files we want to watch
     */
    public function __construct($initialContext, $name, $directory, $interval = 1, $extensionsToWatch = '')
    {

        // call parent constructor
        parent::__construct($initialContext, $name, $directory, $interval, $extensionsToWatch);

        // immediately start the scanner
        $this->start();
    }

    /**
     * Calculates an hash value for all files with certain extensions.
     * This is used to test if the hash value changed, so if
     * it changed, the appserver can react accordingly.
     *
     * @param \SplFileInfo $directory The directory to watch
     *
     * @return string The hash value build out of the found filenames
     */
    protected function getDirectoryHash(\SplFileInfo $directory)
    {

        // clear the stat cache
        clearstatcache();

        // initialize the array for the file stats
        $files = array();
        $result = array();

        // prepare the array with the file extensions of the files used to build the hash
        $extensionsToWatch = $this->getExtensionsToWatch();

        // load all files
        foreach ($extensionsToWatch as $extensionToWatch) {
            $files = array_merge($files, $this->getService()->globDir($directory . DIRECTORY_SEPARATOR . '*.' . $extensionToWatch));
        }

        // iterate over the files
        foreach ($files as $file) {
            // load the last modification time
            $mtime = filemtime($file);

            // store the modification time
            if (isset($result[$file]) === false || $result[$file] !== $mtime) {
                $result[$file] = $mtime;
            }
        }

        // return a md5 hash representation of the directory
        return md5(serialize($result));
    }
}
