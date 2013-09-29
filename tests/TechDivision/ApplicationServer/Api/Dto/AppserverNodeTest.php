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
        $this->appserverNode = new AppserverNode($this->getMockSystemConfiguration());
    }

    /**
     * Test if the initial context has successfully been initialized.
     *
     * @return void
     */
    public function testConstructor()
    {
        error_log(var_export($this->appserverNode, true));
        $this->assertTrue(true);
    }
}