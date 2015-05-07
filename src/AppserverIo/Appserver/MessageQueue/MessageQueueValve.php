<?php

/**
 * \AppserverIo\Appserver\MessageQueue\MessageQueueValve
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

use AppserverIo\Messaging\Utils\StateActive;
use AppserverIo\Messaging\MessageQueueProtocol;
use AppserverIo\Appserver\ServletEngine\ValveInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface;
use AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface;

/**
 * Valve implementation that will be executed by the servlet engine to handle
 * an incoming HTTP message request.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageQueueValve implements ValveInterface
{

    /**
     * Processes the request by invoking the request handler that attaches the message to the
     * requested queue in a protected context.
     *
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletRequestInterface  $servletRequest  The request instance
     * @param \AppserverIo\Psr\Servlet\Http\HttpServletResponseInterface $servletResponse The response instance
     *
     * @return void
     * @throws \Exception Is thrown if the requested message queue is not available
     */
    public function invoke(HttpServletRequestInterface $servletRequest, HttpServletResponseInterface $servletResponse)
    {

        // load the application context
        /** @var \AppserverIo\Appserver\Application\Application $application */
        $application = $servletRequest->getContext();

        // unpack the message
        $message = MessageQueueProtocol::unpack($servletRequest->getBodyContent());

        // load message queue name and priority key
        $queueName = $message->getDestination()->getName();
        $priorityKey = $message->getPriority();

        // lookup the message queue manager and attach the message
        $queueManager = $application->search('QueueContextInterface');
        if ($messageQueue = $queueManager->lookup($queueName)) {
            $messageQueue->attach($message);
        } else {
            throw new \Exception("Can\'t find queue for message queue $queueName");
        }

        // finally dispatch this request, because we have finished processing it
        $servletRequest->setDispatched(true);
    }
}
