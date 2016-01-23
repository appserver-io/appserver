<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\CronNodeInterface
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
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a CRON node implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface CronNodeInterface extends NodeInterface
{

    /**
     * Array with the jobs to set.
     *
     * @param array $jobs The jobs to set
     *
     * @return void
     */
    public function setJobs($jobs);

    /**
     * Array with the jobs.
     *
     * @return array
     */
    public function getJobs();

    /**
     * Returns the job with the passed name.
     *
     * @param string $name The name of the job to be returned
     *
     * @return mixed The requested job
     */
    public function getJob($name);

    /**
     * This method merges the passed CRON node with this one
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\CronNodeInterface $cronNode The node to merge
     *
     * @return void
     */
    public function merge(CronNode $cronNode);
}
