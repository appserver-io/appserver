<?php

/**
 * AppserverIo\Appserver\Naming\InitialContextTest
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

use AppserverIo\Properties\Properties;

/**
 * This is the test for the InitialContext class.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */
class InitialContextTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The default index file name => for the persistence container module.
     *
     * @var string
     */
    const INDEX_FILE = 'index.pc';

    /**
     * The default class name for testing purposes.
     *
     * @var string
     */
    const CLASS_NAME = 'UserProcessor';

    /**
     * The default context name for testing purposes.
     *
     * @var string
     */
    const CONTEXT_NAME = 'example';

    /**
     * The default transport for remote interface handling.
     *
     * @var string
     */
    const TRANSPORT = 'http';

    /**
     * The default context name with a minus for testing purposes.
     *
     * @var string
     */
    const CONTEXT_NAME_WITH_MINUS = 'example-test';

    /**
     * The class name only.
     *
     * @var string
     */
    const IDENTIFIER_CLASS_NAME = 'UserProcessor';

    /**
     * The class name only, local interface.
     *
     * @var string
     */
    const IDENTIFIER_CLASS_NAME_LOCAL = 'UserProcessor/local';

    /**
     * The class name only, local interface.
     *
     * @var string
     */
    const IDENTIFIER_CLASS_NAME_REMOTE = 'UserProcessor/remote';

    /**
     * Specifys application, remote interface.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL_REMOTE = 'php:global/example/UserProcessor/remote';

    /**
     * Specifys application, remote interface with a minus in application name.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL_REMOTE_WITH_MINUS = 'php:global/example-test/UserProcessor/remote';

    /**
     * Specifys application, local interface.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL_LOCAL = 'php:global/example/UserProcessor/local';

    /**
     * Specifys application, if only one interface has been defined.
     *
     * @var string
     */
    const IDENTIFIER_GLOBAL = 'php:global/example/UserProcessor';

    /**
     * Actual application, remote interface.
     *
     * @var string
     */
    const IDENTIFIER_APP_REMOTE = 'php:app/UserProcessor/remote';

    /**
     * Actual application, local interface.
     *
     * @var string
     */
    const IDENTIFIER_APP_LOCAL = 'php:app/UserProcessor/local';

    /**
     * Actual application, if only one interface has been defined.
     *
     * @var string
     */
    const IDENTIFIER_APP = 'php:app/UserProcessor';

    /**
     * The initial context instance we want to test.
     *
     * @var \AppserverIo\Appserver\Naming\InitialContext
     */
    protected $initialContext;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->initialContext = new InitialContext();
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and remote interface.
     *
     * @return void
     */
    public function testWithDefaultPropertiesPopulatedWithGlobalScopeAndRemoteInterface()
    {

        // create default properties
        $defaultProperties = new Properties();
        $defaultProperties->setProperty('indexFile', InitialContextTest::INDEX_FILE);
        $defaultProperties->setProperty('contextName', InitialContextTest::CONTEXT_NAME);
        $defaultProperties->setProperty('transport', InitialContextTest::TRANSPORT);

        // create the initial context initialized with the default properties
        $this->initialContext->injectProperties($defaultProperties);

        // populate the identifier with the data of the passed URL
        $resourceIdentifier =  $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_GLOBAL_REMOTE);

        // check the data from the resource identifier
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(InitialContextTest::CONTEXT_NAME, $resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::TRANSPORT, $resourceIdentifier->getTransport());
        $this->assertSame(EnterpriseBeanResourceIdentifier::REMOTE_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with a class name only.
     *
     * @return void
     */
    public function testPopulateWithClassNameOnly()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_CLASS_NAME);

        // check the data from the resource identifier
        $this->assertNull($resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with a class name and a local interface.
     *
     * @return void
     */
    public function testPopulateWithClassNameWithLocalInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_CLASS_NAME_LOCAL);

        // check the data from the resource identifier
        $this->assertNull($resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with a class name and a remote interface.
     *
     * @return void
     */
    public function testPopulateWithClassNameWithRemoteInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_CLASS_NAME_REMOTE);

        // check the data from the resource identifier
        $this->assertNull($resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::REMOTE_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and remote interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithRemoteInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_GLOBAL_REMOTE);

        // check the data from the resource identifier
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(InitialContextTest::CONTEXT_NAME, $resourceIdentifier->getContextName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::REMOTE_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and remote interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithRemoteInterfaceAndMinusInApplicationName()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_GLOBAL_REMOTE_WITH_MINUS);

        // check the data from the resource identifier
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(InitialContextTest::CONTEXT_NAME_WITH_MINUS, $resourceIdentifier->getContextName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::REMOTE_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope and local interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithLocalInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_GLOBAL_LOCAL);

        // check the data from the resource identifier
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(InitialContextTest::CONTEXT_NAME, $resourceIdentifier->getContextName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with global scope without interface.
     *
     * @return void
     */
    public function testPopulateGlobalScopeWithoutInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_GLOBAL);

        // check the data from the resource identifier
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(InitialContextTest::CONTEXT_NAME, $resourceIdentifier->getContextName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with app scope and remote interface.
     *
     * @return void
     */
    public function testPopulateAppScopeWithRemoteInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_APP_REMOTE);

        // check the data from the resource identifier
        $this->assertNull($resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::REMOTE_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with app scope and local interface.
     *
     * @return void
     */
    public function testPopulateAppScopeWithLocalInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_APP_LOCAL);

        // check the data from the resource identifier
        $this->assertNull($resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE, $resourceIdentifier->getInterface());
    }

    /**
     * Checks if the resource identifier will be initialized propertly
     * from a URL with app scope without interface.
     *
     * @return void
     */
    public function testPopulateAppScopeWithoutInterface()
    {

        // populate the identifier with the data of the passed URL
        $resourceIdentifier = $this->initialContext->prepareResourceIdentifier(InitialContextTest::IDENTIFIER_APP);

        // check the data from the resource identifier
        $this->assertNull($resourceIdentifier->getContextName());
        $this->assertSame(InitialContextTest::INDEX_FILE, $resourceIdentifier->getIndexFile());
        $this->assertSame(InitialContextTest::CLASS_NAME, $resourceIdentifier->getClassName());
        $this->assertSame(EnterpriseBeanResourceIdentifier::LOCAL_INTERFACE, $resourceIdentifier->getInterface());
    }
}
