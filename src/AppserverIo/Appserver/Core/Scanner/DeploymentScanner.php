<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\DeploymentScanner
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

use AppserverIo\Appserver\Core\Interfaces\ExtractorInterface;

/**
 * This is a scanner that watches a flat directory for files that changed
 * and restarts the appserver by using the OS specific start/stop script.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeploymentScanner extends AbstractScanner
{

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
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext    The initial context instance
     * @param string                                                         $directory         The directory we want to scan
     * @param integer                                                        $interval          The interval in seconds we want scan the directory
     * @param string                                                         $extensionsToWatch The comma separated list with extensions of files we want to watch
     */
    public function __construct($initialContext, $directory, $interval = 1, $extensionsToWatch = '')
    {

        // call parent constructor
        parent::__construct($initialContext);

        // initialize the members
        $this->interval = $interval;
        $this->directory = $directory;

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
     * Returns the file information of the deployment flag
     *
     * @return \SplFileInfo The deployment flag file information
     */
    public function getDeploymentFlag()
    {
        return new \SplFileInfo($this->getService()->getDeployDir() . DIRECTORY_SEPARATOR . ExtractorInterface::FILE_DEPLOYMENT_SUCCESSFULL);
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
     * Start's the deployment scanner that restarts the server
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

        // log the configured deployment directory
        $this->getSystemLogger()->debug(sprintf('Start watching directory %s', $directory));

        // load the deployment flag
        $deploymentFlag = $this->getDeploymentFlag();

        // wait until the server has been successfully started at least once
        while ($this->getLastSuccessfullyDeployment($deploymentFlag) === 0) {
            $this->getSystemLogger()->debug(sprintf('%s is waiting for first successful deployment ...', __CLASS__));
            sleep($interval);
        }

        // load the initial hash value of the deployment directory
        $oldHash = $this->getDirectoryHash($directory);

        // watch the deployment directory
        while (true) {
            // load the actual hash value for the deployment directory
            $newHash = $this->getDirectoryHash($directory);

            // log the found directory hash value
            $this->getSystemLogger()->debug(sprintf("Comparing directory hash %s (previous) : %s (actual)", $oldHash, $newHash));

            // compare the hash values, if not equal restart the appserver
            if ($oldHash !== $newHash) {
                // log that changes have been found
                $this->getSystemLogger()->debug(sprintf('Found changes in directory %s', $directory));

                // log the UNIX timestamp of the last successful deployment
                $lastSuccessfullDeployment = $this->getLastSuccessfullyDeployment($deploymentFlag);

                // restart the appserver
                $this->restart();

                // wait until deployment has been finished
                while ($lastSuccessfullDeployment == $this->getLastSuccessfullyDeployment($deploymentFlag)) {
                    sleep($interval);
                }

                // set the directory new hash value after successful deployment
                $oldHash = $this->getDirectoryHash($directory);

                // log that the appserver has been restarted successful
                $this->getSystemLogger()->debug('appserver has successfully been restarted');

            } else {
                // if no changes has been found, wait a second
                sleep($interval);
            }
        }
    }

    /**
     * This method returns 0 to signal that the no successful deployment has been processed
     * so far, e. g. the server has been installed and not been started yet.
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
