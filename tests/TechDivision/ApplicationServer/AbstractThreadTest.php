<?php

/**
 * TechDivision\ApplicationServer\AbstractThreadTest
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\MockThread;
use TechDivision\ApplicationServer\Configuration;
use TechDivision\ApplicationServer\InitialContext;

/**
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Tim Wagner <tw@techdivision.com>
 */
class AbstractThreadTest extends \PHPUnit_Framework_TestCase {
    
	/**
	 * Test's if the thread's constructor works as expected.
	 * 
	 * @return void
	 */
	public function testConstructor()
	{
	    $thread = new MockThread($someInstance = new \stdClass());
	    $this->assertEquals($someInstance, $thread->getSomeInstance());
	}
	
	/**
	 * Test's if the threads main method has been invoked.
	 * 
	 * @return void
	 */
	public function testMain()
	{
	    $thread = new MockThread(new \stdClass());
	    $thread->run();
	    $this->assertTrue($thread->hasExcecuted());
	}
}