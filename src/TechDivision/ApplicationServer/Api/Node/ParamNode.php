<?php
/**
 * TechDivision\ApplicationServer\Api\Node\ParamNode
 *
 * PHP version 5
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace TechDivision\ApplicationServer\Api\Node;

use TechDivision\Configuration\Interfaces\ValueInterface;

/**
 * DTO to transfer a param.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
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
     * @param string                                                $name      The params name
     * @param string                                                $type      The params data type
     * @param \TechDivision\Configuration\Interfaces\ValueInterface $nodeValue The params initial value
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
     * @see \TechDivision\ApplicationServer\Api\Node\AbstractNode::getPrimaryKey()
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
