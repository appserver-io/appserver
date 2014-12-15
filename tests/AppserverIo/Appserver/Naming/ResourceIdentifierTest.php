<?php

/**
 * AppserverIo\Appserver\Naming\ResourceIdentifierTest
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
 * This is the test for the ResourceIdentifier class.
 *
 * @category Library
 * @package    Appserver
 * @subpackage Application
 * @author Tim Wagner <tw@techdivision.com>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link https://github.com/appserver-io/appserver
 */
class ResourceIdentifierTest extends \PHPUnit_Framework_TestCase
{

    /**
     * A simple test URL to check if initialization works properly.
     *
     * @var string
     */
    const TEST_URL = 'php://user:password@127.0.0.1:9080/example/index.pc/TechDivision/Example/Services/UserProcessor?SESSID=sadf8dafs879sdfsad';

    /**
     * The resource identifier instance we want to test.
     *
     * @var \AppserverIo\Appserver\Naming\ResourceIdentifier
     */
    protected $resourceIdentifier;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->resourceIdentifier = new ResourceIdentifier();
    }

    /**
     * Checks if the resource identifier will be initialized
     * propertly from the data of a passed URL.
     *
     * @return void
     */
    public function testPopulateFromUrl()
    {

        // populate the identifier with the data of the passed URL
        $this->resourceIdentifier->populateFromUrl(ResourceIdentifierTest::TEST_URL);

        // check the data from the resource identifier
        $this->assertSame($this->resourceIdentifier->getScheme(), 'php');
        $this->assertSame($this->resourceIdentifier->getUser(), 'user');
        $this->assertSame($this->resourceIdentifier->getPass(), 'password');
        $this->assertSame($this->resourceIdentifier->getHost(), '127.0.0.1');
        $this->assertSame($this->resourceIdentifier->getPort(), 9080);
        $this->assertSame($this->resourceIdentifier->getPath(), '/example/index.pc/TechDivision/Example/Services/UserProcessor');
        $this->assertSame($this->resourceIdentifier->getQuery(), 'SESSID=sadf8dafs879sdfsad');
        $this->assertSame($this->resourceIdentifier->getFilename(), '/example/index.pc');
        $this->assertSame($this->resourceIdentifier->getContextName(), 'example');
        $this->assertSame($this->resourceIdentifier->getPathInfo(), '/TechDivision/Example/Services/UserProcessor');
    }
}
