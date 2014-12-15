<?php

/**
 * AppserverIo\Appserver\Naming\EnterpriseBeanResourceIdentifierTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * PHP version 5
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */

namespace AppserverIo\Appserver\Naming;

/**
 * This is the test for the EnterpriseBeanResourceIdentifier class.
 *
 * @category Library
 * @package    Appserver
 * @subpackage Application
 * @author Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link https://github.com/appserver-io/appserver
 */
class EnterpriseBeanResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The resource identifier instance we want to test.
     *
     * @var \AppserverIo\Appserver\Naming\EnterpriseBeanResourceIdentifier
     */
    protected $resourceIdentifier;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->resourceIdentifier = new EnterpriseBeanResourceIdentifier();
    }

    /**
     * Dummy test implementation.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(true);
    }
}
