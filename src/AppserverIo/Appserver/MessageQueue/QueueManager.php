<?php

/**
 * \AppserverIo\Appserver\MessageQueue\QueueManager
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
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Pms\QueueContextInterface;
use AppserverIo\Psr\Pms\ResourceLocatorInterface;
use AppserverIo\Psr\Pms\QueueInterface;
use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Psr\Naming\NamingException;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Api\InvalidConfigurationException;

/**
 * The queue manager handles the queues and message beans registered for the application.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Markus Stockbauer <ms@techdivision.com>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 *
 * @property \AppserverIo\Psr\Application\ApplicationInterface          $application     The application to manage queues for
 * @property array                                                      $directories     Our directories
 * @property \AppserverIo\Psr\Pms\ResourceLocatorInterface              $resourceLocator Locator for the requested queues
 * @property \AppserverIo\Storage\GenericStackable                      $queues          Queues to manage
 * @property \AppserverIo\Appserver\MessageQueue\QueueSettingsInterface $queueSettings   The queue settings
 */
class QueueManager extends AbstractManager implements QueueContextInterface
{

    /**
     * Injects the storage for the queues.
     *
     * @param \AppserverIo\Storage\GenericStackable $queues An storage for the queues
     *
     * @return void
     */
    public function injectQueues(GenericStackable $queues)
    {
        $this->queues = $queues;
    }

    /**
     * Injects the storage for the messages.
     *
     * @param \AppserverIo\Storage\GenericStackable $messages An storage for the messages
     *
     * @return void
     */
    public function injectMessages(GenericStackable $messages)
    {
        $this->messages = $messages;
    }

    /**
     * Injects the storage for the workers.
     *
     * @param \AppserverIo\Storage\GenericStackable $workers An storage for the workers
     *
     * @return void
     */
    public function injectWorkers(GenericStackable $workers)
    {
        $this->workers = $workers;
    }

    /**
     * Injects the resource locator that locates the requested queue.
     *
     * @param \AppserverIo\Psr\Pms\ResourceLocatorInterface $resourceLocator The resource locator
     *
     * @return void
     */
    public function injectResourceLocator(ResourceLocatorInterface $resourceLocator)
    {
        $this->resourceLocator = $resourceLocator;
    }

    /**
     * Injects the queue settings.
     *
     * @param \AppserverIo\Appserver\MessageQueue\QueueSettingsInterface $queueSettings The queue settings
     *
     * @return void
     */
    public function injectQueueSettings(QueueSettingsInterface $queueSettings)
    {
        $this->queueSettings = $queueSettings;
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
        $this->registerMessageQueues($application);
    }

    /**
     * Deploys the message queues.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface $application The application instance
     *
     * @return void
     */
    protected function registerMessageQueues(ApplicationInterface $application)
    {

        // build up META-INF directory var
        $metaInfDir = $this->getWebappPath() . DIRECTORY_SEPARATOR .'META-INF';

        // check if we've found a valid directory
        if (is_dir($metaInfDir) === false) {
            return;
        }

        // check META-INF + subdirectories for XML files with MQ definitions
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
        $service = $application->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $xmlFiles = $service->globDir($metaInfDir . DIRECTORY_SEPARATOR . 'message-queues.xml');

        // initialize the array for the creating the subdirectories
        $this->directories = new GenericStackable();
        $this->directories[] = $application->getNamingDirectory();

        // gather all the deployed web applications
        foreach ($xmlFiles as $file) {
            try {
                // try to initialize a SimpleXMLElement
                $sxe = new \SimpleXMLElement($file, null, true);
                $sxe->registerXPathNamespace('a', 'http://www.appserver.io/appserver');

                // lookup the MessageQueue's defined in the passed XML node
                if (($nodes = $sxe->xpath('/a:message-queues/a:message-queue')) === false) {
                    continue;
                }

                // validate the file here, if it is not valid we can skip further steps
                try {
                    /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
                    $configurationService = $application->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');
                    $configurationService->validateFile($file, null, true);

                } catch (InvalidConfigurationException $e) {
                    /** @var \Psr\Log\LoggerInterface $systemLogger */
                    $systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger();
                    $systemLogger->error($e->getMessage());
                    $systemLogger->critical(sprintf('Message queue configuration file %s is invalid, needed queues might be missing.', $file));
                    return;
                }

                // iterate over all found queues and initialize them
                foreach ($nodes as $node) {
                    $this->registeMessageQueue($node);
                }

            // if class can not be reflected continue with next class
            } catch (\Exception $e) {
                // log an error message
                $application->getInitialContext()->getSystemLogger()->error($e->__toString());
                // proceed with the next queue
                continue;
            }
        }
    }

