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
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Scanner;

/**
 * AppserverIo\Appserver\Core\Scanner\HeartbeatScanner
 *
 * Scanner to check for proper functioning of the appserver
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
class HeartbeatScanner extends AbstractScanner
{
    /**
     * Name of the heartbeat file to watch
     *
     * @const string HEARTBEAT_FILE_NAME
     */
    const HEARTBEAT_FILE_NAME = '.appserver-last-heartbeat';

    /**
     * The interval for which heartbeat difference is acceptable in seconds
     *
     * @const int HEARTBEAT_INTERVAL
     */
    const HEARTBEAT_INTERVAL = 1;

    /**
     * Max time it might take to restart the appserver
     *
     * @const int RESTART_INTERVAL
     */
    const RESTART_INTERVAL = 5;

    /**
     * The file to monitor for a heartbeat
     *
     * @var \SplFileInfo $heartbeatFile
     */
    protected $heartbeatFile;

    /**
     * Initializes the scanner
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractThread::init()
     */
    public function init()
    {
        // Build up the complete path to the heartbeat file
        $this->heartbeatFile = APPSERVER_BP . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR .
            'run' . DIRECTORY_SEPARATOR . self::HEARTBEAT_FILE_NAME;

        // Init the parent as well, as we have to get some mappings
        parent::init();
    }

    /**
     * Getter for the heartbeat file path
     *
     * @return string
     */
    public function getHeartbeatFile()
    {
        return $this->heartbeatFile;
    }

    /**
     * The thread implementation main method which will be called from run in abstractness
     *
     * @return void
     */
    public function main()
    {

        // log the configured deployment directory
        $this->getSystemLogger()->debug(
            sprintf(
                "Start watching heartbeat file %s",
                $this->getHeartbeatFile()
            )
        );

        // wait until the server has been successfully started at least once
        while ($this->getLastFileTouch($this->getHeartbeatFile()) === 0) {
            $this->getSystemLogger()->debug('Heartbeat scanner is waiting for first successful startup ...');
            sleep(1);
        }

        while (true) { // watch the heartbeat file

            // load the current file change time of the heartbeat file and store it
            $oldMTime = $this->getLastFileTouch($this->getHeartbeatFile());

            // Get the current time
            $currentTime = time();

            // log the found directory hash value
            $this->getSystemLogger()->debug(
                sprintf(
                    "Comparing heartbeat mTimes %s (previous) : %s (actual)",
                    $oldMTime,
                    $currentTime
                )
            );

            // compare the mTime values, if they differ more than allowed we have to take action
            if (($currentTime - $oldMTime) > self::HEARTBEAT_INTERVAL) {

                // log that changes have been found
                $this->getSystemLogger()->debug(
                    sprintf(
                        "Found heartbeat missing for %s seconds.",
                        $currentTime - $oldMTime
                    )
                );

                // As long as the heartbeat does not come back up we will try to restart the appserver
                while ($oldMTime === $this->getLastFileTouch($this->getHeartbeatFile())) {

                    // Tell them we try to restart
                    $this->getSystemLogger()->debug("Will try to restart the appserver.");

                    // restart the appserver
                    $this->restart();

                    // wait until restart has been finished, but only wait for so long
                    for ($i = 0; $i <= self::RESTART_INTERVAL; $i ++) {

                        // Sleep a little
                        sleep(1);
                    }
                }

                // log that the appserver has been restarted successfully
                $this->getSystemLogger()->debug("appserver has successfully been restarted.");

            } else { // if no changes has been found, wait a second
                sleep(1);
            }
        }
    }

    /**
     * Returns an array, because we don't watch files here.
     *
     * @return array An empty array
     */
    protected function getExtensionsToWatch()
    {
        return array();
    }
}
