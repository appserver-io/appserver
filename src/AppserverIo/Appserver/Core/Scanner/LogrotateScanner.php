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
    // const MAX_FILE_SIZE = 2147483647;
    const MAX_FILE_SIZE = 1048576;

    /**
     * Placeholder for the "date" part of the file format.
     *
     * @var string
     */
    const DATE_FORMAT_PLACEHOLDER = '{date}';

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
     * Number of maximal files to keep. Older files exceeding this limit will be deleted.
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
     * Format for the date shown within the rotated filename, e. g. 'Y-m-d'.
     *
     * @var string
     */
    protected $dateFormat;

    /**
     * Date at which the next rotation has to take place (if there are no size based rotations before)
     *
     * @var \DateTime
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
            LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER . '-' .
            LogrotateScanner::DATE_FORMAT_PLACEHOLDER . '.' .
            LogrotateScanner::SIZE_FORMAT_PLACEHOLDER;

        // pre-initialize some default values
        $this->dateFormat = 'Y-m-d';
        $this->nextRotationDate = new \DateTime('tomorrow');

        // explode the comma separated list of file extensions
        $this->extensionsToWatch = explode(',', str_replace(' ', '', $extensionsToWatch));
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

            if ($this->nextRotationDate >= new \DateTime()) {
                $this->nextRotationDate = new \DateTime('tomorrow');
            }

             $this->getSystemLogger()->debug("Now check files to be rotated!");

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
     * Will return the name of the file the next rotation will produce
     *
     * @param string $fileToRotate The file to be rotated
     *
     * @return string
     */
    public function getRotatedFilename($fileToRotate)
    {

        $fileInfo = pathinfo($fileToRotate);

        $currentFilename = str_replace(
            array(
                LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER,
                LogrotateScanner::DATE_FORMAT_PLACEHOLDER,
                LogrotateScanner::SIZE_FORMAT_PLACEHOLDER
            ),
            array(
                $fileInfo['filename'],
                date($this->getDateFormat()),
                $this->getCurrentSizeIteration($fileToRotate)
            ),
            $fileInfo['dirname'] . '/' . $this->getFilenameFormat()
        );

        /*
        if (!empty($fileInfo['extension'])) {
            $currentFilename .= '.' . $fileInfo['extension'];
        }
        */

        return $currentFilename;
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

        $globPattern = $this->getGlobPattern($fileToRotate, date($this->dateFormat));

        error_log("Use glob pattern $globPattern to check current size iteration");

        // load an iterator the current log files
        $logFiles = glob($globPattern);

        $fileCount = count($logFiles); // count the files
        if ($fileCount === 0) {
            return 1;
        } else {
            return count($logFiles) + 1;
        }
    }

    /**
     * Getter for the date format to store the logfiles under.
     *
     * @return string The date format to store the logfiles under
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Getter for the file format to store the logfiles under.
     *
     * @return string The file format to store the logfiles under
     */
    public function getFilenameFormat()
    {
        return $this->filenameFormat;
    }

    /**
     * Will return a glob pattern with which log files belonging to the currently rotated file can be found
     *
     * @param string           $fileToRotate  The file to be rotated
     * @param string|\DateTime $dateSpecifier Might specify a specific date to search files for
     * @param string           $fileExtension The file extension
     *
     * @return string
     */
    public function getGlobPattern($fileToRotate, $dateSpecifier = '*', $fileExtension = '')
    {

        // load the file information
        $fileInfo = pathinfo($fileToRotate);

        // create a glob expression to find all log files
        $glob = str_replace(
            array(
                LogrotateScanner::FILENAME_FORMAT_PLACEHOLDER,
                LogrotateScanner::DATE_FORMAT_PLACEHOLDER,
                LogrotateScanner::SIZE_FORMAT_PLACEHOLDER
            ),
            array(
                $fileInfo['filename'],
                $dateSpecifier,
                '[0-9]'
            ),
            $fileInfo['dirname'] . '/' . $this->filenameFormat
        );

        // append the file extension if available
        if (empty($fileExtension) === false) {
            $glob .= '.' . $fileInfo['extension'];
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
    public function handle($fileToRotate)
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
        rename($fileToRotate, $this->getRotatedFilename($fileToRotate));
    }

    /**
     *
     * @param unknown $fileToRotate
     * @return number
     */
    protected function compressFiles($fileToRotate)
    {

        // load the uncompressed files and compress them
        $logFiles = glob($this->getGlobPattern($fileToRotate));

        // sorting the files by name to remove the older ones
        usort(
            $logFiles,
            function ($a, $b) {
                return strcmp($b, $a);
            }
        );

        // collect the files we have to archive and clean and prepare the archive's internal mapping
        $oldFiles = array();
        foreach ($logFiles as $fileToCompress) {
            file_put_contents("compress.zlib://$fileToCompress.gz", file_get_contents($fileToCompress));
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

        $logFiles = glob($this->getGlobPattern($fileToRotate));
        if ($this->maxFiles >= count($logFiles)) { // no files to remove
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
