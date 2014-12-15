<?php

/**
 * AppserverIo\Appserver\Core\ServerTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Configuration\Configuration;
use AppserverIo\Appserver\Core\Api\Node\BaseDirectoryNode;
use AppserverIo\Appserver\Core\Api\Node\NodeValue;

/**
 *
 * @package AppserverIo\Appserver\Core
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
     * @var AppserverIo\Appserver\Core\Server
     */
    protected $server;

    /**
     * Initializes the server instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        // initialize the configuration
        $configuration = $this->getAppserverConfiguration();

        // replace the base directory
        $appserverConfiguration = new Configuration();
        $appserverConfiguration->setNodeName('appserver');
        $baseDirectoryConfiguration = new Configuration();
        $baseDirectoryConfiguration->setNodeName('baseDirectory');
        $baseDirectoryConfiguration->setValue(__DIR__);
        $appserverConfiguration->addChild($baseDirectoryConfiguration);
        $configuration->merge($appserverConfiguration);

        // initialize the server instance
        $this->server = new Server($configuration);
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\InitialContext', $this->server->getInitialContext());
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
        $this->assertInstanceOf('AppserverIo\Appserver\Core\Api\Node\AppserverNode', $this->server->getSystemConfiguration());
    }

    /**
     * Test the server's start method.
     *
     * @return void
     */
    public function testStart()
    {

        // initialize the configuration
        $configuration = $this->getAppserverConfiguration();

        // replace the base directory
        $appserverConfiguration = new Configuration();
        $appserverConfiguration->setNodeName('appserver');
        $baseDirectoryConfiguration = new Configuration();
        $baseDirectoryConfiguration->setNodeName('baseDirectory');
        $baseDirectoryConfiguration->setValue(__DIR__);
        $appserverConfiguration->addChild($baseDirectoryConfiguration);
        $configuration->merge($appserverConfiguration);

        // create a new mock server implementation
        $server = $this->getMock('AppserverIo\Appserver\Core\Server', array('startContainers'), array($configuration));

        // mock the servers start method
        $server->expects($this->once())->method('startContainers');

        // start the server instance
        $server->start();

        // check that we found the configured container
        $this->assertCount(1, $server->getContainers());
    }

    /**
     * Test the new instance method.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $id = 1;
        $className = 'AppserverIo\Appserver\Core\Mock\MockContainerThread';
        $instance = $this->server->newInstance(
            $className,
            array(
                $this->server->getInitialContext(), \Mutex::create(false), $id
            )
        );
        $this->assertInstanceOf($className, $instance);
    }
}