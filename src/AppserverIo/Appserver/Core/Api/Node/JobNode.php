<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\JobNode
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

/**
 * DTO to transfer a applications provision configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class JobNode extends AbstractNode implements JobNodeInterface
{

    /**
     * The job name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The node containing the schedule information for the job.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ScheduleNode
     * @AS\Mapping(nodeName="schedule", nodeType="AppserverIo\Appserver\Core\Api\Node\ScheduleNode")
     */
    protected $schedule;

    /**
     * The node containing the information to execute something like a script.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ExecuteNode
     * @AS\Mapping(nodeName="execute", nodeType="AppserverIo\Appserver\Core\Api\Node\ExecuteNode")
     */
    protected $execute;

    /**
     * Returns the job name.
     *
     * @return string The job name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the node containing schedule the job schedule.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ScheduleNode The node containing the job schedule
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Returns the node containing executable information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ExecuteNode The node containing executable information
     */
    public function getExecute()
    {
        return $this->execute;
    }
}
