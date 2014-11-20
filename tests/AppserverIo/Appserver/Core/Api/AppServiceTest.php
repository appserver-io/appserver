<?php

/**
 * AppserverIo\Appserver\Core\Api\AppServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Api\AppService;
use AppserverIo\Appserver\Core\Api\Node\AppNode;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AppServiceTest extends AbstractTest
{

    /**
     * The app service instance to test.
     *
     * @var AppserverIo\Appserver\Core\Api\AppService
     */
    protected $appService;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->appService = new AppService($this->getMockInitialContext());
    }

    /**
     * Test if the findAll() method returns the correct number of elements.
     *
     * @return void
     */
    public function testFindAll()
    {
        $this->assertCount(4, $this->appService->findAll());
    }

    /**
     * Test if the findAllByName() method returns the correct number of elements.
     *
     * @return void
     */
    public function testFindAllByName()
    {
        $this->assertCount(2, $this->appService->findAllByName('api'));
    }

    /**
     * Test if the load() method returns the correct app node.
     *
     * @return void
     */
    public function testLoad()
    {
        $appNode = new AppNode();
        $appNode->setNodeName('application');
        $appNode->setWebappPath('/opt/appserver/anotherwebapppath');
        $appNode->setName('someappname');
        $this->appService->persist($appNode);
        $this->assertSame($appNode, $this->appService->load($appNode->getPrimaryKey()));
    }

    /**
     * Test if the load() method returns the NULL for an invalid primary key.
     *
     * @return void
     */
    public function testLoadWithInvalidPrimaryKey()
    {
        $this->assertNull($this->appService->load('invalidPrimaryKey'));
    }
}