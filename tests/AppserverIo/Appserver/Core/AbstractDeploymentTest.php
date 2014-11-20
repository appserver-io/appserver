<?php

/**
 * AppserverIo\Appserver\Core\AbstractDeploymentTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */
namespace AppserverIo\Appserver\Core;

use AppserverIo\Appserver\Core\Mock\MockDeployment;

/**
 *
 * @package AppserverIo\Appserver\Core
 * @copyright Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license http://opensource.org/licenses/osl-3.0.php
 *          Open Software License (OSL 3.0)
 * @author Tim Wagner <tw@techdivision.com>
 */
class AbstractDeploymentTest extends AbstractTest
{

    /**
     * The deployment instance to test.
     *
     * @var \AppserverIo\Appserver\Core\MockDeployment
     */
    protected $deployment;

    /**
     * Initializes the container instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->deployment = new MockDeployment($this->getMockInitialContext(), $this->getContainerNode(), $this->getDeploymentNode());
    }

    /**
     * Checks if the new instance method works as expected.
     *
     * @return void
     */
    public function testNewInstance()
    {
        $className = 'TechDivision\Configuration\Configuration';
        $this->assertInstanceOf($className, $this->deployment->newInstance($className));
    }
}