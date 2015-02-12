<?php

/**
 * AppserverIo\Appserver\Core\Api\DeploymentServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

/**
 * Unit tests for our deployment service implementation.
 *
 * @author    Bernhard Wick <bw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DeploymentServiceTest extends AbstractServicesTest
{

    /**
     * The app service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\DeploymentService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return null
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new DeploymentService($this->getMockInitialContext());
    }

    /**
     * Test if the findAll() method returns the correct number of elements.
     *
     * @return null
     */
    public function testFindAll()
    {
        $this->assertCount(1, $this->service->findAll());
    }

    /**
     * Test if the load() method returns the correct deployment node.
     *
     * @return null
     */
    public function testLoad()
    {
        $deploymentNodes = $this->service->findAll();
        $deploymentNode = reset($deploymentNodes);

        $this->assertSame($deploymentNode, $this->service->load($deploymentNode->getPrimaryKey()));
    }

    /**
     * Test if the load() method returns the NULL for an invalid primary key.
     *
     * @return null
     */
    public function testLoadWithInvalidPrimaryKey()
    {
        $this->assertNull($this->service->load('invalidPrimaryKey'));
    }
}
