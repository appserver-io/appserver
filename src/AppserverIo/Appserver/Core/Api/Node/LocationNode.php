<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\LocationNode
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
 * DTO to transfer location information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Johann Zelger <jz@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LocationNode extends AbstractNode
{

    /**
     * The file handler node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\FileHandlersNodeTrait
     */
    use FileHandlersNodeTrait;

    /**
     * The params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The headers node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\HeadersNodeTrait
     */
    use HeadersNodeTrait;

    /**
     * The condition to match for.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $condition;

    /**
     * Returns the condition to match for.
     *
     * @return string The condition to match for
     */
    public function getCondition()
    {
        return $this->condition;
    }

    /**
     * Converts the location node into an associative array
     * and returns it.
     *
     * @return array The array with the location node data
     */
    public function toArray()
    {
        return array(
            'condition' => $this->getCondition(),
            'params'    => $this->getParamsAsArray(),
            'handlers'  => $this->getFileHandlersAsArray(),
            'headers'   => $this->getHeadersAsArray()
        );
    }
}
