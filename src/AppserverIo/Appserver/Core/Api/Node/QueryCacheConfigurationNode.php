<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\QueryCacheConfigurationNode
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
use AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactories\CacheConfigurationNodeInterface;

/**
 * DTO to transfer a query cache configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class QueryCacheConfigurationNode extends AbstractNode implements CacheConfigurationNodeInterface
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The Doctrine query cache factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * Initialize the node with the passed factory class name.
     *
     * @param string $factory The factory class name to use
     */
    public function __construct($factory = 'AppserverIo\Appserver\PersistenceContainer\Doctrine\CacheFactories\ArrayCacheFactory')
    {
        $this->factory = $factory;
    }

    /**
     * Return's the Doctrine query cache factory class.
     *
     * @return string The Doctrine query cache factory class
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
