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
        $this->appserverNode = new AppserverNode();
        $this->appserverNode->initFromConfiguration($this->getMockSystemConfiguration());
    }

    /**
     * Test if the base directory getter.
     *
     * @return void
     */
    public function testGetBaseDirectory()
    {
        // error_log(var_export($this->appserverNode, true));
        $baseDirectory = $this->appserverNode->getBaseDirectory();
        $this->assertInstanceOf('TechDivision\ApplicationServer\Api\Node\BaseDirectoryNode', $baseDirectory);
    }

    /**
     * Test if the base directory getter.
     *
     * @return void
     */
    public function testExportToConfiguration()
    {
        // error_log(var_export($this->appserverNode->exportToConfiguration(), true));
        $configuration = $this->appserverNode->exportToConfiguration();
        $configuration->setSchemaFile('_files/appserver.xsd');
        $configuration->save('/tmp/appserver.xml');
        $configuration->validate();
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
}