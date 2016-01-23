<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\JobNodeInterface
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\NodeInterface;

/**
 * Interface for a CRON job definition.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
interface JobNodeInterface extends NodeInterface
{

    /**
     * Returns the job name.
     *
     * @return string The job name
     */
    public function getName();

    /**
     * Returns the node containing schedule the job schedule.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ScheduleNode The node containing the job schedule
     */
    public function getSchedule();

    /**
     * Returns the node containing executable information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ExecuteNode The node containing executable information
     */
    public function getExecute();
}
