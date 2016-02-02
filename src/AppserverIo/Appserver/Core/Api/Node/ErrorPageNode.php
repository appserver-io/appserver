<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ErrorPageNode
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

use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Description\Api\Node\ValueNode;
use AppserverIo\Configuration\Interfaces\NodeValueInterface;

/**
 * DTO to transfer the error page configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ErrorPageNode extends AbstractNode implements ErrorPageNodeInterface
{

    /**
     * The HTTP response code pattern the error page is defined for.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ValueNode
     * @AS\Mapping(nodeName="error-code-pattern", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $errorCodePattern;

    /**
     * The location to redirect to.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ValueNode
     * @AS\Mapping(nodeName="error-location", nodeType="AppserverIo\Description\Api\Node\ValueNode")
     */
    protected $errorLocation;

    /**
     * Initializes the error page node with the passed values.
     *
     * @param \AppserverIo\Configuration\Interfaces\NodeValueInterface $errorCodePattern The error code pattern
     * @param \AppserverIo\Configuration\Interfaces\NodeValueInterface $errorLocation    The error location
     */
    public function __construct(
        NodeValueInterface $errorCodePattern = null,
        NodeValueInterface $errorLocation = null
    ) {
        $this->errorCodePattern = $errorCodePattern;
        $this->errorLocation = $errorLocation;
    }

    /**
     * Return's the HTTP response code pattern the error page is defined for.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ValueNode The HTTP response code pattern
     */
    public function getErrorCodePattern()
    {
        return $this->errorCodePattern;
    }

    /**
     * Return's the location to redirect to.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ValueNode The location
     */
    public function getErrorLocation()
    {
        return $this->errorLocation;
    }
}
