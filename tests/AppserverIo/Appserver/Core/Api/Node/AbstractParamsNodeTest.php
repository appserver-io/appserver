<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AbstractParamsNodeTest
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

use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\AbstractTest;
use AppserverIo\Appserver\Core\Api\Node\Mock\MockAbstractParamsNode;

/**
 * Test for the abstract params node implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AbstractParamsNodeTest extends AbstractTest
{

    /**
     * The abstract node instance to test.
     *
     * @var AppserverIo\Appserver\Core\Api\Node\Mock\MockAbstractParamsNode
     */
    protected $abstractParamsNode;

    /**
     * Initializes the abstract instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->abstractParamsNode = new MockAbstractParamsNode($this->getParamNodes());
    }

    /**
     * Test that the param with the requested name has the correct integer type.
     *
     * @return void
     */
    public function testGetParamWithIntegerValue()
    {
        $paramValue = $this->abstractParamsNode->getParam('workerNumber');
        $this->assertSame(16, $paramValue);
    }

    /**
     * Test that the param with the requested name has the correct integer type.
     *
     * @return void
     */
    public function testGetParamWithStringValue()
    {
        $paramValue = $this->abstractParamsNode->getParam('address');
        $this->assertSame('0.0.0.0', $paramValue);
    }

    /**
     * Test that calling the getParam() method with a name that is not available returns NULL.
     *
     * @return void
     */
    public function testGetParamWithInvalidName()
    {
        $this->assertNull($this->abstractParamsNode->getParam('someInvalidName'));
    }

    /**
     * Test that the correct number of params will be returned.
     *
     * @return void
     */
    public function testGetParams()
    {
        $params = $this->abstractParamsNode->getParams();
        $this->assertCount(3, $params);
    }

    /**
     * Test that the array of params has the correct key => value pairs.
     *
     * @return void
     */
    public function testGetParamsAsArray()
    {
        $paramsToCompare = array(
        	'workerNumber' => 16,
            'address' => '0.0.0.0',
            'port' => 8585
        );
        $params = $this->abstractParamsNode->getParamsAsArray();
        $this->assertSame($paramsToCompare, $params);
    }

    /**
     * Returns an array with initialized parameters.
     *
     * @return array<\AppserverIo\Appserver\Core\Api\Node\ParamNode> The array with params
     */
    public function getParamNodes()
    {
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/params.xml');
        $params = array();
        foreach ($configuration->getChilds('/params/param') as $paramConfiguration) {
            $paramNode = new ParamNode();
            $paramNode->initFromConfiguration($paramConfiguration);
            $params[$paramNode->getName()] = $paramNode;
        }
        return $params;
    }
}