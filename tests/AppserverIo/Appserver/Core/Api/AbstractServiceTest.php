<?php

/**
 * AppserverIo\Appserver\Core\Api\AbstractServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Api\Mock\MockService;
use AppserverIo\Appserver\Core\Api\Normalizer;

/**
 *
 * @package AppserverIo\Appserver\Core
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
     * @var AppserverIo\Appserver\Core\Api\MockService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->service = new MockService($this->getMockInitialContext());
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\InitialContext', $this->service->getInitialContext());
    }

    /**
     * Test if the system configuration has successfully been initialized.
     *
     * @return void
     */
    public function testGetSystemConfiguration()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\Api\Node\AppserverNode', $this->service->getSystemConfiguration());
    }

    /**
     * Test if the getter/setter for the system configuration.
     *
     * @return void
     */
    public function testGetSetSystemConfiguration()
    {
        $systemConfiguration = $this->service->getSystemConfiguration();
        $this->service->setSystemConfiguration($systemConfiguration);
        $this->assertSame($systemConfiguration, $this->service->getSystemConfiguration());
    }

    /**
     * Test the new instance method.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'AppserverIo\Appserver\Core\Api\Mock\MockService';
        $instance = $this->service->newInstance($className, array(
            $this->service->getInitialContext(),
            \Mutex::create(false)
        ));
        $this->assertInstanceOf($className, $instance);
    }

    /**
     * Test the new service method.
     *
     * @return void
     */
    public function testNewService()
    {
        $className = 'AppserverIo\Appserver\Core\Api\Mock\MockService';
        $service = $this->service->newService($className);
        $this->assertInstanceOf($className, $service);
    }
}