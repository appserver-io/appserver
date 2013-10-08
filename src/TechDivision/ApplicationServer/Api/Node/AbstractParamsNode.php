<?php

/**
 * TechDivision\ApplicationServer\Api\Node\AbstractParamsNode
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

/**
 * Abstract node that serves nodes having a params/param child.
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
abstract class AbstractParamsNode extends AbstractNode
{

    /**
     * The handler params to use.
     *
     * @var array<\TechDivision\ApplicationServer\Api\Node\ParamNode>
     * @AS\Mapping(nodeName="params/param", nodeType="array", elementType="TechDivision\ApplicationServer\Api\Node\ParamNode")
     */
    protected $params = array();

    /**
     * Array with the handler params to use.
     *
     * @return array<\TechDivision\ApplicationServer\Api\Node\ParamNode>
     */
    public function getParams()
    {
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