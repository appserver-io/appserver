<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\ParamNode
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

use AppserverIo\Lang\String;
use AppserverIo\Lang\Boolean;
use AppserverIo\Description\Api\Node\AbstractValueNode;
use AppserverIo\Configuration\Interfaces\ValueInterface;

/**
 * DTO to transfer a param.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ParamNode extends AbstractValueNode
{

    /**
     * The constant for the param type 'string';
     *
     * @var string
     */
    const TYPE_STRING = 'string';

    /**
     * The constant for the param type 'boolean';
     *
     * @var string
     */
    const TYPE_BOOLEAN = 'boolean';

    /**
     * The constant for the param type 'integer';
     *
     * @var string
     */
    const TYPE_INTEGER = 'integer';

    /**
     * The paramss name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The params data type.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

    /**
     * TRUE if the value is a constant.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $constant;

    /**
     * TRUE if the value is an environment variable.
     *
     * @var string
     * @AS\Mapping(nodeType="boolean")
     */
    protected $env;

    /**
     * Initializes the param node with the necessary data.
     *
     * @param string                                               $name      The params name
     * @param string                                               $type      The params data type
     * @param \AppserverIo\Configuration\Interfaces\ValueInterface $nodeValue The params initial value
     * @param boolean                                              $constant  TRUE if the value is a constant, else FALSE
     * @param boolean                                              $env       TRUE if the value is an environment variable, else FALSE
     */
    public function __construct($name = '', $type = '', ValueInterface $nodeValue = null, $constant = false, $env = false)
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
        $this->nodeValue = $nodeValue;
        $this->constant = $constant;
        $this->env = $env;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Description\Api\Node\AbstractNode::getPrimaryKey()
     */
    public function getPrimaryKey()
    {
        return $this->getName();
    }

    /**
     * Returns the param name.
     *
     * @return string The param name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the param type.
     *
     * @return string The param type
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns the TRUE if the value is a constant, else FALSE.
     *
     * @return boolean TRUE if the value is a constant, else FALSE
     */
    public function isConstant()
    {
        return $this->constant;
    }

    /**
     * Returns the TRUE if the value is an environment variable, else FALSE.
     *
     * @return boolean TRUE if the value is an environment variable, else FALSE
     */
    public function isEnv()
    {
        return $this->env;
    }

    /**
     * Casts the params value to the defined type and returns it.
     *
     * @return mixed The casted value
     */
    public function castToType()
    {

        // load the params value
        $value = $this->getNodeValue()->__toString();

        // query whether or not we've an environment variable and/or a constant
        $this->isEnv() ? $value = getenv($value) : $value;
        $this->isConstant() ? $value = constant($value) : $value;

        // query the parameters type
        switch ($type = $this->getType()) {
            case 'bool':
            case 'boolean':
                // bool + boolean needs custom handling
                $value = Boolean::valueOf(new String($value))->booleanValue();
                break;
            default:
                // all other can go the same way
                settype($value, $type);
        }

        // return the value
        return $value;
    }
}
