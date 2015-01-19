<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AbstractValueNodeTest
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

namespace AppserverIo\Appserver\Core\Api\Node;

use AppserverIo\Appserver\Core\AbstractTest;

/**
 * Test for the abstract value node implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AbstractValueNodeTest extends AbstractTest
{

    /**
     * The mock instance of the abstract class.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AppstractValueNode
     */
    protected $abstractValueNode;

    /**
     * Initializes an instance of the abstract class we want to test.
     *
     * @return void
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->abstractValueNode = $this->getMockForAbstractClass('AppserverIo\Appserver\Core\Api\Node\AbstractValueNode');
    }

    /**
     * Tests the setter/getter for the value node.
     *
     * @return void
     */
    public function testSetGetNodeValue()
    {
        $this->abstractValueNode->setNodeValue($nodeValue = new NodeValue());
        $this->assertSame($nodeValue, $this->abstractValueNode->getNodeValue());
    }
}
