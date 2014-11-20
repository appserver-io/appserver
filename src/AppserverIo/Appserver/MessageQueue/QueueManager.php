<?php

/**
 * AppserverIo\Appserver\MessageQueue\QueueManager
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

use TechDivision\Storage\GenericStackable;
use TechDivision\MessageQueueProtocol\Queue;
use TechDivision\MessageQueueProtocol\Message;
use TechDivision\MessageQueueProtocol\QueueContext;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The queue manager handles the queues and message beans registered for the application.
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
class QueueManager extends GenericStackable implements QueueContext
{

    /**
     * Initializes the queue manager.
     *
     * @return void
     */
    public function __construct()
    {
        $this->webappPath = '';
    }

    /**
     * Injects the storage for the queues.
     *
     * @param \TechDivision\Storage\GenericStackable $queues An storage for the queues
     *
     * @return void
     */
    public function injectQueues(GenericStackable $queues)
    {
        $this->queues = $queues;
    }

    /**
     * Injects the absolute path to the web application.
     *
     * @param string $webappPath The absolute path to this web application
     *
     * @return void
     */
    public function injectWebappPath($webappPath)
    {
        $this->webappPath = $webappPath;
    }

    /**
     * Injects the resource locator that locates the requested queue.
     *
     * @param \AppserverIo\Appserver\MessageQueue\ResourceLocator $resourceLocator The resource locator
     *
     * @return void
     */
    public function injectResourceLocator(ResourceLocator $resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * Has been automatically invoked by the container after the application
     * instance has been created.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface $application The application instance
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function initialize(ApplicationInterface $application)
    {
        $this->registerMessageQueues();
    }

    /**
     * Deploys the message queues.
     *
     * @return void
     */
    protected function registerMessageQueues()
    {

        if (is_dir($basePath = $this->getWebappPath() . DIRECTORY_SEPARATOR . 'META-INF')) {

            $iterator = new \FilesystemIterator($basePath);

            // gather all the deployed web applications
            foreach (new \RegexIterator($iterator, '/^.*\.xml$/') as $file) {

                // check if file or sub directory has been found
                if ($file->isDir() === false) {

                    // try to initialize a SimpleXMLElement
                    $sxe = new \SimpleXMLElement($file, null, true);

                    // lookup the MessageQueue's defined in the passed XML node
                    if (($nodes = $sxe->xpath("/message-queues/message-queue")) === false) {
                        continue;
                    }

                    // iterate over all found queues and initialize them
                    foreach ($nodes as $node) {

                        // load the nodes attributes
                        $attributes = $node->attributes();

                        // create a new queue instance
                        $instance = MessageQueue::createQueue((string)$node->destination, (string)$attributes['type']);

                        // register destination and receiver type
                        $this->queues[$instance->getName()] = $instance;
                    }
                }
            }
        }
    }

    /**
     * Returns the array with queue names and the MessageReceiver class
     * names as values.
     *
     * @return array The registered queues
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * Returns the absolute path to the web application.
     *
     * @return string The absolute path
     */
    public function getWebappPath()
    {
        return $this->webappPath;
    }

    /**
     * Return the resource locator instance.
     *
     * @return \AppserverIo\Appserver\MessageQueue\ResourceLocator The resource locator instance
     */
    public function getResourceLocator()
    {
        return $this->resourceLocator;
    }

    /**
     * Returns TRUE if the application is related with the
     * passed queue instance.
     *
     * @param \TechDivision\MessageQueueProtocol\Queue $queue The queue the application has to be related to
     *
     * @return boolean TRUE if the application is related, else FALSE
     */
    public function hasQueue(Queue $queue)
    {
        return array_key_exists($queue->getName(), $this->getQueues());
    }

    /**
     * Tries to locate the queue that handles the request and returns the instance
     * if one can be found.
     *
     * @param \TechDivision\MessageQueueProtocol\Queue $queue The queue request
     *
     * @return \TechDivision\MessageQueueProtocol\Queue The requested queue instance
     */
    public function locate(Queue $queue)
    {
        return $this->getResourceLocator()->locate($this, $queue);
    }

    /**
     * Updates the message monitor.
     *
     * @param \TechDivision\MessageQueueProtocol\Message $message The message to update the monitor for
     *
     * @return void
     */
    public function updateMonitor(Message $message)
    {
        error_log('Update message monitor for message: ' . spl_object_hash($message));
    }

    /**
     * Initializes the manager instance.
     *
     * @return void
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return QueueContext::IDENTIFIER;
    }

    /**
     * Returns the value with the passed name from the context.
     *
     * @param string $key The key of the value to return from the context.
     *
     * @return mixed The requested attribute
     */
    public function getAttribute($key)
    {
        throw new \Exception(sprintf('%s is not implemented yes', __METHOD__));
    }
}
