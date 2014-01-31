<?php
/**
 * TechDivision\ApplicationServer\Api\Node\AbstractParamsNode
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
 * Abstract node that serves nodes having a params/param child.
 *
 * @category   Appserver
 * @package    TechDivision_ApplicationServer
 * @subpackage Api
 * @author     Tim Wagner <tw@techdivision.com>
 * @copyright  2013 TechDivision GmbH <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
abstract class AbstractParamsNode extends AbstractNode
{

    /**
     * The handler params to use.
     *
     * @var array
     * @AS\Mapping(nodeName="params/param", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ParamNode")
     */
    protected $params = array();

    /**
     * Array with the handler params to use.
     *
     * @return array
     */
    public function getParams(){
        return $this->params;
    }

    /**
     * Returns the param with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the param to be returned
     * @return mixed The requested param casted to the specified type
     */
    public function getParam($name)
    {
        $params = $this->getParamsAsArray();
        if (array_key_exists($name, $params)) {
            return $params[$name];
        }
    }

    /**
     * Returns the params casted to the defined type
     * as associative array.
     *
     * @return array The array with the casted params
     */
    public function getParamsAsArray()
    {
        $params = array();
        foreach ($this->getParams() as $param) {
            $params[$param->getName()] = $param->castToType();
        }
        return $params;
    }
}
