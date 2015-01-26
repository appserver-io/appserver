<?php
/**
 * AppserverIo\Appserver\PersistenceContainer\Utils\TimerState
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

namespace AppserverIo\Appserver\PersistenceContainer\Utils;

/**
 * Utility class with some bean utilities.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class TimerState
{

    /**
     * State indicEnumg that a timer has been created.
     *
     * @var integer
     */
    const CREATED = 1;

    /**
     * State indicating that the timer is active and will receive.
     * any timeout notifications
     *
     * @var integer
     */
    const ACTIVE = 2;

    /**
     * State indicating that the timer has been cancelled and will not
     * receive any future timeout notifications.
     *
     * @var integer
     */
    const CANCELED = 3;

    /**
     * State indicating that there aren't any scheduled timeouts for this timer.
     *
     * @var integer
     */
    const EXPIRED = 4;

    /**
     * State indicating that the timer has received a timeout notification
     * and is processing the timeout task.
     *
     * @var integer
     */
    const IN_TIMEOUT = 5;

    /**
     * State indicating that the timeout task has to be retried.
     *
     * @var integer
     */
    const RETRY_TIMEOUT = 6;
}
