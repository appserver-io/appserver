<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\EnterpriseBeansNode
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
 * DTO to transfer enterprise beans information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class EnterpriseBeansNode extends AbstractNode implements EnterpriseBeansNodeInterface
{

    /**
     * The session beans information.
     *
     * @var array
     * @DI\Mapping(nodeName="session", nodeType="array", elementType="AppserverIo\Description\Api\Node\SessionNode")
     */
    protected $sessions = array();

    /**
     * The message driven beans information.
     *
     * @var array
     * @DI\Mapping(nodeName="message-driven", nodeType="array", elementType="AppserverIo\Description\Api\Node\MessageDrivenNode")
     */
    protected $messageDrivens = array();

    /**
     * Return's the session beans information.
     *
     * @return array The session beans information
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Return's the message driven beans information.
     *
     * @return array The message driven beans information
     */
    public function getMessageDrivens()
    {
        return $this->messageDrivens;
    }
}
