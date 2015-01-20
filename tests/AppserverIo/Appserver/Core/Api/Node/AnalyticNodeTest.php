<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\AnalyticNodeTest
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
use AppserverIo\Configuration\Configuration;

/**
 * Test for the analytic node implementation.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class AnalyticNodeTest extends AbstractTest
{

    /**
     * The location nodeinstance to test.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\AnalyticNode
     */
    protected $analytic;

    /**
     * Initializes an analytic node class we want to test.
     *
     * @return void
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->analytic = new AnalyticNode();
    }

    /**
     * Tests if the getUri() method works as expected.
     *
     * @return void
     */
    public function testGetType()
    {

        // initialize the analytic node
        $this->analytic->setNodeName('access');
        $this->analytic->initFromFile(__DIR__ . '/_files/analytic.xml');

        // initialize the array with result
        $connectors = array(
            array(
                'name' => 'ga-app-tracking',
                'type' => '\AppserverIo\WebServer\Modules\Analytics\Connectors\UniversalAnalytics\MeasurementProtocol',
                'params' => array(
                    't' => 'screenview',
                    'av' => '$1',
                    'aid' => '$2',
                    'aiid' => '$3',
                    'tid' => 'UA-12386171-4',
                    'an' => 'appserver',
                    'cd' => 'installation'
                )
            )
        );

        // check the URI and the connector configuration
        $this->assertSame('^\/welcome-page\/logo_(.+?)_(.+?)_(.+?)\.png.*', $this->analytic->getUri());
        $this->assertSame($connectors, $this->analytic->getConnectorsAsArray());
    }
}
