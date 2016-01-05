<?php

/**
 * \AppserverIo\Appserver\Core\Api\Node\InitParamNode
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
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

/**
 * DTO to transfer a the initialization parameter information.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class InitParamNode extends AbstractNode implements InitParamNodeInterface
{

    /**
     * The parameter name information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamNameNode
     * @AS\Mapping(nodeName="param-name", nodeType="AppserverIo\Appserver\Core\Api\Node\ParamNameNode")
     */
    protected $paramName;

    /**
     * The parameter value information.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\ParamValueNode
     * @AS\Mapping(nodeName="param-value", nodeType="AppserverIo\Appserver\Core\Api\Node\ParamValueNode")
     */
    protected $paramValue;

    /**
     * Return's the parameter name information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ParamNameNode The parameter name information
     */
    public function getParamName()
    {
        return $this->paramName;
    }

    /**
     * Return's the parameter value information.
     *
     * @return \AppserverIo\Appserver\Core\Api\Node\ParamNameNode The parameter value information
     */
    public function getParamValue()
    {
        return $this->paramValue;
    }
}
