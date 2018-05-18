<?php

/**
 * AppserverIo\Appserver\Core\Api\DatasourceServiceTest
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
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\Core\Api;

use AppserverIo\Description\Api\Node\DatasourceNode;

/**
 * Tests for our DatasourceService class
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @author     Bernhard Wick <bw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       http://www.appserver.io
 */
class DatasourceServiceTest extends AbstractServicesTest
{

    /**
     * The abstract service instance to test.
     *
     * @var \PHPUnit_Framework_MockObject_MockObject|\AppserverIo\Appserver\Core\Api\DatasourceService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->service = new DatasourceService($this->getMockInitialContext());
    }

    /**
     * Test if the findAll() method returns the correct number of elements.
     *
     * @return null
     */
    public function testFindAll()
    {
        $this->assertCount(0, $this->service->findAll());
    }

    /**
     * Test if the findAllByName() method returns the correct number of elements.
     *
     * @return null
     */
    public function testFindAllByName()
    {
        $this->assertCount(0, $this->service->findAllByName('api'));
    }

    /**
     * Test if the load() method returns the correct app node.
     *
     * @return null
     */
    public function testLoad()
    {
        $datasourceNode = new DatasourceNode();
        $this->service->persist($datasourceNode);

        $this->assertSame($datasourceNode, $this->service->load($datasourceNode->getPrimaryKey()));
    }

    /**
     * Test if the load() method returns the NULL for an invalid primary key.
     *
     * @return null
     */
    public function testLoadWithInvalidPrimaryKey()
    {
        $this->assertNull($this->service->load('invalidPrimaryKey'));
    }
}
