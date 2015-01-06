<?php

/**
 * AppserverIo\Appserver\ServletEngine\Http\RequestTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\ServletEngine\Http;

use AppserverIo\Http\HttpProtocol;

/**
 * Test for the request implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class RequestTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The response instance to test.
     *
     * @var \AppserverIo\Appserver\ServletEngine\Http\Request
     */
    protected $request;

    /**
     * Initializes the request instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->request = new Request();
    }

    /**
     * Tests has non existing parameter on HTTP request instance.
     *
     * @return void
     */
    public function testHasParameterWhenNonExists()
    {

        // initialize the mock HTTP request
        $mockRequest = $this->getMock('AppserverIo\Http\HttpRequest');
        $mockRequest->expects($this->any())
            ->method('hasParam')
            ->will($this->returnValue(false));

        // inject the mock HTTP request
        $this->request->injectHttpRequest($mockRequest);

        // check for an non existing parameter
        $this->assertFalse($this->request->hasParameter('unknown'));
    }
}
