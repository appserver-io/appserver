<?php

/**
 * TechDivision\ApplicationServer\AbstractContainer
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 */

namespace TechDivision\ApplicationServer;

use TechDivision\ApplicationServer\SplClassLoader;

/**
 * An abstraction layer for Threads
 *
 * The major change vs. a normal Thread is that you have to use a main() method instead of a run() method.
 * You can use init() method to get and process args passed in constructor.
 *
 * @package     TechDivision\ApplicationServer
 * @copyright  	Copyright (c) 2013 <info@techdivision.com> - TechDivision GmbH
 * @license    	http://opensource.org/licenses/osl-3.0.php
 *              Open Software License (OSL 3.0)
 * @author      Johann Zelger <jz@techdivision.com>
 */
abstract class AbstractThread extends \Thread
{

    /**
     * Constructor sets initialContext object per default and calls
     * init function to pass other args.
     *
     * @return void
     */
    public function __construct()
    {
        // get all params
        $functionArgs = func_get_args();
        // send call init function and pass all args
        call_user_func_array(array("static", "init"), $functionArgs);
    }

    /**
     * @see \Thread::run()
     */
    public function run()
    {
        $this->main();
    }

    /**
     * The thread methods
     *
     * @return mixed
     */
    abstract public function main();
}
