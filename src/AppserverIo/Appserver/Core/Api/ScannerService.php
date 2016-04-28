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

use AppserverIo\Appserver\Core\Api\Node\CronNode;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Configuration\ConfigurationException;
use AppserverIo\Properties\Properties;
use AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys;

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
        try {
            // initialize the array with the CRON instances
            $cronInstances = array();

            // load the service necessary to validate CRON configuration files
            /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
            $configurationService = $this->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');

            // load the base CRON configuration file
            $baseCronPath = $this->getConfdDir('cron.xml');

            // we will need to test our CRON configuration files
            $configurationService->validateFile($baseCronPath, null);

            // validate the base CRON file and load it as default if validation succeeds
            $cronInstance = new CronNode();
            $cronInstance->initFromFile($baseCronPath);

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

            // iterate over the configured containers
            /** @var \AppserverIo\Appserver\Core\Api\Node\ContainerNodeInterface $containerNode */
            foreach ($this->getSystemConfiguration()->getContainers() as $containerNode) {
                // iterate over all applications and create the CRON configuration
                foreach (glob($this->getWebappsDir($containerNode) . '/*', GLOB_ONLYDIR) as $webappPath) {
                    // iterate through all CRON configurations (cron.xml), validate and merge them
                    foreach ($this->globDir(AppEnvironmentHelper::getEnvironmentAwareGlobPattern($webappPath, 'META-INF/cron')) as $cronFile) {
                        try {
                            // validate the file, but skip it if validation fails
                            $configurationService->validateFile($cronFile, null);

                            // load the system properties
                            $properties = $this->getSystemProperties($containerNode);

                            // append the application specific properties
                            $properties->add(SystemPropertyKeys::WEBAPP, $webappPath);
                            $properties->add(SystemPropertyKeys::WEBAPP_NAME, basename($webappPath));

                            // create a new CRON node instance and replace the properties
                            $cronInstance = new CronNode();
                            $cronInstance->initFromFile($cronFile);
                            $cronInstance->replaceProperties($properties);

                            // append it to the other CRON configurations
                            $cronInstances[] = $cronInstance;

                        } catch (ConfigurationException $ce) {
                            // load the logger and log the XML validation errors
                            $systemLogger = $this->getInitialContext()->getSystemLogger();
                            $systemLogger->error($ce->__toString());

                            // additionally log a message that CRON configuration will be missing
                            $systemLogger->critical(
                                sprintf('Will skip app specific CRON configuration %s, configuration might be faulty.', $cronFile)
                            );
                        }
                    }
                }
            }

        } catch (ConfigurationException $ce) {
            // load the logger and log the XML validation errors
            $systemLogger = $this->getInitialContext()->getSystemLogger();
            $systemLogger->error($ce->__toString());

            // additionally log a message that DS will be missing
            $systemLogger->critical(
                sprintf('Problems validating base CRON file %s, this might affect app configurations badly.', $baseCronPath)
            );
        }

        // return the array with the CRON instances
        return $cronInstances;
    }

    /**
     * Resolves the passed path. If the passed path is NULL, the webapp path is
     * used, if the passed path is relative, the webapp path is prepended.
     *
     * @param string $webappPath The absolute path to the webapp
     * @param string $path       The path to be resolved
     *
     * @return string The resolved path
     */
    protected function resolvePath($webappPath, $path = null)
    {

        // query whether or not a base directory has been specified
        if ($path == null) {
            // set the directory where the cron.xml file located as base directory
            $path = $webappPath;

        // query whether or not we found an absolute path or not
        } elseif ($path != null) {
            if (strpos($path, '/') > 0) {
                $path = sprintf('%s/%s', $webappPath, $path);
            }
        }

        // return the resolved path
        return $path;
    }
}
