<?php

/**
 * AppserverIo\Appserver\ServletEngine\Servlets\DhtmlServletTest
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

namespace AppserverIo\Appserver\ServletEngine\Servlets;

use AppserverIo\Http\HttpProtocol;

/**
 * This is test implementation for the DHTML servlet implementation.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class DhtmlServletTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests the servlets init() method.
     *
     * @return void
     */
    public function testInit()
    {

        // create a servlet context mock instance
        $mockServletContext = $this->getMockBuilder($servletContextInterface = 'AppserverIo\Appserver\ServletEngine\Servlets\Mock\MockServletContextInterface')
            ->setMethods(get_class_methods($servletContextInterface))
            ->getMock();

        // mock the necessary method
        $mockServletContext->expects($this->once())
            ->method('getAppBase')
            ->will($this->returnValue($webappPath = '/opt/appserver/webapps'));
        $mockServletContext->expects($this->once())
            ->method('getBaseDirectory')
            ->will($this->returnValue($webappPath = '/opt/appserver'));

        // create a servlet config mock instance
        $mockServletConfig = $this->getMockBuilder($servletConfigInterface = 'AppserverIo\Psr\Servlet\ServletConfigInterface')
            ->setMethods(get_class_methods($servletConfigInterface))
            ->getMock();

        // mock the necessary method
        $mockServletConfig->expects($this->once())
            ->method('getWebappPath')
            ->will($this->returnValue($webappPath = '/opt/appserver/webapps/test'));
        $mockServletConfig->expects($this->exactly(2))
            ->method('getServletContext')
            ->will($this->returnValue($mockServletContext));

        // create and initialize a servlet instance
        $servlet = new DhtmlServlet();
        $servlet->init($mockServletConfig);

        // check that the servlet has been initilized successfully
        $this->assertSame($webappPath, $servlet->getWebappPath());
        $this->assertSame(get_class($servlet), $servlet->getPoweredBy());
    }

    /**
     * This tests the service() method with a request, prepared with a servlet path.
     *
     * @return void
     */
    public function testServiceWithServletPath()
    {

        // initialize the controller with mocked methods
        $mockServlet = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\Servlets\DhtmlServlet')
            ->setMethods(array('getWebappPath', 'getPoweredBy'))
            ->getMock();

        // mock the necessary methods
        $mockServlet->expects($this->once())
            ->method('getWebappPath')
            ->will($this->returnValue(__DIR__));
        $mockServlet->expects($this->once())
            ->method('getPoweredBy')
            ->will($this->returnValue(get_class($mockServlet)));

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\Http\Request')
            ->setMethods(array('getServletPath'))
            ->getMock();

        // mock the necessary methods
        $mockServletRequest->expects($this->once())
            ->method('getServletPath')
            ->will($this->returnValue('/_files/my_template.dhtml'));

        // create a mock servlet response instance
        $mockServletResponse = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\Http\Response')
            ->setMethods(array('hasHeader', 'getHeader', 'addHeader', 'appendBodyStream'))
            ->getMock();

        // mock the necessary methods
        $mockServletResponse->expects($this->once())
            ->method('hasHeader')
            ->with(HttpProtocol::HEADER_X_POWERED_BY)
            ->will($this->returnValue(true));
        $mockServletResponse->expects($this->once())
            ->method('getHeader')
            ->with(HttpProtocol::HEADER_X_POWERED_BY)
            ->will($this->returnValue($poweredBy = 'AppserverIo\Routlt\ControllerServlet'));
        $mockServletResponse->expects($this->once())
            ->method('addHeader')
            ->with(HttpProtocol::HEADER_X_POWERED_BY, $poweredBy . ', ' . get_class($mockServlet));
        $mockServletResponse->expects($this->once())
            ->method('appendBodyStream')
            ->with('Hello World!');

        // invoke the method we want to test
        $mockServlet->service($mockServletRequest, $mockServletResponse);
    }

    /**
     * This tests the service() method with a request, prepared with a missing PHTML file.
     *
     * @return void
     *
     * @expectedException AppserverIo\Psr\Servlet\ServletException
     * @expectedExceptionMessage Can't load requested template '/_files/not_existing_template.dhtml'
     */
    public function testServiceWithMissingPhtmlFile()
    {

        // initialize the controller with mocked methods
        $mockServlet = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\Servlets\DhtmlServlet')
            ->setMethods(array('getWebappPath', 'getPoweredBy'))
            ->getMock();

        // mock the necessary methods
        $mockServlet->expects($this->once())
            ->method('getWebappPath')
            ->will($this->returnValue(__DIR__));
        $mockServlet->expects($this->once())
            ->method('getPoweredBy')
            ->will($this->returnValue(get_class($mockServlet)));

        // create a mock servlet request instance
        $mockServletRequest = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\Http\Request')
            ->setMethods(array('getServletPath', 'hasHeader'))
            ->getMock();

        // mock the necessary methods
        $mockServletRequest->expects($this->once())
            ->method('getServletPath')
            ->will($this->returnValue('/_files/not_existing_template.dhtml'));

        // create a mock servlet response instance
        $mockServletResponse = $this->getMockBuilder('AppserverIo\Appserver\ServletEngine\Http\Response')
            ->setMethods(array('hasHeader', 'addHeader'))
            ->getMock();

        // mock the necessary methods
        $mockServletResponse->expects($this->once())
            ->method('hasHeader')
            ->with(HttpProtocol::HEADER_X_POWERED_BY)
            ->will($this->returnValue(false));
        $mockServletResponse->expects($this->once())
            ->method('addHeader')
            ->with(HttpProtocol::HEADER_X_POWERED_BY, get_class($mockServlet));

        // invoke the method we want to test
        $mockServlet->service($mockServletRequest, $mockServletResponse);
    }
}
