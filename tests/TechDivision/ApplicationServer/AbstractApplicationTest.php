<?php

/**
 * TechDivision\ApplicationServer\AbstractApplicationTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Mock\MockApplication;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;
use TechDivision\ApplicationServer\Api\Node\ContainerNode;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractApplicationTest extends AbstractTest
{

    /**
     * The application instance to test.
     *
     * @var \TechDivision\ApplicationServer\MockApplication
     */
    protected $application;

    /**
     * A dummy application name for testing purposes.
     *
     * @var string
     */
    protected $applicationName = 'api';

    /**
     * A dummy application ID for testing purposes;
     *
     * @var integer
     */
    protected $applicationId = 1;

    /**
     * Initializes the application instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->application = new MockApplication($this->getMockInitialContext(), $this->getContainerNode(), $this->getApplicationName());
    }

    /**
     * Checks if the application returns the correct application name.
     *
     * @return void
     */
    public function testGetName()
    {
        $this->assertEquals($this->getApplicationName(), $this->application->getName());
    }

    /**
     * Checks if the application returns the correct container base path.
     *
     * @return void
     */
    public function testGetAppBase()
    {
        $this->assertEquals('/webapps', $this->application->getAppBase());
    }

    /**
     * Checks if the new instance method works correctly.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\ApplicationServer\Configuration';
        $this->assertInstanceOf($className, $this->application->newInstance($className));
    }

    /**
     * Returns a dummy application name.
     *
     * @return string A dummy application name
     */
    public function getApplicationName()
    {
        return $this->applicationName;
    }

    /**
     * Returns a dummy application ID.
     *
     * @return integer A dummy application ID
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }
}