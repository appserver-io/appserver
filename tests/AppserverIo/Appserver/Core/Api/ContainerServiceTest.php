<?php

/**
 * AppserverIo\Appserver\Core\Api\ContainerServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Api\ContainerService;
use AppserverIo\Appserver\Core\Api\Normalizer;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ContainerServiceTest extends AbstractTest
{

    /**
     * The abstract service instance to test.
     *
     * @var AppserverIo\Appserver\Core\Api\ContainerService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->service = new ContainerService($this->getMockInitialContext());
    }

    /**
     * Test if the application server's base directory will be returned.
     *
     * @return void
     */
    public function testGetBaseDirectory()
    {
        $this->assertSame('/opt/appserver', $this->service->getBaseDirectory());
    }

    /**
     * Test if the application server's base directory will be returned appended
     * with the directory passed as parameter.
     *
     * @return void
     */
    public function testGetBaseDirectoryWithDirectoryToAppend()
    {
        $directoryToAppend = '/webapps';
        $this->assertSame('/opt/appserver' . $directoryToAppend, $this->service->getBaseDirectory($directoryToAppend));
    }
}