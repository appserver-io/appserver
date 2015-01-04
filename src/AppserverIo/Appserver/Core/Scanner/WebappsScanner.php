<?php
/**
 * AppserverIo\Appserver\Core\Scanner\DeploymentScanner
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

/**
 * This is a monitor that watches the deployment directory and restarts
 * the appserver by using the sbin/appserverctl script.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class WebappsScanner extends DeploymentScanner
{

    /**
     * Returns the path to the deployment directory
     *
     * @return \SplFileInfo The deployment directory
     */
    public function getDirectory()
    {
        return new \SplFileInfo($this->getService()->getWebappsDir());
    }

    /**
     * Returns an array with file extensions that should be
     * watched for new deployments.
     *
     * @return array The array with the file extensions
     */
    protected function getExtensionsToWatch()
    {
        return array('php');
    }

    /**
     * Start's the deployment scanner that restarts the server
     * when a PHAR should be deployed or undeployed.
     *
     * @return void
     * @see \AppserverIo\Appserver\Core\AbstractThread::main()
     */
    public function main()
    {

        // load the deployment directory
        $directory = $this->getDirectory();

        // log the configured deployment directory
        $this->getSystemLogger()->debug(sprintf('Start watching deployment directory %s', $directory));

        // open the deployment flag
        $deploymentFlag = new \SplFileInfo($directory . DIRECTORY_SEPARATOR . ExtractorInterface::FILE_DEPLOYMENT_SUCCESSFULL);

        // wait until the server has been successfully started at least once
        while ($this->getLastSuccessfullyDeployment($deploymentFlag) === 0) {
            $this->getSystemLogger()->debug('Deplyoment scanner is waiting for first successful deployment ...');
            sleep(1);
        }

        // load the initial hash value of the deployment directory
        $oldHash = $this->getDirectoryHash($directory);

        while (true) { // watch the deployment directory

            // load the actual hash value for the deployment directory
            $newHash = $this->getDirectoryHash($directory);

            // log the found directory hash value
            $this->getSystemLogger()->debug(sprintf("Comparing directory hash %s (previous) : %s (actual)", $oldHash, $newHash));

            // compare the hash values, if not equal restart the appserver
            if ($oldHash !== $newHash) {

                // log that changes have been found
                $this->getSystemLogger()->debug(sprintf('Found changes in deployment directory %s', $directory));

                // log the UNIX timestamp of the last successfull deployment
                $lastSuccessfullDeployment = $this->getLastSuccessfullyDeployment($deploymentFlag);

                // restart the appserver
                $this->restart();

                // wait until deployment has been finished
                while ($lastSuccessfullDeployment == $this->getLastSuccessfullyDeployment($deploymentFlag)) {
                    sleep(1);
                }

                // set the directory new hash value after successfull deployment
                $oldHash = $this->getDirectoryHash($directory);

                // log that the appserver has been restarted successfull
                $this->getSystemLogger()->debug('appserver has successfully been restarted');

            } else { // if no changes has been found, wait a second
                sleep(1);
            }
        }
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