    /**
     * Deploys the message queue described by the passed XML node.
     *
     * @param \SimpleXMLElement $node The XML node that describes the message queue
     *
     * @return void
     */
    protected function registeMessageQueue(\SimpleXMLElement $node)
    {

        // load the nodes attributes
        $attributes = $node->attributes();

        // load destination queue and receiver type
        $destination = (string) $node->destination;
        $type = (string) $attributes['type'];

        // initialize the message queue
        $messageQueue = new MessageQueue();
        $messageQueue->injectType($type);
        $messageQueue->injectName($destination);
        $messageQueue->injectWorkers($this->workers);
        $messageQueue->injectMessages($this->messages);
        $messageQueue->injectApplication($this->application);
        $messageQueue->injectQueueSettings($this->queueSettings);
        $messageQueue->start();

        // initialize the queues storage for the priorities
        $this->queues[$messageQueue->getName()] = $messageQueue;

        // prepare the naming directory to bind the callback to
        $path = explode('/', $destination);
        for ($i = 0; $i < sizeof($path) - 1; $i++) {
            try {
                $this->directories[$i]->search(sprintf('php:global/%s/%s', $this->getApplication()->getName(), $path[$i]));
            } catch (NamingException $ne) {
                $this->directories[$i + 1] = $this->directories[$i]->createSubdirectory(sprintf('php:global/%s/%s', $this->getApplication()->getName(), $path[$i]));
            }
        }

        // bind the callback for creating a new MQ sender instance to the naming directory => necessary for DI provider
        $this->getApplication()->getNamingDirectory()->bindCallback(sprintf('php:global/%s/%s', $this->getApplication()->getName(), $destination), array(&$this, 'createSenderForQueue'), array($destination));
    }

    /**
     * Returns the array with queue names and the MessageListener class
     * names as values.
     *
     * @return array The registered queues
     */
    public function getQueues()
    {
        return $this->queues;
    }

    /**
     * Return the resource locator instance.
     *
     * @return \AppserverIo\Psr\Pms\ResourceLocatorInterface The resource locator instance
     */
    public function getResourceLocator()
    {
        return $this->resourceLocator;
    }

    /**
     * Return's the queue settings.
     *
     * @return \AppserverIo\Appserver\MessageQueue\QueueSettingsInterface The queue settings
     */
    public function getQueueSettings()
    {
        return $this->queueSettings;
    }

    /**
     * Returns TRUE if the application is related with the
     * passed queue instance.
     *
     * @param \AppserverIo\Psr\Pms\QueueInterface $queue The queue the application has to be related to
     *
     * @return boolean TRUE if the application is related, else FALSE
     */
    public function hasQueue(QueueInterface $queue)
    {
        return array_key_exists($queue->getName(), $this->getQueues());
    }

    /**
     * Tries to locate the queue that handles the request and returns the instance
     * if one can be found.
     *
     * @param \AppserverIo\Psr\Pms\QueueInterface $queue The queue request
     *
     * @return \AppserverIo\Psr\Pms\QueueInterface The requested queue instance
     */
    public function locate(QueueInterface $queue)
    {
        return $this->getResourceLocator()->locate($this, $queue);
    }

    /**
     * Runs a lookup for the message queue with the passed lookup name and
     * session ID.
     *
     * @param string $lookupName The queue lookup name
     * @param string $sessionId  The session ID
     * @param array  $args       The arguments passed to the queue
     *
     * @return \AppserverIo\Psr\Pms\QueueInterface The requested queue instance
     * @todo Still to implement
     */
    public function lookup($lookupName, $sessionId = null, array $args = array())
    {
        return $this->getResourceLocator()->lookup($this, $lookupName, $sessionId, $args);
    }

    /**
     * Return a new sender for the message queue with the passed lookup name.
     *
     * @param string $lookupName The lookup name of the queue to return a sender for
     * @param string $sessionId  The session-ID to be passed to the queue session
     *
     * @return \AppserverIo\Messaging\QueueSender The sender instance
     */
    public function createSenderForQueue($lookupName, $sessionId = null)
    {

        // load the application name
        $applicationName = $this->getApplication()->getName();

        // initialize and return the sender
        $queue = \AppserverIo\Messaging\MessageQueue::createQueue($lookupName);
        $connection = \AppserverIo\Messaging\QueueConnectionFactory::createQueueConnection($applicationName);
        $session = $connection->createQueueSession();
        return $session->createSender($queue);
    }

    /**
     * Updates the message monitor.
     *
     * @param \AppserverIo\Psr\Pms\MessageInterface $message The message to update the monitor for
     *
     * @return void
     */
    public function updateMonitor(MessageInterface $message)
    {
        $this->getApplication()->getInitialContext()->getSystemLogger()->info(
            sprintf('Update message monitor for message: %s', spl_object_hash($message))
        );
    }

    /**
     * Initializes the manager instance.
     *
     * @return string
     * @see \AppserverIo\Psr\Application\ManagerInterface::initialize()
     */
    public function getIdentifier()
    {
        return QueueContextInterface::IDENTIFIER;
    }

    /**
     * Shutdown the session manager instance.
     *
     * @return void
     * \AppserverIo\Psr\Application\ManagerInterface::stop()
     */
    public function stop()
    {

        // load the queue keys
        $queues = get_object_vars($this->getQueues());

        // iterate over the queues and shut them down
        /** @var AppserverIo\Psr\Pms\QueueInterface $queue */
        foreach ($queues as $queue) {
            $queue->stop();
        }
    }
}
