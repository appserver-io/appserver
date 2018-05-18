<?php

/**
 * AppserverIo\Appserver\Core\ContainerThreadTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Mock\MockAbstractContainerThread;
use AppserverIo\Psr\ApplicationServer\ApplicationServerInterface;

/**
 * Test for the container thread class.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class ContainerThreadTest extends AbstractTest
{

    /**
     * The application instance to test.
     *
     * @var \AppserverIo\Appserver\Core\AbstractContainerThread
     */
    protected $containerThread;

    /**
     * Initializes the application instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->containerThread = new MockAbstractContainerThread(
            $this->getMockInitialContext(),
            $this->getNamingDirectory(),
            $this->getContainerNode(),
            ApplicationServerInterface::NETWORK
        );
    }

    /**
     * Test's if the configuration instance passed to the constructor is returned by
     * the getter method.
     *
     * @return void
     */
    public function testGetDeployment()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\Mock\MockDeployment', $this->containerThread->getDeployment());
    }

    /**
     * Checks if the new instance method works correctly.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'AppserverIo\Configuration\Configuration';
        $this->assertInstanceOf($className, $this->containerThread->newInstance($className));
    }
}