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
     * The params's name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $name;

    /**
     * The param's class name.
     *
     * @var string
     * @AS\Mapping(nodeType="string")
     */
    protected $type;

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
     * Cast's the param's value to the defined type and returns it.
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
