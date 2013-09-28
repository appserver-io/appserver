<?php

/**
 * TechDivision\ApplicationServer\AbstractContextThreadTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\Mock\MockContextThread;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 *
 * @package TechDivision\ApplicationServer
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractContextThreadTest extends AbstractTest
{

    /**
     * The mock context thread to test.
     *
     * @var \TechDivision\ApplicationServer\MockAbstractContextThread
     */
    protected $contextThread;

    /**
     * Initializes the container instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->contextThread = new MockContextThread($this->getMockInitialContext());
    }

    /**
     * Checks if the context thread returns the initial context
     * passed with the constructor.
     *
     * @return void
     */
    public function testGetInitialContext()
    {
        $this->assertInstanceOf('TechDivision\ApplicationServer\InitialContext', $this->contextThread->getInitialContext());
    }

    /**
     * Checks if the new instance method works correctly.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\ApplicationServer\Configuration';
        $this->assertInstanceOf($className, $this->contextThread->newInstance($className));
    }
}