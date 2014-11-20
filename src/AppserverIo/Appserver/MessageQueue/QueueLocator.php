<?php

/**
 * AppserverIo\Appserver\MessageQueue\QueueLocator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use TechDivision\MessageQueueProtocol\Queue;
use AppserverIo\Appserver\MessageQueue\QueueManager;

/**
 * The queue resource locator implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class QueueLocator implements ResourceLocator
{

    /**
     * Tries to locate the queue that handles the request and returns the instance
     * if one can be found.
     *
     * @param \AppserverIo\Appserver\MessageQueue\QueueManager  $queueManager The queue manager instance
     * @param \TechDivision\MessageQueueProtocol\Queue $queue        The queue request
     *
     * @return \TechDivision\MessageQueueProtocol\Queue The requested queue instance
     * @see \AppserverIo\Appserver\MessageQueue\ResourceLocator::locate()
     */
    public function locate(QueueManager $queueManager, Queue $queue)
    {

        // load registered queues and requested queue name
        $queues = $queueManager->getQueues();

        // return Receiver of requested queue if available
        if (array_key_exists($queueName = $queue->getName(), $queues)) {
            return $queues[$queueName];
        }
    }
}
