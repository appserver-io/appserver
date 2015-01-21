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
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * AppserverIo\Appserver\Core\Api\Node\ParamsNodeTrait
 *
 * Abstract node that serves nodes having a params/param child.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH - <info@techdivision.com>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.techdivision.com/
 */
trait ParamsNodeTrait
{

    /**
     * The handler params to use.
     *
     * @var array
     * @AS\Mapping(nodeName="params/param", nodeType="array", elementType="AppserverIo\Appserver\Core\Api\Node\ParamNode")
     */
    protected $params = array();

    /**
     * Array with the handler params to use.
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Array with the handler params to use.
     *
     * @param array $params The handler params
     *
     * @return void
     */
    public function setParams(array $params)
    {
        $this->params = $params;
    }

    /**
     * Sets the param with the passed name, type and value.
     *
     * @param string $name  The param name
     * @param string $type  The param type
     * @param mixed  $value The param value
     *
     * @return void
     */
    public function setParam($name, $type, $value)
    {

        // initialize the param to set
        $paramToSet = new ParamNode($name, $type, new NodeValue($value));

        // query whether a param with this name has already been set
        foreach ($this->params as $key => $param) {
            if ($param->getName() === $paramToSet->getName()) {
                // override the param
                $this->params[$key] = $paramToSet;
                return;
            }
        }

        // append the param
        $this->params[] = $paramToSet;
    }

    /**
     * Returns the param with the passed name casted to
     * the specified type.
     *
     * @param string $name The name of the param to be returned
     *
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
        if (is_array($this->getParams())) {
            foreach ($this->getParams() as $param) {
                $params[$param->getName()] = $param->castToType();
            }
        }
        return $params;
    }
}
