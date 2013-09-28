<?php

/**
 * TechDivision\ApplicationServer\Api\ContainerServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Api\ContainerService;
use TechDivision\ApplicationServer\Api\Normalizer;

/**
 *
 * @package TechDivision\ApplicationServer
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
     * @var TechDivision\ApplicationServer\Api\ContainerService
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
        $this->service->setNormalizer(new Normalizer());
    }

    /**
     * Tests if all containers have been initialized successfully.
     *
     * @return void
     */
    public function testFindAll()
    {
        $result = $this->service->findAll();
        $toCompare = json_decode(file_get_contents('TechDivision/ApplicationServer/Api/_files/containers.json'));
        $this->assertEquals($toCompare, $result);
    }

    /**
     * Tests if a dedicated container has been initialized successfully.
     *
     * @return void
     */
    public function testLoad()
    {
        $result = $this->service->load(1);
        $toCompare = json_decode(file_get_contents('TechDivision/ApplicationServer/Api/_files/container_1.json'));
        $this->assertEquals($toCompare, $result);
    }
}