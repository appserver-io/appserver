<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * AppserverIo\Appserver\Core\Api\Node\AnalyticNode
 *
 * Node which represents a collection of analytic steps run on a certain uri
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Core
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io/
 */
class AnalyticNode extends AbstractNode
{
    // We use traits which give us the possibility to have collections of the child nodes mentioned in the
    // corresponding trait name
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
