<?php

/**
 * AppserverIo\Appserver\Core\AbstractContextThreadTest
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

use AppserverIo\Appserver\Core\Mock\MockContextThread;

/**
 * Test for the abstract context thread class.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AbstractContextThreadTest extends AbstractTest
{

    /**
     * The mock context thread to test.
     *
     * @var \AppserverIo\Appserver\Core\Mock\MockContextThread
     */
    protected $contextThread;

    /**
     * Initializes the container instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->contextThread = new MockContextThread($this->getMockInitialContext());
    }

    /**
     * Checks if the context thread returns the initial context
     * passed with the constructor.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('AppserverIo\Appserver\Core\InitialContext', $this->contextThread->getInitialContext());
    }

    /**
     * Checks if the new instance method works correctly.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'AppserverIo\Configuration\Configuration';
        $this->assertInstanceOf($className, $this->contextThread->newInstance($className));
    }
}
