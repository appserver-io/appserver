<?php

/**
 * \AppserverIo\Appserver\Core\Api\ScannerService
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
namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\Node\CronNode;
use AppserverIo\Appserver\Core\Api\Node\ContextNode;
use AppserverIo\Appserver\Core\Api\Node\DeploymentNode;
use AppserverIo\Appserver\Core\Interfaces\ContainerInterface;

/**
 * A service that handles scanner configuration data.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ScannerService extends AbstractFileOperationService
{

    /**
     * Returns the node with the passed UUID.
     *
     * @param integer $uuid UUID of the node to return
     *
     * @return \AppserverIo\Configuration\Interfaces\NodeInterface The node with the UUID passed as parameter
     */
    public function load($uuid)
    {
        // not implemented yet
    }

    /**
     * Initializes the available CRON configurations and returns them.
     *
     * @return array The array with the available CRON configurations
     */
    public function findAll()
    {

        // initialize the array with the CRON instances
        $cronInstances = array();

        // we will need to test our CRON configuration files
        $configurationTester = new ConfigurationService($this->getInitialContext());
        $baseCronPath = $this->getConfdDir('cron.xml');

        // validate the base CRON file and load it as default if validation succeeds
        $cronInstance = new CronNode();
        if (! $configurationTester->validateFile($baseCronPath, null)) {
            $errorMessages = $configurationTester->getErrorMessages();
            $systemLogger = $this->getInitialContext()->getSystemLogger();
            $systemLogger->error(reset($errorMessages));
            $systemLogger->critical(sprintf('Problems validating base CRON file %s, this might affect app configurations badly.', $baseCronPath));
        } else {
            $cronInstance->initFromFile($baseCronPath);
        }

        // iterate over all jobs to configure the directory where they has to be executed
        /** @var \AppserverIo\Appserver\Core\Api\Node\JobNodeInterface $jobNode */
        foreach ($cronInstance->getJobs() as $job) {
            // load the execution information
            $execute = $job->getExecute();
            // query whether or not a base directory has been specified
            if ($execute && $execute->getDirectory() == null) {
                // set the directory where the cron.xml file located as base directory, if not
                $execute->setDirectory(dirname($baseCronPath));
            }
        }

        // add the default CRON configuration
        $cronInstances[] = $cronInstance;

        // iterate over all applications and create the CRON configuration
        foreach (glob($this->getWebappsDir() . '/*', GLOB_ONLYDIR) as $webappPath) {
            // iterate through all CRON configurations (cron.xml), validate and merge them
            foreach ($this->globDir($webappPath . '/META-INF/cron.xml') as $cronFile) {
                // validate the file, but skip it if validation fails
                if (! $configurationTester->validateFile($cronFile, null)) {
                    $errorMessages = $configurationTester->getErrorMessages();
                    $systemLogger = $this->getInitialContext()->getSystemLogger();
                    $systemLogger->error(reset($errorMessages));
                    $systemLogger->alert(sprintf('Will skip app specific context file %s, configuration might be faulty.', $cronFile));
                    continue;
                }

                // create a new CRON node instance
                $cronInstance = new CronNode();
                $cronInstance->initFromFile($cronFile);

                // iterate over all jobs to configure the directory where they has to be executed
                /** @var \AppserverIo\Appserver\Core\Api\Node\JobNodeInterface $jobNode */
                foreach ($cronInstance->getJobs() as $job) {
                    // load the execution information
                    $execute = $job->getExecute();
                    // query whether or not a base directory has been specified
                    if ($execute && $execute->getDirectory() == null) {
                        // set the directory where the cron.xml file located as base directory, if not
                        $execute->setDirectory($webappPath);
                    }
                }

                // merge it into the default configuration
                $cronInstances[] = $cronInstance;
            }
        }

        // return the array with the CRON instances
        return $cronInstances;
    }
}
