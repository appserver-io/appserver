<?php

/**
 * TechDivision\ApplicationServer\Api\AbstractServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Api\Mock\MockService;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractServiceTest extends AbstractTest
{

    /**
     * The abstract service instance to test.
     *
     * @var TechDivision\ApplicationServer\Api\MockService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $configuration = new Configuration();
        $configuration->initFromFile('_files/appserver_initial_context.xml');

        $systemConfiguration = new Configuration();
        $systemConfiguration->initFromFile('TechDivision/ApplicationServer/_files/appserver.xml');

        $initialContext = new InitialContext($configuration);
        $initialContext->setSystemConfiguration($systemConfiguration);

        $this->service = new MockService($initialContext);
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext', $this->service->getInitialContext());
    }

    /**
     * Test if the system configuration has successfully been initialized.
     *
     * @return void
     */
    public function testGetSystemConfiguration()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $this->service->getSystemConfiguration());
    }

    /**
     * Test the normalization of a Configuration node.
     *
     * @return void
     */
    public function testNormalize()
    {
        // initialize the configuration
        $configuration = new Configuration();
        $configuration->setNodeName('aNodeName');
        $configuration->setValue($value = 'Some string');
        $configuration->setData($key01 = 'key_01', $value01 = 'value_01');
        $configuration->setData($key02 = 'key_02', $value02 = 'value_02');

        // add a child node
        $child = new Configuration();
        $child->setNodeName('childNode');
        $configuration->addChild($child);

        // normalize the configuration node
        $node = $this->service->normalize($configuration);

        // and compare it
        $nodeToCompare = new \stdClass();
        $nodeToCompare->value = $value;
        $nodeToCompare->$key01 = $value01;
        $nodeToCompare->$key02 = $value02;
        $this->assertEquals($nodeToCompare, $node);
    }

    /**
     * Test the new instance method.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\ApplicationServer\Api\Mock\MockService';
        $instance = $this->service->newInstance($className, array(
            $this->service->getInitialContext(),
            \Mutex::create(false)
        ));
        $this->assertInstanceOf($className, $instance);
    }
}