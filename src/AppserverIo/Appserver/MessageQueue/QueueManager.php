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

use AppserverIo\Properties\Properties;
use AppserverIo\Storage\GenericStackable;
use AppserverIo\Psr\Servlet\SessionUtils;
use AppserverIo\Psr\Pms\QueueContextInterface;
use AppserverIo\Psr\Pms\ResourceLocatorInterface;
use AppserverIo\Psr\Pms\QueueInterface;
use AppserverIo\Psr\Pms\MessageInterface;
use AppserverIo\Psr\Application\ApplicationInterface;
use AppserverIo\Appserver\Core\Environment;
use AppserverIo\Appserver\Core\AbstractManager;
use AppserverIo\Appserver\Core\Utilities\DirectoryKeys;
use AppserverIo\Appserver\Core\Utilities\EnvironmentKeys;
use AppserverIo\Appserver\Core\Utilities\SystemPropertyKeys;
use AppserverIo\Appserver\Core\Utilities\AppEnvironmentHelper;
use AppserverIo\Appserver\Core\Api\InvalidConfigurationException;
use AppserverIo\Appserver\Core\Api\Node\MessageQueuesNode;
use AppserverIo\Appserver\Core\Api\Node\MessageQueueNodeInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsAwareInterface;
use AppserverIo\Appserver\Application\Interfaces\ManagerSettingsInterface;
use AppserverIo\Appserver\PersistenceContainer\RemoteMethodInvocation\ProxyGeneratorInterface;

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
 * @property \AppserverIo\Psr\Application\ApplicationInterface                                          $application          The application to manage queues for
 * @property array                                                                                      $directories          Our directories
 * @property \AppserverIo\Psr\Pms\ResourceLocatorInterface                                              $resourceLocator      Locator for the requested queues
 * @property \AppserverIo\Storage\GenericStackable                                                      $queues               Queues to manage
 * @property \AppserverIo\Appserver\MessageQueue\QueueManagerSettingsInterface                          $managerSettings      The queue settings
 */
