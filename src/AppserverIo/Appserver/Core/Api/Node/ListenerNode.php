<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ListenerNode
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer a listener configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ListenerNode extends AbstractNode implements ListenerNodeInterface
{

    /**
     * The event the listner is bound to.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $event;

    /**
     * The listener's class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The listener's priority.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $priority;

    /**
     * Return's the event the listener is bound to.
     *
     * @return string The unique class loader name
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Return's the listener type.
     *
     * @return string The listener type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Return's the listener priority.
     *
     * @return string The listener priority
     */
    public function getPriority()
    {
        return $this->priority;
    }
}
