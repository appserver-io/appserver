<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\UpstreamNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;

/**
 * DTO to transfer upstream information.
 *
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class UpstreamNode extends AbstractNode
{
    /**
     * The trait for the servers.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\UpstreamServersNodeTrait
     */
    use UpstreamServersNodeTrait;

    /**
     * The servers type.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The servers name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * Returns the servers type.
     *
     * @return string The servers type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the server name.
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
}
