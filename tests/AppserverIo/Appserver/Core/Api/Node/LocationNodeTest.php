<?php

/**
 * AppserverIo\Appserver\Core\Api\Node\LocationNodeTest
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
 * Test for the location node implementation.
 *
 * @author    Tim Wagner <tw@appserver.io>
 * @copyright 2015 TechDivision GmbH <info@appserver.io>
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      https://github.com/appserver-io/appserver
 * @link      http://www.appserver.io
 */
class LocationNodeTest extends AbstractTest
{

    /**
     * The location nodeinstance to test.
     *
     * @var \AppserverIo\Appserver\Core\Api\Node\LocationNode
     */
    protected $location;

    /**
     * Initializes a location node class we want to test.
     *
     * @return void
     * @see PHPUnit_Framework_TestCase::setUp()
     */
    protected function setUp()
    {
        $this->location = new LocationNode();
    }

    /**
     * Tests if the initialization from a file works as expected.
     *
     * @return void
     */
    public function testInitFromFile()
    {

        // initialize the location node
        $this->location->setNodeName('location');
        $this->location->initFromFile(__DIR__ . '/_files/location.xml');

        // validate the node
        $this->validate();
    }

    /**
     * Tests if the initialization from a file works as expected.
     *
     * @return void
     */
    public function testInitFromFileWithUuids()
    {

        // initialize the location node
        $this->location->setNodeName('location');
        $this->location->initFromFile(__DIR__ . '/_files/location-with-uuids.xml');

        // validate the node
        $this->validate();
    }

    /**
     * Tests if the initialization from a string works as expected.
     *
     * @return void
     */
    public function testInitFromString()
    {

        // initialize the location node
        $this->location->setNodeName('location');
        $this->location->initFromString(file_get_contents(__DIR__ . '/_files/location.xml'));

        // validate the node
        $this->validate();
    }

    /**
     * Tests if the initialization from a configuration instance works as expected.
     *
     * @return void
     */
    public function testInitFromConfiguration()
    {

        // initialize the configuration
        $configuration = new Configuration();
        $configuration->initFromFile(__DIR__ . '/_files/location.xml');

        // initialize the location node
        $this->location->setNodeName('location');
        $this->location->initFromConfiguration($configuration);

        // validate the node
        $this->validate();
    }

    /**
     * Validates the location node when inititalized from
     * the example file.
     *
     * @return void
     */
    public function validate()
    {

        // check the condition
        $this->assertSame('^test\/.*\.php', $this->location->getCondition());

        // load the file handlers
        $fileHandlers = $this->location->getFileHandlers();

        // asser that we've exactly one file handler
        $this->assertCount(1, $fileHandlers);

        // check the file handlers values
        foreach ($fileHandlers as $fileHandler) {
            $this->assertSame('hhvm', $fileHandler->getName());
            $this->assertSame('.php', $fileHandler->getExtension());
            $this->assertSame(9000, $fileHandler->getParam('port'));
            $this->assertSame('127.0.0.1', $fileHandler->getParam('host'));
        }
    }

    /**
     * Validates the location node when inititalized from
     * the example file.
     *
     * @return void
     */
    public function validateWithUuids()
    {

        // check the UUID and condition
        $this->assertSame('a47ac10b-58cc-4372-a567-0e02b2c3d479', $this->location->getUuid());
        $this->assertSame('^test\/.*\.php', $this->location->getCondition());

        // load the file handlers
        $fileHandlers = $this->location->getFileHandlers();

        // asser that we've exactly one file handler
        $this->assertCount(1, $fileHandlers);

        // values to compare
        $paramsToCompare = array(
            'host' => array('uuid' => 'c47ac10b-4372- 58cc-a567-0e02b2c3d479', 'type' => 'string', 'value' => '127.0.0.1'),
            'port' => array('uuid' => 'd47ac10b-4372- 58cc-a567-0e02b2c3d479', 'type' => 'integer', 'value' => 9000)
        );

        // check the file handlers values
        foreach ($fileHandlers as $fileHandler) {

            // compare the file handler values
            $this->assertSame('b47ac10b-4372- 58cc-a567-0e02b2c3d479', $fileHandler->getUuid());
            $this->assertSame('hhvm', $fileHandler->getName());
            $this->assertSame('.php', $fileHandler->getExtension());

            // compare the params
            foreach ($fileHandler->getParams() as $param) {
                $this->assertSame($paramsToCompare[$param->getName()]['uuid'], $param->getUuid());
                $this->assertSame($paramsToCompare[$param->getName()]['type'], $param->getType());
                $this->assertSame($paramsToCompare[$param->getName()]['value'], $param->castToType());
            }
        }
    }
}
