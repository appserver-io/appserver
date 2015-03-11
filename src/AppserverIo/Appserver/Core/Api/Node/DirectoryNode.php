<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\DirectoryNode
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

use AppserverIo\Configuration\Interfaces\ValueInterface;

/**
 * DTO to transfer the directory information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DirectoryNode extends AbstractValueNode implements DirectoryNodeInterface
{

    /**
     * The flag to enforce design-by-contract type checking on classes of this directory.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $enforced;

    /**
     * Initializes the directory node with the necessary data.
     *
     * @param \AppserverIo\Configuration\Interfaces\ValueInterface|null $nodeValue The node value
     * @param string                                                    $enforced  The enforcement flag
     */
    public function __construct(ValueInterface $nodeValue = null, $enforced = false)
    {
        $this->nodeValue = $nodeValue;
        $this->enforced = $enforced;
    }

    /**
     * Returns the enforcement flag.
     *
     * @return boolean The enforcement flag
     */
    public function isEnforced()
    {
        return $this->enforced;
    }
}
