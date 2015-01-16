<?php

/**
 * AppserverIo\Appserver\Core\Scanner\LogrotateScanner
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Scanner;

use AppserverIo\Appserver\Application\Interfaces\ContextInterface;

/**
 * This is a scanner that watches a flat directory for files that has to
 * be rotated.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class LogrotateScanner extends AbstractScanner
{

    /**
     * The maximal possible file size (2Gb). Limited as a precaution due to PHP
     * integer type limitation on x86 systems.
     *
     * Example values are:
     *
     * 102400 = 100KB
     * 1048576 = 1MB
     * 2147483647 = 2GB
     *
     * @var integer
     */
    const MAX_FILE_SIZE = 1048576;

    /**
     * Placeholder for the "original filename" part of the file format.
     *
     * @var string
     */
    const FILENAME_FORMAT_PLACEHOLDER = '{filename}';

    /**
     * Placeholder for the "size iterator" part of the file format.
     *
     * @var string
     */
    const SIZE_FORMAT_PLACEHOLDER = '{sizeIterator}';

    /**
     * The interval in seconds we use to scan the directory.
     *
     * @var integer
     */
    protected $interval;

    /**
     * A list with extensions of files we want to watch.
     *
     * @var array
     */
    protected $extensionsToWatch;

    /**
     * The directory we want to watch.
     *
     * @var array
     */
    protected $directory;

    /**
     * Number of maximal files to keep. Older files exceeding this limit
     * will be deleted.
     *
     * @var integer
     */
    protected $maxFiles;

    /**
     * Maximal size in byte a file might have after rotation gets triggered.
     *
     * @var integer
     */
    protected $maxSize;

    /**
     * UNIX timestamp at which the next rotation has to take
     * place (if there are no size based rotations before).
     *
     * @var integer
     */
    protected $nextRotationDate;

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext    The initial context instance
     * @param string                                                         $directory         The directory we want to scan
     * @param integer                                                        $interval          The interval in seconds we want scan the directory
     * @param string                                                         $extensionsToWatch The comma separeted list with extensions of files we want to watch
     * @param integer                                                        $maxFiles          The maximal amount of files to keep (0 means unlimited)
     * @param integer|null                                                   $maxSize           The maximal size of a log file in byte (limited to a technical max of 2GB)
     */
    public function __construct(
        ContextInterface $initialContext,
        $directory,
        $interval = 1,
        $extensionsToWatch = '',
        $maxFiles = 0,
        $maxSize = null
    ) {

        // call parent constructor
        parent::__construct($initialContext);

        // initialize the members
        $this->interval = $interval;
        $this->directory = $directory;
        $this->maxFiles = (integer) $maxFiles;

        // also set the maximal size, but make sure we do not exceed the boundary
        if ($maxSize > LogrotateScanner::MAX_FILE_SIZE || is_null($maxSize)) {
            $maxSize = LogrotateScanner::MAX_FILE_SIZE;
        }

        // set the maximum size of log files
        $this->maxSize = (int) $maxSize;

        // pre-initialize the filename format
        $this->filenameFormat =
            LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER . '.' .
            LogrotateScanner::SIZE_FORMAT_PLACEHOLDER;

        // explode the comma separated list of file extensions
        $this->extensionsToWatch = explode(',', str_replace(' ', '', $extensionsToWatch));

        // next rotation date is tomorrow
        $tomorrow = new \DateTime('tomorrow');
        $this->nextRotationDate = $tomorrow->getTimestamp();
    }

    /**
     * Returns the interval in seconds we want to scan the directory.
     *
     * @return integer The interval in seconds
     */
    protected function getInterval()
    {
        return $this->interval;
    }

    /**
     * Returns the path to the deployment directory
     *
     * @return \SplFileInfo The deployment directory
     */
    public function getDirectory()
    {
        return new \SplFileInfo($this->getService()->getBaseDirectory($this->directory));
    }

    /**
     * Returns an array with file extensions that should be
     * watched for new deployments.
     *
     * @return array The array with the file extensions
     */
    protected function getExtensionsToWatch()
    {
        return $this->extensionsToWatch;
    }

    /**
     * Getter for the file format to store the logfiles under.
     *
     * @return string The file format to store the logfiles under
     */
    protected function getFilenameFormat()
    {
        return $this->filenameFormat;
    }

    /**
     * Getter for the maximal size in bytes a log file might have after rotation
     * gets triggered.
     *
     * @return integer The maximal file size in bytes
     */
    protected function getMaxSize()
    {
        return $this->maxSize;
    }

    /**
     * Getter for the number of maximal files to keep. Older files exceeding this limit
     * will be deleted.
     *
     * @return integer The maximal number of files to keep
     */
    protected function getMaxFiles()
    {
        return $this->maxFiles;
    }

    /**
     * Setter for UNIX timestamp at which the next rotation has to take.
     *
     * @param integer $nextRotationDate The next rotation date as UNIX timestamp
     *
     * @return void
     */
    protected function setNextRotationDate($nextRotationDate)
    {
        $this->nextRotationDate = $nextRotationDate;
    }

    /**
     * Getter for UNIX timestamp at which the next rotation has to take.
     *
     * @return string The next rotation date as UNIX timestamp
     */
    protected function getNextRotationDate()
    {
        return $this->nextRotationDate;
    }

    /**
     * Start the logrotate scanner that queries whether the configured
     * log files has to be rotated or not.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractThread::main()
     */
    public function main()
    {

        // load the interval we want to scan the directory
        $interval = $this->getInterval();

        // load the configured directory
        $directory = $this->getDirectory();

        // prepare the extensions of the file we want to watch
        $extensionsToWatch = sprintf('{%s}', implode(',', $this->getExtensionsToWatch()));

        // log the configured deployment directory
        $this->getSystemLogger()->info(
            sprintf('Start scanning directory %s for files to be rotated (interval %d)', $directory, $interval)
        );

        while (true) { // watch the configured directory

            // iterate over the files to be watched
            foreach (glob($directory . '/*.' . $extensionsToWatch, GLOB_BRACE) as $fileToRotate) {

                // log that we're rotate the file
                $this->getSystemLogger()->debug(
                    sprintf('Query wheter it is necessary to rotate %s', $fileToRotate)
                );

                // handle file rotation
                $this->handle($fileToRotate);

                // cleanup files
                $this->cleanup($fileToRotate);
            }

            // sleep a while
            sleep($interval);
        }
    }

    /**
     * Will return a glob pattern with which log files belonging to the currently rotated
     * file can be found.
     *
     * @param string $fileToRotate  The file to be rotated
     * @param string $fileExtension The file extension
     *
     * @return string
     */
    protected function getGlobPattern($fileToRotate, $fileExtension = '')
    {

        // load the file information
        $dirname = pathinfo($fileToRotate, PATHINFO_DIRNAME);
        $filename = pathinfo($fileToRotate, PATHINFO_FILENAME);

        // create a glob expression to find all log files
        $glob = str_replace(
            array(LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER, LogrotateScanner::SIZE_FORMAT_PLACEHOLDER),
            array($filename, '[0-9]'),
            $dirname . '/' . $this->getFilenameFormat()
        );

        // append the file extension if available
        if (empty($fileExtension) === false) {
            $glob .= '.' . $fileExtension;
        }

        // return the glob expression
        return $glob;
    }

    /**
     * Handles the log message.
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return void
     */
    protected function handle($fileToRotate)
    {

        // next rotation date is tomorrow
        $today = new \DateTime();

        // do we have to rotate based on the current date or the file's size?
        if ($this->getNextRotationDate() < $today->getTimestamp()) {
            $this->rotate($fileToRotate);
        } elseif (file_exists($fileToRotate) && filesize($fileToRotate) >= $this->getMaxSize()) {
            $this->rotate($fileToRotate);
        }
    }

    /**
     * Does the rotation of the log file which includes updating the currently
     * used filename as well as cleaning up the log directory.
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return void
     */
    protected function rotate($fileToRotate)
    {

        // clear the filesystem cache
        clearstatcache();

        // query whether the file is NOT available anymore or we dont have access to it
        if (file_exists($fileToRotate) === false ||
            is_writable($fileToRotate) === false) {
            return;
        }

        // query whether the file has any content, because we don't want to rotate empty files
        if (filesize($fileToRotate) === 0) {
            return;
        }

        // load the existing log files
        $logFiles = glob($this->getGlobPattern($fileToRotate, 'gz'));

        // sorting the files by name to remove the older ones
        usort(
            $logFiles,
            function ($a, $b) {
                return strcmp($b, $a);
            }
        );

        // load the information about the found file
        $dirname = pathinfo($fileToRotate, PATHINFO_DIRNAME);
        $filename = pathinfo($fileToRotate, PATHINFO_FILENAME);

        // raise the counter of the rotated files
        foreach ($logFiles as $fileToRename) {

            // load the information about the found file
            $extension = pathinfo($fileToRename, PATHINFO_EXTENSION);
            $basename = pathinfo($fileToRename, PATHINFO_BASENAME);

            // prepare the regex to grep the counter with
            $regex = sprintf('/^%s\.([0-9]{1,})\.%s/', $filename, $extension);

            // check the counter
            if (preg_match($regex, $basename, $counter)) {

                // load and raise the counter by one
                $raised = ((integer) end($counter)) + 1;

                // prepare the new filename
                $newFilename = sprintf('%s/%s.%d.%s', $dirname, $filename, $raised, $extension);

                // rename the file
                rename($fileToRename, $newFilename);
            }
        }

        // rotate the file
        rename($fileToRotate, $newFilename = sprintf('%s/%s.0', $dirname, $filename));

        // compress the log file
        file_put_contents("compress.zlib://$newFilename.gz", file_get_contents($newFilename));

        // delete the old file
        unlink($newFilename);

        // next rotation date is tomorrow
        $tomorrow = new \DateTime('tomorrow');
        $this->setNextRotationDate($tomorrow->getTimestamp());
    }

    /**
     * Will cleanup log files based on the value set for their maximal number
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return void
     */
    protected function cleanup($fileToRotate)
    {

        // load the maximum number of files to keep
        $maxFiles = $this->getMaxFiles();

        // skip GC of old logs if files are unlimited
        if (0 === $maxFiles) {
            return;
        }

        // load the rotated log files
        $logFiles = glob($this->getGlobPattern($fileToRotate, 'gz'));

        // query whether we've the maximum number of files reached
        if ($maxFiles >= count($logFiles)) {
            return;
        }

        // iterate over the files we want to clean-up
        foreach (array_slice($logFiles, $maxFiles) as $fileToDelete) {
            unlink($fileToDelete);
        }
    }
}
