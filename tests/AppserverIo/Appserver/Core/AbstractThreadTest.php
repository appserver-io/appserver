<?php

/**
 * AppserverIo\Appserver\Core\AbstractThreadTest
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

use AppserverIo\Appserver\Core\Mock\MockThread;

/**
 * Test for the abstract tread class.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AbstractThreadTest extends AbstractTest
{

    /**
     * Test's if the thread's constructor works as expected.
     *
     * @return void
     */
    public function testConstructor()
    {
        $thread = new MockThread($someInstance = new \stdClass());
        $this->assertEquals($someInstance, $thread->getSomeInstance());
    }

    /**
     * Test's if the threads main method has been invoked.
     *
     * @return void
     */
    public function testMain()
    {
        $thread = new MockThread(new \stdClass());
        $thread->run();
        $this->assertTrue($thread->hasExcecuted());
    }
}