<?php

/**
 * TechDivision\ApplicationServer\Api\Node\AppserverNodeTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer\Api\Node;

use TechDivision\ApplicationServer\AbstractTest;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2010 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AppserverNodeTest extends AbstractTest
{

    /**
     * The abstract service instance to test.
     *
     * @var TechDivision\ApplicationServer\Api\Node\AppserverNodeTest
     */
    protected $appserverNode;

    /**
     * Initializes the service instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->appserverNode = $this->getAppserverNode();
    }

    /**
     * Test if the base directory getter.
     *
     * @return void
     */
    public function testGetBaseDirectory()
    {
        $baseDirectory = $this->appserverNode->getBaseDirectory();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode', $baseDirectory);
    }

    /**
     * Test's the export as configuration method.
     *
     * @return void
     */
    public function testExportToConfiguration()
    {
        $configuration = $this->appserverNode->exportToConfiguration();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Configuration', $configuration);
    }

    /**
     * Test if the initial context has been successfully initialized.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $initialContext = $this->appserverNode->getInitialContext();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Api\Node\InitialContextNode', $initialContext);
    }

    /**
     * Test if the system logger has been successfully initialized.
     *
     * @return void
     */
    public function testGetSystemLogger()
    {
        $systemLogger = $this->appserverNode->getSystemLogger();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Api\Node\SystemLoggerNode', $systemLogger);
    }

    /**
     * Test if the containers has been successfully initialized.
     *
     * @return void
     */
    public function testGetContainers()
    {
        $containers = $this->appserverNode->getContainers();
        $this->assertCount(3, $containers);
    }

    /**
     * Test if the apps has been successfully initialized.
     *
     * @return void
     */
    public function testGetApps()
    {
        $apps = $this->appserverNode->getApps();
        $this->assertCount(4, $apps);
    }

    /**
     * Test if the datasources has been successfully initialized.
     *
     * @return void
     */
    public function testGetDatasources()
    {
        $datasources = $this->appserverNode->getDatasources();
        $this->assertCount(0, $datasources);
    }

    /**
     * Test if it is possible to attach a new app.
     *
     * @return void
     */
    public function testAttachApp()
    {
        $appNode = new AppNode();
        $appNode->setNodeName('application');
        $appNode->setName('someApp');
        $appNode->setWebappPath('/someApp');

        $this->appserverNode->attachApp($appNode);
        $this->assertCount(5, $this->appserverNode->getApps());
    }

    /**
     * Test if it is possible to attach a new datasource.
     *
     * @return void
     */
    public function testAttachDatasource()
    {
        $datasourceNode = new DatasourceNode();
        $datasourceNode->setNodeName('datasource');

        $this->appserverNode->attachDatasource($datasourceNode);
        $this->assertCount(1, $this->appserverNode->getDatasources());
    }
}