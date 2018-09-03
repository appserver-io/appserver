<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ParserNode
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

use AppserverIo\Description\Annotations as DI;
use AppserverIo\Description\Api\Node\AbstractNode;
use AppserverIo\Description\Api\Node\DirectoriesNodeTrait;

/**
 * DTO to transfer the object description parser configuration.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ParserNode extends AbstractNode implements ParserNodeInterface
{

    /**
     * A directories node trait.
     *
     * @var \AppserverIo\Description\Api\Node\DirectoriesNodeTrait
     */
    use DirectoriesNodeTrait;

    /**
     * The unique parser name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The parser class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * The parsers factory class name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $factory;

    /**
     * The parsers descriptor name.
     *
     * @var string
     * @DI\Mapping(nodeType="string")
     */
    protected $descriptorName;

    /**
     * Returns the parser name.
     *
     * @return string The unique parser name
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

    /**
     * Returns the descriptor name.
     *
     * @return string The descriptor name
     */
    public function getDescriptorName()
    {
        return $this->descriptorName;
    }
}
