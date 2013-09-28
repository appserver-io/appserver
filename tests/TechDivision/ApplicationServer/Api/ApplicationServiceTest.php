<?php

/**
 * TechDivision\ApplicationServer\Api\ApplicationServiceTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Api\ApplicationService;
use TechDivision\ApplicationServer\Api\Normalizer;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class ApplicationServiceTest extends AbstractTest
{

    /**
     * The abstract service instance to test.
     *
     * @var TechDivision\ApplicationServer\Api\ApplicationService
     */
    protected $service;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->service = new ApplicationService($this->getMockInitialContext());
        $this->service->setNormalizer(new Normalizer());
    }

    /**
     * Test's if all application have been initialized successfully.
     *
     * @return void
     */
    public function testFindAll()
    {
        $result = $this->service->findAll();
        $toCompare = json_decode(file_get_contents('TechDivision/ApplicationServer/Api/_files/apps.json'));
        $this->assertEquals($toCompare, $result);
    }

    /**
     * Test's if a dedicated application has been initialized successfully.
     *
     * @return void
     */
    public function testLoad()
    {
        $result = $this->service->load(1);
        $toCompare = json_decode(file_get_contents('TechDivision/ApplicationServer/Api/_files/apps_1.json'));
        $this->assertEquals($toCompare, $result);
    }

    /**
     * Test's if the create method add's the passed Configuration node with applications
     * will be added to the container.
     *
     * @return void
     */
    public function testCreate()
    {

        $systemConfiguration = $this->service->getSystemConfiguration();

        $mockApplications = array();

        $mocksToCreate = array(1 => 'api', 2 => 'demo', 3 => 'example', 4 => 'testName');
        foreach ($mocksToCreate as $id => $name) {
            $mockApplications[$id] = $this->getMockApplication($id, $name);
        }

        // add a new application to the system configuration
        $application = new \stdClass();
        $application->name = $mockApplications[4]->getName();
        $this->service->create($application);

        foreach ($systemConfiguration->getChilds(ApplicationService::XPATH_CONTAINERS) as $containerConfiguration) {
            foreach ($containerConfiguration->getChilds(ApplicationService::XPATH_APPLICATION) as $applicationConfiguration) {

                error_log(var_export($applicationConfiguration, true));

                $this->assertEquals($mockApplications[$applicationConfiguration->getId()], $applicationConfiguration);
            }
        }
    }

    public function getMockApplication($id, $name)
    {
        $applicationConfiguration = new Configuration();
        $applicationConfiguration->setNodeName('application');
        $applicationConfiguration->setData('name', $name);
        $applicationConfiguration->setData('id', $id);
        return $applicationConfiguration;
    }
}