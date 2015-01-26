<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AnalyticsNodeTraitTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Appserver\Core\AbstractTest;

/**
 * Test for the analytics node trait implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AnalyticsNodeTraitTest extends AbstractTest
{

    /**
     * Tests if the getAnalyticsAsArray() method works as expected.
     *
     * @return void
     */
    public function testGetAnalyticsAsArray()
    {

        // initialize the array with expected result
        $result = json_decode(file_get_contents(__DIR__ . '/_files/prepareAnalytics.json'), true);

        // create an AnalyticNode instance
        $analytic = new AnalyticNode();
        $analytic->initFromFile(__DIR__ . '/_files/analytic.xml');

        // mock the trait to be tested
        $mockTrait = $this->getMockForTrait('AppserverIo\Appserver\Core\Api\Node\AnalyticsNodeTrait', array(), '', false, false, true, array('getAnalytics'));
        $mockTrait->expects($this->once())
            ->method('getAnalytics')
            ->will($this->returnValue(array($analytic)));

        // check the result
        $this->assertSame($result, $mockTrait->getAnalyticsAsArray());
    }

    /**
     * Tests if the getAnalytic() method works as expected.
     *
     * @return void
     */
    public function testGetAnalytic()
    {

        // create an AnalyticNode instance
        $analytic = new AnalyticNode();
        $analytic->initFromFile(__DIR__ . '/_files/analytic.xml');

        // mock the trait to be tested
        $mockTrait = $this->getMockForTrait('AppserverIo\Appserver\Core\Api\Node\AnalyticsNodeTrait', array(), '', false, false, true, array('getAnalytics'));
        $mockTrait->expects($this->once())
            ->method('getAnalytics')
            ->will($this->returnValue(array($analytic)));

        // check the result
        $this->assertSame($analytic, $mockTrait->getAnalytic('^\/welcome-page\/logo_(.+?)_(.+?)_(.+?)\.png.*'));
    }
}
