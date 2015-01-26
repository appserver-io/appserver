<?php

/**
 * AppserverIo\Appserver\WebSocketProtocol\WebSocketRequestTest
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

namespace AppserverIo\Appserver\WebSocketProtocol;

/**
 * Test implementation for the WebSocketRequest class.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class WebSocketRequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * A handler path for testing purposes.
     *
     * @var string
     */
    const HANDLER_PATH = '/example';

    /**
     * The instance we want to test.
     *
     * @var \AppserverIo\Appserver\WebSocketProtocol\WebSocketRequest
     */
    protected $webSocketRequest;

    /**
     * Initializes the method we wan to test.
     *
     * @return void
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->webSocketRequest = new WebSocketRequest();
    }

    /**
     * Test the getter/setter for the handler path.
     *
     * @return void
     */
    public function testSetGetHandlerPath()
    {
        $this->webSocketRequest->setHandlerPath(WebSocketRequestTest::HANDLER_PATH);
        $this->assertSame(WebSocketRequestTest::HANDLER_PATH, $this->webSocketRequest->getHandlerPath());
    }
}
