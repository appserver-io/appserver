<?php
/**
 * AppserverIo\Appserver\Core\Api\Node\ParamNode
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Configuration\Interfaces\ValueInterface;

/**
 * DTO to transfer a param.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class ParamNode extends AbstractValueNode
{

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
     * Initializes the param node with the necessary data.
     *
     * @param string                                               $name      The params name
     * @param string                                               $type      The params data type
     * @param \AppserverIo\Configuration\Interfaces\ValueInterface $nodeValue The params initial value
     */
    public function __construct($name = '', $type = '', ValueInterface $nodeValue = null)
    {

        // initialize the UUID
        $this->setUuid($this->newUuid());

        // set the data
        $this->name = $name;
        $this->type = $type;
        $this->nodeValue = $nodeValue;
    }

    /**
     * Returns the nodes primary key, the name by default.
     *
     * @return string The nodes primary key
     * @see \AppserverIo\Appserver\Core\Api\Node\AbstractNode::getPrimaryKey()
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
     * Casts the params value to the defined type and returns it.
     *
     * @return mixed The casted value
     */
    public function castToType()
    {
        $value = $this->getNodeValue()->__toString();
        settype($value, $this->getType());
        return $value;
    }
}