class QueueManager extends AbstractManager implements QueueContextInterface, ManagerSettingsAwareInterface
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
     * @param \AppserverIo\Appserver\MessageQueue\QueueManagerSettingsInterface $managerSettings The queue settings
     *
     * @return void
     */
    public function injectManagerSettings(ManagerSettingsInterface $managerSettings)
    {
        $this->managerSettings = $managerSettings;
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

        // add the application instance to the environment
        Environment::singleton()->setAttribute(EnvironmentKeys::APPLICATION, $application);

        // create s simulated request/session ID whereas session equals request ID
        Environment::singleton()->setAttribute(EnvironmentKeys::SESSION_ID, $sessionId = SessionUtils::generateRandomString());
        Environment::singleton()->setAttribute(EnvironmentKeys::REQUEST_ID, $sessionId);

        // register the message queues
        $this->registerMessageQueues($application);
    }

    /**
     * Deploys the message queues.
     *
     * @param \AppserverIo\Psr\Application\ApplicationInterface|\AppserverIo\Psr\Naming\NamingDirectoryInterface $application The application instance
     *
     * @return void
     */
    public function registerMessageQueues(ApplicationInterface $application)
    {

        // initialize the array for the creating the subdirectories
        $this->directories = new GenericStackable();
        $this->directories[] = $application->getNamingDirectory();

        // check META-INF + subdirectories for XML files with MQ definitions
        /** @var \AppserverIo\Appserver\Core\Api\DeploymentService $service */
        $service = $application->newService('AppserverIo\Appserver\Core\Api\DeploymentService');
        $xmlFiles = $service->globDir(AppEnvironmentHelper::getEnvironmentAwareGlobPattern($this->getWebappPath(), 'META-INF' . DIRECTORY_SEPARATOR . 'message-queues'));

        // load the configuration service instance
        /** @var \AppserverIo\Appserver\Core\Api\ConfigurationService $configurationService */
        $configurationService = $application->newService('AppserverIo\Appserver\Core\Api\ConfigurationService');

        // load the container node to initialize the system properties
        /** @var \AppserverIo\Psr\ApplicationServer\Configuration\ContainerConfigurationInterface $containerNode */
        $containerNode = $application->getContainer()->getContainerNode();

        // gather all the deployed web applications
        foreach ($xmlFiles as $file) {
            try {
                // validate the file here, but skip if the validation fails
                $configurationService->validateFile($file, null, true);

                // load the system properties
                $properties = $service->getSystemProperties($containerNode);

                // append the application specific properties
                $properties->add(SystemPropertyKeys::WEBAPP, $webappPath = $application->getWebappPath());
                $properties->add(SystemPropertyKeys::WEBAPP_NAME, basename($webappPath));
                $properties->add(SystemPropertyKeys::WEBAPP_DATA, $application->getDataDir());
                $properties->add(SystemPropertyKeys::WEBAPP_CACHE, $application->getCacheDir());
                $properties->add(SystemPropertyKeys::WEBAPP_SESSION, $application->getSessionDir());

                // create a new message queue node instance and replace the properties
                $messageQueuesNode = new MessageQueuesNode();
                $messageQueuesNode->initFromFile($file);
                $messageQueuesNode->replaceProperties($properties);

                // register the entity managers found in the configuration
                foreach ($messageQueuesNode->getMessageQueues() as $messageQueueNode) {
                    $this->registeMessageQueue($messageQueueNode);
                }

            } catch (InvalidConfigurationException $e) {
                // try to load the system logger instance
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger()) {
                    $systemLogger->error($e->getMessage());
                    $systemLogger->critical(sprintf('Persistence configuration file %s is invalid, needed queues might be missing.', $file));
                }

            } catch (\Exception $e) {
                // try to load the system logger instance
                /** @var \Psr\Log\LoggerInterface $systemLogger */
                if ($systemLogger = $this->getApplication()->getInitialContext()->getSystemLogger()) {
                    $systemLogger->error($e->__toString());
                }
            }
        }
    }

    /**
     * Deploys the message queue described by the passed node.
     *
     * @param \AppserverIo\Appserver\Core\Api\Node\MessageQueueNodeInterface $messageQueueNode The node that describes the message queue
     *
     * @return void
     */
    public function registeMessageQueue(MessageQueueNodeInterface $messageQueueNode)
    {

        // load destination queue and receiver type
        $type = $messageQueueNode->getType();
        $destination = $messageQueueNode->getDestination()->__toString();

        // initialize the message queue
        $messageQueue = new MessageQueue();
        $messageQueue->injectType($type);
        $messageQueue->injectName($destination);
        $messageQueue->injectWorkers($this->workers);
        $messageQueue->injectMessages($this->messages);
        $messageQueue->injectApplication($this->application);
        $messageQueue->injectManagerSettings($this->managerSettings);
        $messageQueue->start();

        // initialize the queues storage for the priorities
        $this->queues[$messageQueue->getName()] = $messageQueue;

        // bind the callback for creating a new MQ sender instance to the naming directory => necessary for DI provider
        $this->getApplication()->getNamingDirectory()->bindCallback(sprintf('php:global/%s/%s', $this->getApplication()->getUniqueName(), $destination), array(&$this, 'createSenderForQueue'), array($destination));
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
     * @return \AppserverIo\Appserver\MessageQueue\QueueManagerSettingsInterface The queue settings
     */
    public function getManagerSettings()
    {
        return $this->managerSettings;
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
        $application = $this->getApplication();
        $applicationName = $application->getName();
        $webappPath = $application->getWebappPath();

        // initialize the variable for the properties
        $properties = null;

        // load the configuration base directory
        if ($baseDirectory = $this->getManagerSettings()->getBaseDirectory()) {
            // look for naming context properties in the manager's base directory
            $propertiesFile = DirectoryKeys::realpath(
                sprintf('%s/%s/%s', $webappPath, $baseDirectory, QueueManagerSettingsInterface::CONFIGURATION_FILE)
            );

            // load the properties from the configuration file
            if (file_exists($propertiesFile)) {
                $properties = Properties::create()->load($propertiesFile);
            }
        }

        // initialize and return the sender
        $queue = \AppserverIo\Messaging\MessageQueue::createQueue($lookupName);
        $connection = \AppserverIo\Messaging\QueueConnectionFactory::createQueueConnection($applicationName, $properties);
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
        \info(sprintf('Update message monitor for message: %s', spl_object_hash($message)));
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
