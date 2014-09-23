<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * This file is based on the Monolog\Handler\RotatingFileHandler class by Christophe Coevoet and Jordi Boggiano.
 * Copyright to Jordi Boggiano might apply to some parts of it
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @author     Christophe Coevoet <stof@notk.org>
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 * @see        https://github.com/Seldaek/monolog/blob/master/src/Monolog/Handler/RotatingFileHandler.php
 */

namespace TechDivision\ApplicationServer;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * TechDivision\ApplicationServer\RotatingMonologHandler
 *
 * Altered copy of the Monolog\Handler\RotatingFileHandler class which allows for proper usage within a pthreads
 * environment, as there is a bug denying a call to a protected method within the same hierarchy if the classes are
 * already known by the parent thread context.
 * The class got also extended to support file rotation based on a maximal file size
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @author     Christophe Coevoet <stof@notk.org>
 * @author     Jordi Boggiano <j.boggiano@seld.be>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class RotatingMonologHandler extends StreamHandler
{

    /**
     * Placeholder for the "date" part of the file format
     *
     * @var string DATE_FORMAT_PLACEHOLDER
     */
    const DATE_FORMAT_PLACEHOLDER = '{date}';

    /**
     * Placeholder for the "original filename" part of the file format
     *
     * @var string FILENAME_FORMAT_PLACEHOLDER
     */
    const FILENAME_FORMAT_PLACEHOLDER = '{filename}';

    /**
     * The maximal possible file size (2Gb).
     * Limited as a precaution due to PHP integer type limitation on x86 systems
     *
     * @var integer MAX_FILE_SIZE
     */
    const MAX_FILE_SIZE = 2147483647;

    /**
     * Placeholder for the "size iterator" part of the file format
     *
     * @var string SIZE_FORMAT_PLACEHOLDER
     */
    const SIZE_FORMAT_PLACEHOLDER = '{sizeIterator}';

    /**
     * Keeps track of the current size dependent iteration of log files
     *
     * @var string $currentSizeIteration
     */
    protected $currentSizeIteration;

    /**
     * Format for the date shown within the rotated filename.
     * E.g. 'Y-m-d'
     *
     * @var string $dateFormat
     */
    protected $dateFormat;

    /**
     * Name of the file to rotate
     *
     * @var string $filename
     */
    protected $filename;

    /**
     * Format of the filename after being rotated
     *
     * @var string $filenameFormat
     */
    protected $filenameFormat;

    /**
     * Number of maximal files to keep.
     * Older files exceeding this limit will be deleted
     *
     * @var integer $maxFiles
     */
    protected $maxFiles;

    /**
     * Maximal size a log file might have after rotation gets triggered
     *
     * @var integer $maxSize
     */
    protected $maxSize;

    /**
     * Whether or not a rotation has to take place at the next possible point in time
     *
     * @var boolean $mustRotate
     */
    protected $mustRotate;

    /**
     * Date at which the next rotation has to take place (if there are no size based rotations before)
     *
     * @var \DateTime $nextRotationDate
     */
    protected $nextRotationDate;

    /**
     * The original name of the file to rotate
     *
     * @var string $originalFilename
     */
    protected $originalFilename;

    /**
     * Default constructor
     *
     * @param string       $filename       Log file base name
     * @param integer      $maxFiles       The maximal amount of files to keep (0 means unlimited)
     * @param integer      $level          The minimum logging level at which this handler will be triggered
     * @param boolean      $bubble         Whether the messages that are handled can bubble up the stack or not
     * @param integer      $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param integer|null $maxSize        The maximal size of a log file in byte (limited to a technical max of 2GB)
     */
    public function __construct($filename, $maxFiles = 0, $level = Logger::DEBUG, $bubble = true, $filePermission = null, $maxSize = null)
    {
        // get the values passed via constructor
        $this->filename = $filename;
        $this->originalFilename = $filename;
        $this->maxFiles = (int)$maxFiles;

        // set some default values
        $this->dateFormat = 'Y-m-d';
        $this->currentSizeIteration = $this->getCurrentSizeIteration();
        $this->mustRotate = false;
        $this->nextRotationDate = new \DateTime('tomorrow');

        // also set the maximal size, but make sure we do not exceed the boundary
        if ($maxSize > RotatingMonologHandler::MAX_FILE_SIZE || is_null($maxSize)) {

            $maxSize = RotatingMonologHandler::MAX_FILE_SIZE;
        }
        $this->maxSize = (int)$maxSize;

        // preset the filename format
        $this->filenameFormat = self::FILENAME_FORMAT_PLACEHOLDER . '-' .
            self::DATE_FORMAT_PLACEHOLDER . '_' .
            self::SIZE_FORMAT_PLACEHOLDER;

        // also construct the parent
        parent::__construct($filename, $level, $bubble, $filePermission);
    }

    /**
     * Will cleanup log files based on the value set for their maximal number
     *
     * @return void
     */
    protected function cleanupFiles()
    {
        // skip GC of old logs if files are unlimited
        if (0 === $this->maxFiles) {
            return;
        }

        $logFiles = glob($this->getGlobPattern());
        if ($this->maxFiles >= count($logFiles)) {
            // no files to remove
            return;
        }

        // Sorting the files by name to remove the older ones
        usort(
            $logFiles,
            function ($a, $b) {
                return strcmp($b, $a);
            }
        );

        // collect the files we have to archive and clean and prepare the archive's internal mapping
        $oldFiles = array();
        foreach (array_slice($logFiles, $this->maxFiles) as $oldFile) {

            $oldFiles[basename($oldFile)] = $oldFile;
        }

        // create an archive from the old files
        $dateTime = new \DateTime();
        $currentTime = $dateTime->format($this->getDateFormat());
        $phar = new \PharData($this->originalFilename . $currentTime .  '.tar');
        $phar->buildFromIterator(new \ArrayIterator($oldFiles));

        // finally delete them as we got them in the archive
        foreach ($oldFiles as $oldFile) {
            if (is_writable($oldFile)) {
                unlink($oldFile);
            }
        }
    }

    /**
     * Will close the handler
     *
     * @return void
     */
    public function close()
    {
        parent::close();

        // might do a rotation before closing
        if (true === $this->mustRotate) {
            $this->rotate();
        }
    }

    /**
     * Will return the name of the file the next rotation will produce
     *
     * @return string
     */
    public function getRotatedFilename()
    {
        $fileInfo = pathinfo($this->filename);
        $currentFilename = str_replace(
            array(
                self::FILENAME_FORMAT_PLACEHOLDER,
                self::DATE_FORMAT_PLACEHOLDER,
                self::SIZE_FORMAT_PLACEHOLDER
            ),
            array(
                $fileInfo['filename'],
                date($this->dateFormat),
                $this->currentSizeIteration
            ),
            $fileInfo['dirname'] . '/' . $this->filenameFormat
        );

        if (!empty($fileInfo['extension'])) {
            $currentFilename .= '.' . $fileInfo['extension'];
        }

        return $currentFilename;
    }

    /**
     * Will return the currently used iteration based on a file's size
     *
     * @return integer
     */
    protected function getCurrentSizeIteration()
    {
        $logFiles = glob($this->getGlobPattern(date($this->dateFormat)));

        $fileCount = count($logFiles);
        if ($fileCount === 0 || $fileCount === 1) {

            return 1;

        } else {

            return count($logFiles) - 1;
        }
    }

    /**
     * Getter for the dateFormat property
     *
     * @return string
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * Getter for the filenameFormat property
     *
     * @return string
     */
    public function getFilenameFormat()
    {
        return $this->filenameFormat;
    }

    /**
     * Will return a glob pattern with which log files belonging to the currently rotated file can be found
     *
     * @param string|\DateTime $dateSpecifier Might specify a specific date to search files for
     *
     * @return string
     */
    public function getGlobPattern($dateSpecifier = '*')
    {
        $fileInfo = pathinfo($this->filename);
        $glob = str_replace(
            array(
                self::FILENAME_FORMAT_PLACEHOLDER,
                self::DATE_FORMAT_PLACEHOLDER,
                self::SIZE_FORMAT_PLACEHOLDER
            ),
            array(
                $fileInfo['filename'],
                $dateSpecifier,
                '*'
            ),
            $fileInfo['dirname'] . '/' . $this->filenameFormat
        );
        if (!empty($fileInfo['extension'])) {
            $glob .= '.' . $fileInfo['extension'];
        }

        return $glob;
    }

    /**
     * Does the rotation of the log file which includes updating the currently used filename as well as cleaning up
     * the log directory
     *
     * @return void
     */
    protected function rotate()
    {
        // update filename
        rename($this->url, $this->getRotatedFilename());

        $this->nextRotationDate = new \DateTime('tomorrow');
        $this->mustRotate = false;
    }

    /**
     * Setter for the date format
     *
     * @param string $dateFormat Form that date will be shown in
     *
     * @return void
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        $this->close();
    }

    /**
     * Setter for the file format
     * If setting this please make use of the defined format placeholder constants
     *
     * @param string $filenameFormat New format to be used
     *
     * @return void
     */
    public function setFilenameFormat($filenameFormat)
    {
        $this->filenameFormat = $filenameFormat;
        $this->close();
    }

    /**
     * Will write a record to the log file.
     * Will take care of checks for a needed rotation
     *
     * @param array $record The record to write to the log file
     *
     * @return void
     *
     * {@inheritdoc}
     */
    public function write(array $record)
    {
        // do we have to rotate based on the current date or the file's size?
        if ($this->nextRotationDate < $record['datetime']) {

            $this->mustRotate = true;
            $this->currentSizeIteration = 1;
            $this->close();

        } elseif (file_exists($this->url) && filesize($this->url) >= $this->maxSize) {

            $this->mustRotate = true;
            $this->close();
            $this->currentSizeIteration ++;
        }

        // do the actual writing
        parent::write($record);

        // cleanup the files we might not want
        $this->cleanupFiles();
    }
}
