<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\BootstrapNode
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

/**
 * DTO to transfer the application server's bootstrap configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class BootstrapNode extends AbstractNode implements BootstrapNodeInterface
{

    /**
     * A listeners node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ListenersNodeTrait
     */
    use ListenersNodeTrait;

    /**
     * The default runlevel.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $defaultRunlevel;

    /**
     * Returns the application server's default runlevel.
     *
     * @return array The application server's default runlevel
     */
    public function getDefaultRunlevel()
    {
        return $this->defaultRunlevel;
    }
}
