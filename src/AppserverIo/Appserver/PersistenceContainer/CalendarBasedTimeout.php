<?php
/**
 * AppserverIo\Appserver\PersistenceContainer\CalendarBasedTimeout
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

namespace AppserverIo\Appserver\PersistenceContainer;

use AppserverIo\Microcron\FieldFactory;
use AppserverIo\Microcron\CronExpression;
use AppserverIo\Psr\EnterpriseBeans\ScheduleExpression;

/**
 * A wrapper for a (micro-)cron expression implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class CalendarBasedTimeout extends CronExpression
{

    /**
     * The schedule expression with the data to create the instance with.
     *
     * @var \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression
     */
    protected $scheduleExpression;

    /**
     * Parse a Microcron (seconds digit + CRON ) expression
     *
     * @param string                                              $expression         Microcron expression (e.g. '8 * * * * *')
     * @param \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression $scheduleExpression The schedule expression with the data to create the instance with
     * @param \AppserverIo\Microcron\FieldFactory                 $fieldFactory       Factory to create cron fields
     */
    public function __construct($expression, ScheduleExpression $scheduleExpression, FieldFactory $fieldFactory = null)
    {
        // call parent constructor
        parent::__construct($expression, $fieldFactory);

        // set the schedule expression instance
        $this->scheduleExpression = $scheduleExpression;
    }

    /**
     * Additional factory method that creates a new instance from
     * the passed schedule expression.
     *
     * @param \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression $scheduleExpression The schedule expression with the data to create the instance with
     *
     * @return \AppserverIo\Appserver\PersistenceContainer\CalendarBasedTimeout The instance
     */
    public static function factoryFromScheduleExpression(ScheduleExpression $scheduleExpression)
    {

        // prepare the CRON expression
        $cronExpression = sprintf(
            '%s %s %s %s %s %s %s',
            $scheduleExpression->getSecond(),
            $scheduleExpression->getMinute(),
            $scheduleExpression->getHour(),
            $scheduleExpression->getDayOfMonth(),
            $scheduleExpression->getMonth(),
            $scheduleExpression->getDayOfWeek(),
            $scheduleExpression->getYear()
        );

        // return the point in time at which the next timer expiration is scheduled to occur
        return new CalendarBasedTimeout($cronExpression, $scheduleExpression, new FieldFactory());
    }

    /**
     * Returns the schedule expression the CRON has been initialized with.
     *
     * @return \AppserverIo\Psr\EnterpriseBeans\ScheduleExpression The schedule expression
     */
    public function getScheduleExpression()
    {
        return $this->scheduleExpression;
    }
}
