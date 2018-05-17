<?php

/**
 * AppserverIo\Appserver\Core\AbstractDeploymentTest
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

/**
 * Test for the abstract deployment class.
 *
 * @author    Tim Wagner <tw@techdivision.com>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AbstractDeploymentTest extends AbstractTest
{

    /**
     * The abstract deployment instance to test.
     *
     * @var \AppserverIo\Appserver\Core\AbstractDeployment
     */
    protected $deployment;

    /**
     * Initializes the container instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->deployment = $this->getMockForAbstractClass('AppserverIo\Appserver\Core\AbstractDeployment');
    }

    /**
     * Checks if the newInstance() method works as expected.
     *
     * @return void
     */
    public function testNewInstance()
    {

        // define the name of the instance to be created
        $className = 'AppserverIo\Configuration\Configuration';

        // mock the initial context
        $mockInitialContext = $this->getMockBuilder('AppserverIo\Psr\ApplicationServer\ContextInterface')
                                   ->setMethods(get_class_methods('AppserverIo\Psr\ApplicationServer\ContextInterface'))
                                   ->getMock();
        $mockInitialContext->expects($this->once())
                           ->method('newInstance')
                           ->with($className, array())
                           ->will($this->returnValue(new $className()));

        // mock the container
        $mockContainer = $this->getMockBuilder('AppserverIo\Appserver\Core\Interfaces\ContainerInterface')
                              ->setMethods(get_class_methods('AppserverIo\Appserver\Core\Interfaces\ContainerInterface'))
                              ->getMock();
        $mockContainer->expects($this->once())
                      ->method('getInitialContext')
                      ->will($this->returnValue($mockInitialContext));

        // inject the container
        $this->deployment->injectContainer($mockContainer);

        // query the created instance
        $this->assertInstanceOf($className, $this->deployment->newInstance($className));
    }
}