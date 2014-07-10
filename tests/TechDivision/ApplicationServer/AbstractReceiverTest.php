<?php

/**
 * TechDivision\ApplicationServer\AbstractReceiverTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\Configuration\Configuration;
use TechDivision\ApplicationServer\Mock\MockApplication;
use TechDivision\ApplicationServer\Mock\MockContainer;
use TechDivision\ApplicationServer\Mock\MockReceiver;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractReceiverTest extends AbstractTest
{

    /**
     * The receiver instance to test.
     *
     * @var \TechDivision\ApplicationServer\MockReceiver
     */
    protected $receiver;

    /**
     * The initial context instance passed to the receiver.
     *
     * @var \TechDivision\ApplicationServer\InitialContext
     */
    protected $initialContext;

    /**
     * The container instance passed to the receiver.
     *
     * @var \TechDivision\ApplicationServer\MockContainer
     */
    protected $container;

    /**
     * Initializes the application instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->container = $this->getMockContainer();
        $this->initialContext = $this->container->getInitialContext();
        $this->receiver = new MockReceiver($this->initialContext, $this->container);
    }

    /**
     * Test's if the container instance passed with the constructor
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetContainer()
    {
        $this->assertSame($this->container, $this->receiver->getContainer());
    }

    /**
     * Test's that the thread type specified in the configuration file
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetThreadType()
    {
        $this->assertSame('TechDivision\ApplicationServer\Mock\Socket\MockRequest', $this->receiver->getThreadType());
    }

    /**
     * Test's that the worker type specified in the configuration file
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetWorkerType()
    {
        $this->assertSame('TechDivision\ApplicationServer\Mock\Socket\MockWorker', $this->receiver->getWorkerType());
    }

    /**
     * Test's that the worker number specified in the configuration file
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetWorkerNumber()
    {
        $this->assertSame(16, $this->receiver->getWorkerNumber());
    }

    /**
     * Test's that the address specified in the configuration file
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetAddress()
    {
        $this->assertSame('0.0.0.0', $this->receiver->getAddress());
    }

    /**
     * Test's that the port specified in the configuration file
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetPort()
    {
        $this->assertSame(8586, $this->receiver->getPort());
    }

    /**
     * Test's that garbage collection returns the number of
     * collected cycles.
     *
     * @return void
     */
    public function testGc()
    {
        gc_collect_cycles(); // to make sure that garbage collection returns zero cycles
        $this->assertSame(0, $this->receiver->gc());
    }

    /**
     * Test's that the method to enable the garbage collection
     * works as expected.
     *
     * @return void
     */
    public function testGcEnable()
    {
        gc_disable();
        $this->assertFalse(gc_enabled());
        $this->receiver->gcEnable();
        $this->assertTrue(gc_enabled());
    }

    /**
     * Test's that the method to disable the garbage collection
     * works as expected.
     *
     * @return void
     */
    public function testGcDisable()
    {
        gc_enable();
        $this->assertTrue(gc_enabled());
        $this->receiver->gcDisable();
        $this->assertFalse(gc_enabled());
    }

    /**
     * Test's that the method to check if the garbage collection
     * is enabled works as expected.
     *
     * @return void
     */
    public function testGcEnabled()
    {
        $this->receiver->gcDisable();
        $this->assertFalse($this->receiver->gcEnabled());
        $this->receiver->gcEnable();
        $this->assertTrue($this->receiver->gcEnabled());
    }

    /**
     * Test's that the receiver creates an instance of the
     * worker class defined in the configuration file.
     *
     * @return void
     */
    public function testNewWorker()
    {
        list ($client, $server) = $this->getSocketPair();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Mock\Socket\MockWorker', $this->receiver->newWorker($server));
    }

    /**
     * Checks if the new instance method works correctly.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\Configuration\Configuration';
        $this->assertInstanceOf($className, $this->receiver->newInstance($className));
    }

    /**
     * Check's if the initial context instance passed with the constructor
     * is the same as returned by the getter.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertSame($this->initialContext, $this->receiver->getInitialContext());
    }

    /**
     * Test's the start method.
     *
     * @return void
     */
    public function testStart()
    {
        $this->markTestSkipped('Seems to be a pthread error.');
        $this->assertTrue($this->receiver->start());
    }

    /**
     * Test's the start method when socket can't be created.
     *
     * @return void
     */
    public function testStartWhenSocketCantBeCreated()
    {
        $this->markTestSkipped('Seems to be a pthread error.');
        $this->receiver->setResourceClass('TechDivision\ApplicationServer\Mock\Socket\MockServerThatCantCreateSocket');
        $this->assertFalse($this->receiver->start());
    }
}