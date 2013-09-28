<?php

/**
 * TechDivision\ApplicationServer\ServerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ServerTest extends AbstractTest
{

    /**
     * The server instance to test.
     *
     * @var TechDivision\ApplicationServer\Server
     */
    protected $server;

    /**
     * Initializes the server instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $configuration = new Configuration();
        $configuration->initFromFile('_files/appserver.xml');
        $configuration->addChildWithNameAndValue('baseDirectory', '/opt/appserver');
        $this->server = new Server($configuration);
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext', $this->server->getInitialContext());
    }

    /**
     * Test if the system logger has successfully been initialized.
     *
     * @return void
     */
    public function testGetSystemLogger()
    {
        $this->assertInstanceOf('Monolog\Logger', $this->server->getSystemLogger());
    }

    /**
     * Test if the system configuration has been passed successfully.
     *
     * @return void
     */
    public function testGetSystemConfiguration()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $this->server->getSystemConfiguration());
    }

    /**
     * Test if the system logger configuration has been initialized.
     *
     * @return void
     */
    public function testGetSystemLoggerConfiguration()
    {
        $systemLoggerConfiguration = $this->server->getSystemLoggerConfiguration();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $systemLoggerConfiguration);
        $this->assertEquals('Monolog\Logger', $systemLoggerConfiguration->getType());
    }

    /**
     * Test if the initial context configuration has been initialized.
     *
     * @return void
     */
    public function testGetInitialContextConfiguration()
    {
        $initialContextConfiguration = $this->server->getInitialContextConfiguration();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $initialContextConfiguration);
        $this->assertEquals('TechDivision\ApplicationServer\InitialContext', $initialContextConfiguration->getType());
    }

    /**
     * Test the server's start method.
     *
     * @return void
     */
    public function testStart()
    {
        $this->server->start();
        $this->assertCount(3, $this->server->getThreads());
    }

    /**
     * Test the new instance method.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\ApplicationServer\Mock\MockContainerThread';
        $instance = $this->server->newInstance($className, array(
            $this->server->getInitialContext(),
            \Mutex::create(false),
            $id = 1
        ))
        ;
        $this->assertInstanceOf($className, $instance);
    }
}