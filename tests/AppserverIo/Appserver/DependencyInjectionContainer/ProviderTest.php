<?php

/**
 * AppserverIo\Appserver\DependencyInjectionContainer\ProviderTest
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
 * @link       http://www.appserver.io
 */

namespace AppserverIo\Appserver\DependencyInjectionContainer;

/**
 * Test implementation for the DI provider.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 * @link       http://www.appserver.io
 */
class ProviderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The provider instance we want to test.
     *
     * @var \AppserverIo\Appserver\DependencyInjectionContainer\Provider
     */
    protected $provider;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->provider = new Provider();
    }

    /**
     * A dummy test implementation.
     *
     * @return void
     */
    public function testDummy()
    {
        $this->assertTrue(true);
    }
}
