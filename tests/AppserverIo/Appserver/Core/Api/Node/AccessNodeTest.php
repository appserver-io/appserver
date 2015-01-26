<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AccessNodeTest
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
use AppserverIo\Configuration\Configuration;

/**
 * Test for the access node implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class AccessNodeTest extends AbstractTest
{

    /**
     * The location node instance to test.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AccessNode
     */
    protected $access;

    /**
     * Initializes an access node class we want to test.
     *
     * @return void
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->access = new AccessNode();
    }

    /**
     * Tests if the getType() method works as expected.
     *
     * @return void
     */
    public function testGetType()
    {

        // initialize the access node
        $this->access->setNodeName('access');
        $this->access->initFromFile(__DIR__ . '/_files/access.xml');

        // check the type and params
        $this->assertSame('allow', $this->access->getType());
        $this->assertSame(array('X_REQUEST_URI' => '.*'), $this->access->getParamsAsArray());
    }
}
