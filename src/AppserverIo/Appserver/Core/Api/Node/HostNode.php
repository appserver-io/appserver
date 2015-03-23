<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\HostNode
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

/**
 * DTO to transfer a host.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class HostNode extends AbstractNode
{

    /**
     * A context node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ContextsNodeTrait
     */
    use ContextsNodeTrait;

    /**
     * The host name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The applications base directory.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $appBase;

    /**
     * Returns the host name.
     *
     * @return string The host name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the applications base directory.
     *
     * @return string The applications base directory
     */
    public function getAppBase()
    {
        return $this->appBase;
    }
}
