<?php

/**
 * \AppserverIo\Appserver\MessageQueue\DefaultQueueSettings
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

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Storage\GenericStackable;

/**
 * Default MQ configuration settings.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property integer $maximumJobsToProcess The maximum number of jobs to process in prallel
 */
class DefaultQueueSettings extends GenericStackable implements QueueSettingsInterface
{

    /**
     * The default maximum number of jobs to process in parallel.
     *
     * @var string
     */
    const DEFAULT_MAXIMUM_JOBS_TO_PROCESS = 200;

    /**
     * Initialize the default MQ settings.
     */
    public function __construct()
    {
        // initialize the default values
        $this->setMaximumJobsToProcess(DefaultQueueSettings::DEFAULT_MAXIMUM_JOBS_TO_PROCESS);
    }

    /**
     * Set's the maximum number of jobs to process in prallel.
     *
     * @param integer $maximumJobsToProcess The maximum number of jobs
     *
     * @return void
     */
    public function setMaximumJobsToProcess($maximumJobsToProcess)
    {
        $this->maximumJobsToProcess = $maximumJobsToProcess;
    }

    /**
     * Return's the maximum number of jobs to process in prallel.
     *
     * @return integer The the maximum number of jobs
     */
    public function getMaximumJobsToProcess()
    {
        return $this->maximumJobsToProcess;
    }

    /**
     * Merge the passed params with the default settings.
     *
     * @param array $params The associative array with the params to merge
     *
     * @return void
     */
    public function mergeWithParams(array $params)
    {
        // merge the passed properties with the default settings for the stateful session beans
        foreach (array_keys(get_object_vars($this)) as $propertyName) {
            if (array_key_exists($propertyName, $params)) {
                $this->$propertyName = $params[$propertyName];
            }
        }
    }
}
