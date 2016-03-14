<?php

/**
 * \AppserverIo\Appserver\MessageQueue\QueueManagerSettings
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

use AppserverIo\Appserver\Application\StandardManagerSettings;

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
class QueueManagerSettings extends StandardManagerSettings implements QueueManagerSettingsInterface
{

    /**
     * The default maximum number of jobs to process in parallel.
     *
     * @var string
     */
    const DEFAULT_MAXIMUM_JOBS_TO_PROCESS = 200;

    /**
     * The default base directory containing additional configuration information.
     *
     * @var string
     */
    const BASE_DIRECTORY = 'META-INF';

    /**
     * Initialize the default MQ settings.
     */
    public function __construct()
    {
        $this->setBaseDirectory(QueueManagerSettings::BASE_DIRECTORY);
        $this->setMaximumJobsToProcess(QueueManagerSettings::DEFAULT_MAXIMUM_JOBS_TO_PROCESS);
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
     * @return integer The maximum number of jobs
     */
    public function getMaximumJobsToProcess()
    {
        return $this->maximumJobsToProcess;
    }
}
