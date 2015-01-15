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

use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;
use AppserverIo\Appserver\Application\Interfaces\ContextInterface;

/**
 * This is a scanner that watches a flat directory for files that changed
 * and restarts the appserver by using the OS specific start/stop script.
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
     * @var integer
     */
    // const MAX_FILE_SIZE = 2147483647; // 2GB
    // const MAX_FILE_SIZE =    1048576; // 1MB
    const MAX_FILE_SIZE =        102400; // 100KB

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
     * Maximal size a log file might have after rotation gets triggered.
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
    public function __construct($initialContext, $directory, $interval = 1, $extensionsToWatch = '', $maxFiles = 0, $maxSize = null)
    {

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
    public function getInterval()
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
     * Start's the logrotate scanner that restarts the server
     * when a PHAR should be deployed or undeployed.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractThread::main()
     */
    public function main()
    {

        // load the interval we want to scan the directory
        $interval = $this->getInterval();

        // load the deployment directory
        $directory = $this->getDirectory();

        // prepare the extensions of the file we want to watch
        $extensionsToWatch = sprintf('{%s}', implode(',', $this->getExtensionsToWatch()));

        // log the configured deployment directory
        $this->getSystemLogger()->info(sprintf('Start watching directory %s, interval %d', $directory, $interval));

        while (true) { // watch the deployment directory

            // log the found directory hash value
            $this->getSystemLogger()->info("Now checking files to be rotated");

            foreach (glob($directory . '/*.' . $extensionsToWatch, GLOB_BRACE) as $fileToRotate) {

                $this->getSystemLogger()->info("Now check file $fileToRotate");

                $this->handle($fileToRotate);

                // compress and cleanup files
                $this->compressFiles($fileToRotate);
                $this->cleanupFiles($fileToRotate);
            }

            // if no changes has been found, wait a second
            sleep($interval);
        }
    }

    /**
     * Will return the name of the file the next rotation will produce.
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return string
     */
    protected function getRotatedFilename($fileToRotate)
    {

        // load the file information
        $fileInfo = pathinfo($fileToRotate);

        // prepare the name for rotated file
        $currentFilename = str_replace(
            array(LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER, LogrotateScanner::SIZE_FORMAT_PLACEHOLDER),
            array($fileInfo['filename'], $this->getCurrentSizeIteration($fileToRotate)),
            $fileInfo['dirname'] . '/' . $this->getFilenameFormat()
        );

        // return the name for the rotated file
        return $currentFilename;
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
        $fileInfo = pathinfo($fileToRotate);

        // create a glob expression to find all log files
        $glob = str_replace(
            array(LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER, LogrotateScanner::SIZE_FORMAT_PLACEHOLDER),
            array($fileInfo['filename'], '[0-9]'),
            $fileInfo['dirname'] . '/' . $this->getFilenameFormat()
        );

        // append the file extension if available
        if (empty($fileExtension) === false) {
            $glob .= '.' . $fileExtension;
        }

        // return the glob expression
        return $glob;
    }

    /**
     * Will return the currently used iteration based on a file's size.
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return integer The number of logfiles already exists
     */
    protected function getCurrentSizeIteration($fileToRotate)
    {

        // load an iterator the current log files
        $logFiles = glob($this->getGlobPattern($fileToRotate, 'gz'));

        // count the files
        $fileCount = count($logFiles);

        // return the next iteration
        if ($fileCount === 0) {
            return 1;
        } else {
            return $fileCount + 1;
        }
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
     * Handles the log message.
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return void
     */
    protected function handle($fileToRotate)
    {

        // do we have to rotate based on the current date or the file's size?
        if ($this->nextRotationDate < new \DateTime()) {
            $this->rotate($fileToRotate);
        } elseif (file_exists($fileToRotate) && filesize($fileToRotate) >= $this->maxSize) {
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

        // rotate the file
        rename($fileToRotate, $this->getRotatedFilename($fileToRotate));

        // next rotation date is tomorrow
        $tomorrow = new \DateTime('tomorrow');
        $this->nextRotationDate = $tomorrow->getTimestamp();
    }

    /**
     *
     * @param unknown $fileToRotate
     * @return number
     */
    protected function compressFiles($fileToRotate)
    {

        // load the array with uncompressed, but rotated files
        $logFiles = glob($this->getGlobPattern($fileToRotate));

        // sorting the files by name to remove the older ones
        usort(
            $logFiles,
            function ($a, $b) {
                return strcmp($b, $a);
            }
        );

        // iterate over the uncompressed, but rotated log files
        foreach ($logFiles as $fileToCompress) {

            // compress the log files
            file_put_contents("compress.zlib://$fileToCompress.gz", file_get_contents($fileToCompress));

            // delete the uncompressed file
            unlink($fileToCompress);
        }
    }

    /**
     * Will cleanup log files based on the value set for their maximal number
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return void
     */
    protected function cleanupFiles($fileToRotate)
    {

        // skip GC of old logs if files are unlimited
        if (0 === $this->maxFiles) {
            return;
        }

        // load the rotated log files
        $logFiles = glob($this->getGlobPattern($fileToRotate, 'gz'));

        // query whether we've the maximum number of files reached
        if ($this->maxFiles >= count($logFiles)) {
            return;
        }

        // sorting the files by name to remove the older ones
        usort(
            $logFiles,
            function ($a, $b) {
                return strcmp($b, $a);
            }
        );

        // collect the files we have to archive and clean and prepare the archive's internal mapping
        foreach (array_slice($logFiles, $this->maxFiles) as $fileToDelete) {
            unlink($fileToDelete);
        }
    }
}
