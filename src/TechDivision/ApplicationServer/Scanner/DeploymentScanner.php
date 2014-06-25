<?php
/**
 * TechDivision\ApplicationServer\Scanner\DeploymentScanner
 *
 * PHP version 5
 *
 * @category  Appserver
 * @package   TechDivision_ApplicationServer
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2013 TechDivision GmbH <info@techdivision.com>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Scanner;

use TechDivision\ApplicationServer\Interfaces\ExtractorInterface;

/**
 * This is a monitor that watches the deployment directory and restarts
 * the appserver by using the sbin/appserverctl script.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Scanner
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DeploymentScanner extends AbstractScanner
{

    /**
     * Returns the path to the deployment directory
     *
     * @return \SplFileInfo The deployment directory
     */
    public function getDirectory()
    {
        return new \SplFileInfo($this->getService()->getDeployDir());
    }

    /**
     * Initializes the scanner with the necessary service instance.
     *
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractThread::init()
     */
    public function init()
    {
        // Init the parent as well, as we have to get some mappings
        parent::init();
    }

    /**
     * Start's the deployment scanner that restarts the server
     * when a PHAR should be deployed or undeployed.
     *
     * @return void
     * @see \TechDivision\ApplicationServer\AbstractThread::main()
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

        // prepare the array with the file extensions of the files used to build the hash
        $extensionsToWatch = array('dodeploy', 'deployed');

        // load the initial hash value of the deployment directory
        $oldHash = $this->getDirectoryHash($directory, $extensionsToWatch);

        while (true) { // watch the deployment directory

            // load the actual hash value for the deployment directory
            $newHash = $this->getDirectoryHash($directory, $extensionsToWatch);

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
                $oldHash = $this->getDirectoryHash($directory, $extensionsToWatch);

                // log that the appserver has been restarted successfull
                $this->getSystemLogger()->debug('appserver has successfully been restarted');

            } else { // if no changes has been found, wait a second
                sleep(1);
            }
        }
    }

    /**
     * This method returns 0 to signal that the no
     * successful deployment has been processed so far, e. g. the server has
     * been installed and not been started yet.
     *
     * @param \SplFileInfo $file The deployment directory
     *
     * @return integer The UNIX timestamp with the last successfully deployment date or 0 if no successful
     *      deployment has been processed
     */
    public function getLastSuccessfullyDeployment(\SplFileInfo $file)
    {
        return $this->getLastFileTouch($file);
    }
}
