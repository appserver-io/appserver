<?php

/**
 * \AppserverIo\Appserver\Core\Scanner\CronScanner
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

use AppserverIo\Microcron\CronExpression;
use AppserverIo\Appserver\Core\Api\Node\CronNode;
use AppserverIo\Appserver\Core\Api\Node\JobNodeInterface;

/**
 * This is a simple CRON scanner implementation configured by one or more
 * XML configuration files.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CronScanner extends AbstractScanner
{

    /**
     * The interval in seconds we use execute the configured jobs.
     *
     * @var integer
     */
    protected $interval;

    /**
     * The configuration file to use.
     *
     * @var array
     */
    protected $configurationFile;

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @param \AppserverIo\Appserver\Application\Interfaces\ContextInterface $initialContext    The initial context instance
     * @param string                                                         $configurationFile The configuration file we want to use
     * @param integer                                                        $interval          The interval in seconds we want to execute the configured jobs
     */
    public function __construct($initialContext, $configurationFile, $interval = 1)
    {

        // call parent constructor
        parent::__construct($initialContext);

        // initialize the members
        $this->interval = $interval;
        $this->configurationFile = $configurationFile;
    }

    /**
     * Returns the interval to execute the CRON jobs in seconds.
     *
     * @return integer The interval
     */
    public function getInterval()
    {
        return $this->interval;
    }

    /**
     * Returns the configuration file.
     *
     * @return \SplFileInfo The configuration file
     */
    public function getConfigurationFile()
    {
        return $this->getService()->getBaseDirectory($this->configurationFile);
    }

    /**
     * Returns an array with file extensions that should be
     * watched for new deployments.
     *
     * @return array The array with the file extensions
     */
    protected function getExtensionsToWatch()
    {
        return array();
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

        // load the configuration file with the jobs
        $configurationFile = $this->getConfigurationFile();

        // log the configured deployment directory
        $this->getSystemLogger()->info(sprintf('Start CRON with configuration file %s', $this->getConfigurationFile()));

        // load the configured CRON jobs
        $cronNode = new CronNode();
        $cronNode->initFromFile($configurationFile);

        // execute all the registered CRON jobs
        while (true) {
            // execute each of the jobs found in the configuration file
            /** @var \AppserverIo\Appserver\Core\Api\Node\JobNodeInterface $jobNode */
            foreach ($cronNode->getJobs() as $jobNode) {
				// load the scheduled expression from the job definition
            	$schedule = $jobNode->getSchedule()->getNodeValue()->__toString();

				// query whether the job has to be scheduled or not
            	if (CronExpression::factory($schedule)->isDue()) {
                	$this->getCronJob($jobNode);
            	}
            }

            // sleep for the configured interval
            sleep($this->getInterval());
        }
    }

    /**
     * Creates and returns a new CRON job (thread) for the passed
     * job information.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\JobNodeInterface $jobNode The job information
     *
     * @return \AppserverIo\Appserver\Core\Scanner\CronJob The CRON job
     */
    public function getCronJob(JobNodeInterface $jobNode)
    {
        return new CronJob($jobNode);
    }
}
