<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\ManagerNode
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

use AppserverIo\Appserver\Application\Interfaces\ManagerConfigurationInterface;

/**
 * DTO to transfer a manager.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ManagerNode extends AbstractNode implements ManagerConfigurationInterface
{
    /**
     * The params trait.
     *
     * @var \Trait
     */
    use ParamsNodeTrait;

    /**
     * A directories node trait.
     *
     * @var \TraitInterface
     */
    use DirectoriesNodeTrait;

    /**
     * A descriptors node trait.
     *
     * @var \TraitInterface
     */
    use DescriptorsNodeTrait;

    /**
     * The unique manager name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The manager class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The managers factory class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * Initializes the manager configuration with the passed values.
     *
     * @param string $name    The unique manager name
     * @param string $type    The manager class name
     * @param string $factory The managers factory class name
     */
    public function __construct($name = '', $type = '', $factory = '')
    {
        $this->name = $name;
        $this->type = $type;
        $this->factory = $factory;
    }

    /**
     * Returns the application name.
     *
     * @return string The unique application name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the class name.
     *
     * @return string The class name
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the factory class name.
     *
     * @return string The factory class name
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
