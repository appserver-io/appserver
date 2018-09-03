<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ObjectDescriptionNode
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
 * DTO to transfer the object description information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ObjectDescriptionNode extends AbstractNode implements DescriptorsAwareConfigurationInterface, ParsersAwareConfigurationInterface
{

    /**
     * A descriptors node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\DescriptorsNodeTrait
     */
    use DescriptorsNodeTrait;

    /**
     * A parsers node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParsersNodeTrait
     */
    use ParsersNodeTrait;
}
