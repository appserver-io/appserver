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
 * @category   Appserver
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace TechDivision\ApplicationServer;

use Monolog\Logger;

/**
 * TechDivision\ApplicationServer\RotatingMonologHandlerTest
 *
 * @category   Appserver
 * @package    TechDivision
 * @subpackage ApplicationServer
 * @author     Bernhard Wick <b.wick@techdivision.com>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class RotatingMonologHandlerTest extends AbstractTest
{

    /**
     * A temporary directory for testing purposes
     *
     * @var string TMP_DIR
     */
    const TMP_DIR = '/_files/var/tmp/';

    /**
     * A temporary file for testing purposes
     *
     * @var string TMP_FILE
     */
    const TMP_FILE = 'test-file.log';

    /**
     * @var  $handler
     */
    protected $handler;

    /**
     * Will return a test record due at a certain date
     *
     * @param \DateTime $dateTime The time the record was apparently logged
     *
     * @return array
     */
    protected function getRecordByDate(\DateTime $dateTime)
    {
        return array (
            'message' => 'Provisioner datasource successfully initialized and executed',
            'context' =>
                array (
                ),
            'level' => 100,
            'level_name' => 'DEBUG',
            'channel' => 'system',
            'datetime' => $dateTime,
            'extra' =>
                array (
                    'file' => '/opt/appserver/app/code/vendor/techdivision/appserver/src/TechDivision/ApplicationServer/Server.php',
                    'line' => 288,
                    'class' => 'TechDivision\\ApplicationServer\\Server',
                    'function' => 'initProvisioners',
                ),
            'formatted' => '[' . $dateTime->format('Y-m-d H:i:s') . '] system.DEBUG: Provisioner datasource successfully initialized and executed [] {"file":"/opt/appserver/app/code/vendor/techdivision/appserver/src/TechDivision/ApplicationServer/Server.php","line":288,"class":"TechDivision\\\\ApplicationServer\\\\Server","function":"initProvisioners"}
            ',
        );
    }

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->handler = new RotatingMonologHandler(__DIR__ . self::TMP_DIR . self::TMP_FILE);
    }

    /**
     * Cleans the log files we might have created during our tests
     *
     * @return void
     */
    public function tearDown()
    {
        $globPattern = $this->handler->getGlobPattern();
        foreach (glob($globPattern) as $file) {

            unlink($file);
        }
    }

    /**
     * Test for the classes "getCurrentSizeIteration" method to count correctly
     *
     * @return void
     */
    public function testGetCurrentSizeIterationNotOver()
    {
        // get a new handler with a very low file size
        $this->handler = new RotatingMonologHandler(__DIR__ . self::TMP_DIR . self::TMP_FILE, 0, Logger::DEBUG, true,  null, 20);

        // write two times
        $record = $this->getRecordByDate(new \DateTime());
        for ($i = 0; $i < 2; $i++) {

            $this->handler->write($record);
        }

        $comingSizeIterator = (int) substr(strrchr($this->handler->getRotatedFilename(), "_"), 1, 1);
        $this->assertEquals(3, $comingSizeIterator);
    }

    /**
     * Test for the classes "rotate" method based on a record from the future
     *
     * @return void
     */
    public function testRotateByDate()
    {
        // get the glob pattern and check how the amount of files changes for each write
        $globPattern = $this->handler->getGlobPattern(date($this->handler->getDateFormat()));

        // write once for today
        $record = $this->getRecordByDate(new \DateTime());
        $this->handler->write($record);
        $this->assertEquals(0, count(glob($globPattern)));

        // write once for the future
        $record = $this->getRecordByDate(new \DateTime('Wednesday next week'));
        $this->handler->write($record);
        $rotatedFiles = glob($globPattern);
        $this->assertEquals(1, count($rotatedFiles));
        $this->assertTrue(file_exists(__DIR__ . self::TMP_DIR . self::TMP_FILE));

        // remove the current file and write again to check if we always write to the current file
        foreach ($rotatedFiles as $rotatedFile) {

            unlink($rotatedFile);
        }
        $record = $this->getRecordByDate(new \DateTime());
        $this->handler->write($record);
        $this->assertEquals(0, count(glob($globPattern)));
    }

    /**
     * Test for the classes "rotate" method based on a low maxFiles value
     *
     * @return void
     */
    public function testRotateByMaxFiles()
    {
        // get a new handler with a very low number of maximum files and a low maximal file size
        $this->handler = new RotatingMonologHandler(__DIR__ . self::TMP_DIR . self::TMP_FILE, 2, Logger::DEBUG, true,  null, 20);

        // get the glob pattern and check how the amount of files changes for each write
        $globPattern = $this->handler->getGlobPattern(date($this->handler->getDateFormat()));

        // write four times
        $record = $this->getRecordByDate(new \DateTime());
        for ($i = 0; $i < 4; $i++) {

            $this->handler->write($record);
        }
        $this->assertEquals(2, count(glob($globPattern)));

        // get a new handler with a very low number of maximum files and a low maximal file size
        $this->handler = new RotatingMonologHandler(__DIR__ . self::TMP_DIR . self::TMP_FILE, 1, Logger::DEBUG, true,  null, 20);

        // write three times
        for ($i = 0; $i < 3; $i++) {

            $this->handler->write($record);
        }
        $this->assertEquals(1, count(glob($globPattern)));
        $this->assertTrue(file_exists(__DIR__ . self::TMP_DIR . self::TMP_FILE));
    }

    /**
     * Test for the classes "setDateFormat" method
     *
     * @return void
     */
    public function testSetDateFormat()
    {
        $dummyFormat = 'I am not a format at all';
        $this->handler->setDateFormat($dummyFormat);
        $this->assertEquals($dummyFormat, $this->handler->getDateFormat());
    }

    /**
     * Test for the classes "setFilenameFormat" method
     *
     * @return void
     */
    public function testSetFilenameFormat()
    {
        $dummyFormat = 'I am not a format at all';
        $this->handler->setFilenameFormat($dummyFormat);
        $this->assertEquals($dummyFormat, $this->handler->getFilenameFormat());
    }

    /**
     * Test for the classes "write" method with a forced rotation based on the file size
     *
     * @return void
     */
    public function testSizeRotation()
    {
        // get a new handler with a very low file size
        $this->handler = new RotatingMonologHandler(__DIR__ . self::TMP_DIR . self::TMP_FILE, 0, Logger::DEBUG, true,  null, 20);

        // get the glob pattern and check how the amount of files changes for each write
        $globPattern = $this->handler->getGlobPattern(date($this->handler->getDateFormat()));
        $initialFileCount = count(glob($globPattern));

        // create a new record with current date
        $record = $this->getRecordByDate(new \DateTime());

        // write three times
        for ($i = 0; $i < 3; $i++) {

            $this->handler->write($record);
        }
        $this->assertEquals($initialFileCount + 3, count(glob($globPattern)));
    }

    /**
     * Test for the classes "write" method
     *
     * @return void
     */
    public function testWrite()
    {
        // create a new record with current date and write it
        $record = $this->getRecordByDate(new \DateTime());
        $this->handler->write($record);
        $this->assertTrue(file_exists(__DIR__ . self::TMP_DIR . self::TMP_FILE));
    }

    /**
     * Test for the classes "write" method
     *
     * @return void
     */
    public function testWriteWithoutRotating()
    {
        // get the glob pattern and check how the amount of files changes for each write
        $globPattern = $this->handler->getGlobPattern(date($this->handler->getDateFormat()));
        $initialFileCount = count(glob($globPattern));

        // create a new record with current date
        $record = $this->getRecordByDate(new \DateTime());

        // write three times
        for ($i = 0; $i < 3; $i++) {

            $this->handler->write($record);
        }
        $this->assertEquals($initialFileCount, count(glob($globPattern)));
        $this->assertTrue(file_exists(__DIR__ . self::TMP_DIR . self::TMP_FILE));
    }
}