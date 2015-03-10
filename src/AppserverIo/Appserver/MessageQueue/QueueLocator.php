<?php

/**
 * \AppserverIo\Appserver\MessageQueue\QueueLocator
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Psr\Pms\QueueInterface;
use AppserverIo\Psr\Pms\QueueContextInterface;
use AppserverIo\Psr\Pms\ResourceLocatorInterface;

/**
 * The queue resource locator implementation.
 *
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class QueueLocator implements ResourceLocatorInterface
{

    /**
     * Tries to locate the queue that handles the request and returns the instance
     * if one can be found.
     *
     * @param \AppserverIo\Psr\Pms\QueueContextInterface $queueManager The queue manager instance
     * @param \AppserverIo\Psr\Pms\QueueInterface        $queue        The queue request
     *
     * @return \AppserverIo\Psr\Pms\QueueInterface The requested queue instance
     * @see \AppserverIo\Appserver\MessageQueue\ResourceLocator::locate()
     */
    public function locate(QueueContextInterface $queueManager, QueueInterface $queue)
    {

        // load registered queues and requested queue name
        $queues = $queueManager->getQueues();

        // return the listener of requested queue if available
        if (array_key_exists($queueName = $queue->getName(), $queues)) {
            return $queues[$queueName];
        }
    }
}
