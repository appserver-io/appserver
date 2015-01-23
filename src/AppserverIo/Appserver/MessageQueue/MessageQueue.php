<?php

/**
 * AppserverIo\Appserver\MessageQueue\MessageQueue
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
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\MessageQueue;

use AppserverIo\Psr\Pms\Queue;

/**
 * Class Queue
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class MessageQueue implements Queue
{

    /**
     * The queue name to use.
     *
     * @var string
     */
    protected $name = null;

    /**
     * The message bean type to handle the messages.
     *
     * @var string
     */
    protected $type = null;


    /**
     * Initializes the queue with the name to use.
     *
     * @param string $name Holds the queue name to use
     * @param string $type The message bean type to handle the messages
     */
    protected function __construct($name, $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * Returns the queue name.
     *
     * @return string The queue name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the message bean type to handle the messages.
     *
     * @return string The message bean type to handle the messages
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Initializes and returns a new Queue instance.
     *
     * @param string $name Holds the queue name to use
     * @param string $type The message bean type to handle the messages
     *
     * @return \AppserverIo\Appserver\MessageQueue\MessageQueue The instance
     */
    public static function createQueue($name, $type)
    {
        return new MessageQueue($name, $type);
    }
}
