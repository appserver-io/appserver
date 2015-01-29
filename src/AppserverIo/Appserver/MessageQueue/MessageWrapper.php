<?php

/**
 * AppserverIo\Appserver\MessageQueue\MessageWrapper
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
use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Psr\Pms\MonitorInterface;
use AppserverIo\Psr\Pms\StateKeyInterface;
use AppserverIo\Psr\Pms\PriorityKeyInterface;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Messaging\Utils\PriorityKeys;
use AppserverIo\Messaging\Utils\PriorityLow;
use AppserverIo\Messaging\Utils\StateKeys;
use AppserverIo\Messaging\Utils\StateActive;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * This is a simple stackable wrapper for a message.
 *
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class MessageWrapper extends GenericStackable implements MessageInterface
{

    /**
     * Initializes the instance.
     */
    public function __construct()
    {

        // initialize the members
        $this->messageId = null;
        $this->message = null;
        $this->sessionId = '';
        $this->destination = null;
        $this->parentMessage = null;
        $this->messageMonitor = null;
        $this->priority = PriorityLow::KEY;
        $this->state = StateActive::KEY;
    }

    /**
     * Creates a new and empty wrapper instance.
     *
     * @return \AppserverIo\Psr\Pms\MessageInterface The empty message wrapper instance
     */
    public static function emptyInstance()
    {
        return new MessageWrapper();
    }

    /**
     * Initializes the wrapper with the real message
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message we want to wrap
     *
     * @return void
     */
    public function init(MessageInterface $message)
    {
        $this->messageId = $message->getMessageId();
        $this->message = $message->getMessage();
        $this->sessionId = $message->getSessionId();
        $this->messageMonitor = $message->getMessageMonitor();
        $this->priority = $message->getPriority()->getPriority();
        $this->state = $message->getState()->getState();
        $this->destination = $message->getDestination();
        $this->sessionId = $message->getSessionId();
    }

    /**
     * Initializes and returns a new job instance.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return \AppserverIo\Appserver\MessageQueue\Job The job instance
     */
    public function getJob(ApplicationInterface $application)
    {
        return new Job($this, $application);
    }

    /**
     * Returns the message id.
     *
     * @return string The message id as hash value
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * The message itself.
     *
     * @return array The message itself
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Returns the message as string.
     *
     * @return string The message as string
     */
    public function __toString()
    {
        return serialize($this->message);
    }

    /**
     * Sets the unique session id.
     *
     * @param string $sessionId The uniquid id
     *
     * @return void
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
    }

    /**
     * Returns the unique session id.
     *
     * @return string The uniquid id
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * Sets the destination Queue.
     *
     * @param \AppserverIo\Psr\Pms\QueueInterface $destination The destination
     *
     * @return void
     */
    public function setDestination(QueueInterface $destination)
    {
        $this->destination = $destination;
    }

    /**
     * Returns the destination Queue.
     *
     * @return \AppserverIo\Psr\Pms\QueueInterface The destination Queue
     */
    public function getDestination()
    {
        return $this->destination;
    }

    /**
     * Sets the priority of the message.
     *
     * @param \AppserverIo\Psr\Pms\PriorityKeyInterface $priority The priority to set the message to
     *
     * @return void
     */
    public function setPriority(PriorityKeyInterface $priority)
    {
        $this->priority = $priority->getPriority();
    }

    /**
     * Returns the priority of the message.
     *
     * @return \AppserverIo\Psr\Pms\PriorityKeyInterface The priority of the message
     */
    public function getPriority()
    {
        return PriorityKeys::get($this->priority);
    }

    /**
     * Sets the state of the message.
     *
     * @param \AppserverIo\Psr\Pms\StateKeyInterface $state The new state
     *
     * @return void
     */
    public function setState(StateKeyInterface $state)
    {
        $this->state = $state->getState();
    }

    /**
     * Returns the state of the message.
     *
     * @return \AppserverIo\Psr\Pms\StateKeyInterface The message state
     */
    public function getState()
    {
        return StateKeys::get($this->state);
    }

    /**
     * Sets the parent message.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $parentMessage The parent message
     *
     * @return void
     */
    public function setParentMessage(MessageInterface $parentMessage)
    {
        $this->parentMessage = $parentMessage;
    }

    /**
     * Returns the parent message.
     *
     * @return \AppserverIo\Psr\Pms\MessageInterface The parent message
     * @see \AppserverIo\Psr\Pms\MessageInterface::getParentMessage()
     */
    public function getParentMessage()
    {
        return $this->parentMessage;
    }

    /**
     * Sets the monitor for monitoring the message itself.
     *
     * @param \AppserverIo\Psr\Pms\MonitorInterface $messageMonitor The monitor
     *
     * @return void
     */
    public function setMessageMonitor(MonitorInterface $messageMonitor)
    {
        $this->messageMonitor = $messageMonitor;
    }

    /**
     * Returns the message monitor.
     *
     * @return \AppserverIo\Psr\Pms\MonitorInterface The monitor
     * @see \AppserverIo\Appserver\Pms\Message::getMessageMonitor()
     */
    public function getMessageMonitor()
    {
        return $this->messageMonitor;
    }
}
