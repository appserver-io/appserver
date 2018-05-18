<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ConsoleNode
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
use AppserverIo\Description\Api\Node\ParamsNodeTrait;
use AppserverIo\Psr\Cli\Configuration\ConsoleConfigurationInterface;

/**
 * DTO to transfer a console configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ConsoleNode extends AbstractNode implements ConsoleConfigurationInterface
{

    /**
     * A params node trait.
     *
     * @var \AppserverIo\Description\Api\Node\ParamsNodeTrait
     */
    use ParamsNodeTrait;

    /**
     * The console's name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The console's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The console's factory class name
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * Returns the console's name
     *
     * @return string The console's name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the console's class name.
     *
     * @return string The console's class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the console's factory class name.
     *
     * @return string The console's factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
