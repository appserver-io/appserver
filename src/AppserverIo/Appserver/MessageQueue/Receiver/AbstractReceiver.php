<?php
/**
 * AppserverIo\Appserver\MessageQueue\Receiver\AbstractReceiver
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
 * @author     Markus Stockbauer <ms@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue\Receiver;

use TechDivision\MessageQueueProtocol\Message;
use TechDivision\MessageQueueProtocol\Receiver;
use TechDivision\MessageQueueProtocol\QueueContext;
use AppserverIo\Psr\Application\ApplicationInterface;

/**
 * The abstract superclass for all receivers.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Markus Stockbauer <ms@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
abstract class AbstractReceiver implements Receiver
{

    /**
     * The application instance that provides the entity manager.
     *
     * @var \AppserverIo\Appserver\Core\Interfaces\ApplicationInterface
     */
    protected $application;

    /**
     * Initializes the session bean with the Application instance.
     *
     * Checks on every start if the database already exists, if not
     * the database will be created immediately.
     *
     * @param \AppserverIo\Appserver\Core\Interfaces\ApplicationInterface $application The application instance
     */
    public function __construct(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * Returns the application instance.
     *
     * @return \AppserverIo\Appserver\Core\Interfaces\ApplicationInterface The application instance
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Updates the message monitor over the applications queue manager method.
     *
     * @param \TechDivision\MessageQueueProtocol\Message $message The message to update the monitor for
     *
     * @return void
     * @throws \Exception Is thrown if no queue manager is registered in the application
     */
    protected function updateMonitor(Message $message)
    {

        // check if a application instance is available
        $queueManager = $this->getApplication()->search('QueueContext');
        if ($queueManager == null) {
            throw new \Exception(sprintf('Can\'t find queue manager instance in application %s', $this->getApplication()->getName()));
        }

        // update the monitor
        $queueManager->updateMonitor($message);
    }
}
