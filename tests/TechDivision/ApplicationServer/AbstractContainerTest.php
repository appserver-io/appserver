<?php

/**
 * TechDivision\ApplicationServer\AbstractContainerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Mock\MockContainer;
use TechDivision\ApplicationServer\Mock\MockApplication;
use TechDivision\ApplicationServer\Mock\MockReceiver;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractContainerTest extends AbstractTest
{

    /**
     * The application instance to test.
     *
     * @var \TechDivision\ApplicationServer\MockContainer
     */
    protected $container;

    /**
     * Initializes the container instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = new MockContainer($this->getMockInitialContext(), $this->getContainerNode(), $this->getMockApplications());
    }

    /**
     * Checks if the number of applications equals the number that has been
     * passed to the constructor.
     *
     * @return void
     */
    public function testGetApplicationsFromConstructor()
    {
        $this->markTestSkipped('Seems to be a pthread error.');
        $this->assertCount(1, $this->container->getApplications());
    }

    /**
     * Checks if the number of applications equals to the number that has been
     * passed to the setter.
     *
     * @return void
     */
    public function testSetGetApplications()
    {
        $applications = $this->getMockApplications($size = 4);
        $this->container->setApplications($applications);
        // assertSame() doesn't work here because the AbstractContainer extends a \Stackable
        $this->assertEquals($applications, $this->container->getApplications());
    }

    /**
     * Checks if the receiver instance specified in the configuration has
     * been returned.
     *
     * @return void
     */
    public function testGetReceiver()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\Interfaces\ReceiverInterface', $this->container->getReceiver());
    }

    /**
     * Tests the if the receiver type specified in the configuration file
     * is used by the container.
     *
     * @return void
     */
    public function testGetReceiverType()
    {
        $this->assertEquals('TechDivision\ApplicationServer\Mock\MockReceiver', $this->container->getReceiverType());
    }

    /**
     * Checks if the new instance method works as expected.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\ApplicationServer\Configuration';
        $this->assertInstanceOf($className, $this->container->newInstance($className));
    }

    /**
     * Test if the run method starts the receiver.
     *
     * @return void
     */
    public function testRun()
    {
        $this->markTestSkipped('Seems to be a pthread error.');
        $this->container->run();
        $this->assertTrue($this->container->isStarted());
    }

    /**
     * Checks if the context thread returns the initial context
     * passed with the constructor.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext', $this->container->getInitialContext());
    }
}