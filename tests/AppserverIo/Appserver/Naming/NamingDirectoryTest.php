<?php

/**
 * AppserverIo\Appserver\Naming\NamingDirectoryTest
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
 * This is the test for the NamingDirectory class.
 *
 * @category   Server
 * @package    Appserver
 * @subpackage Application
 * @author     Tim Wagner <tw@appserver.io>
 * @copyright  2014 TechDivision GmbH <info@appserver.io>
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link       https://github.com/appserver-io/appserver
 */
class NamingDirectoryTest extends \PHPUnit_Framework_TestCase
{

    /**
     * The naming directory instance we want to test.
     *
     * @var \AppserverIo\Appserver\Naming\NamingDirectory
     */
    protected $namingDirectory;

    /**
     * Initialize the instance to test.
     *
     * @return void
     */
    public function setUp()
    {
        $this->namingDirectory = new NamingDirectory();
        $this->namingDirectory->setScheme('php');
    }

    /**
     * Tests a simple bind to a directory.
     *
     * @return void
     */
    public function testSimpleBind()
    {
        $this->namingDirectory->bind($name = 'test', $value = 'testValue');
        $this->assertSame($this->namingDirectory->search($name), $value);
    }

    /**
     * Test if the subdirectory creatin works.
     *
     * @return void
     */
    public function testCreateSubdirectory()
    {

        // create a subdirectory and bind a value
        $subdirectory = $this->namingDirectory->createSubdirectory('testname');
        $subdirectory->bind('atest', 'testValue');

        // check if the subdirectory is available and the value is bound
        $this->assertInstanceOf('AppserverIo\Psr\Naming\NamingDirectoryInterface', $subdirectory);
        $this->assertSame('testValue', $subdirectory->search('atest'));
    }

    /**
     * Test the descending recursive search on a directory tree
     * with a one level structure.
     *
     * @return void
     */
    public function testOneLevelDescendingRecursiveSearch()
    {

        // create the one level tree
        $level1 = $this->namingDirectory->createSubdirectory('level1');

        // bind a value and search recursive
        $this->namingDirectory->bind($name = 'php:level1/test', $value = 'testValue');
        $this->assertSame($this->namingDirectory->search($name), $value);
    }

    /**
     * Test the descending recursive search on a directory tree
     * with a two level structure.
     *
     * @return void
     */
    public function testTwoLevelDescendingRecursiveSearch()
    {

        // create the two level tree
        $level1 = $this->namingDirectory->createSubdirectory('level1');
        $level2 = $level1->createSubdirectory('level2');

        // bind a value and search recursive
        $this->namingDirectory->bind($name = 'php:level1/level2/test', $value = 'testValue');
        $this->assertSame($this->namingDirectory->search($name), $value);
    }

    /**
     * Test the descending recursive search on a directory tree
     * with a three level structure.
     *
     * @return void
     */
    public function testThreeLevelDescendingRecursiveSearch()
    {

        // create the three level tree
        $level1 = $this->namingDirectory->createSubdirectory('level1');
        $level2 = $level1->createSubdirectory('level2');
        $level3 = $level2->createSubdirectory('level3');

        // bind a value and search recursive
        $this->namingDirectory->bind($name = 'php:level1/level2/level3/test', $value = 'testValue');
        $this->assertSame($this->namingDirectory->search($name), $value);
    }

    /**
     * Test if the search for a subdirectory works.
     *
     * @return void
     */
    public function testSearchForASubdirectory()
    {

        // create a three level directory
        $level1 = $this->namingDirectory->createSubdirectory('level1');
        $level2 = $level1->createSubdirectory('level2');
        $level3 = $level2->createSubdirectory('level3');

        // search for the last created subdirectory
        $this->assertInstanceOf('AppserverIo\Psr\Naming\NamingDirectoryInterface', $this->namingDirectory->search('php:level1/level2/level3'));
    }
}
