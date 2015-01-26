<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AnalyticNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * Node which represents a collection of analytic steps run on a certain URI.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AnalyticNode extends AbstractNode
{

    /**
     * A connectors node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ConnectionHandlersNodeTrait
     */
    use ConnectorsNodeTrait;

    /**
     * The URI to run analytics on
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $uri;

    /**
     * Returns the URI to run analytics on
     *
     * @return string The URI at which we have to run analytics
     */
    public function getUri()
    {
        return $this->uri;
    }
}
